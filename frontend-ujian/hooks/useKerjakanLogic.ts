import { useEffect, useState, useRef } from 'react';
import { AppState, Platform, Alert, BackHandler, Animated, Dimensions } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import api from '../src/api/axiosConfig'; // Sesuaikan dengan path axiosConfig Anda
import * as ScreenCapture from 'expo-screen-capture';
import { Audio } from 'expo-av';
import Pusher from 'pusher-js';

const { width } = Dimensions.get('window');

export function useKerjakanLogic(id: string | string[], token: string | string[], router: any, navigation: any) {
  // STATE SOAL & JAWABAN
  const [soal, setSoal] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedAnswers, setSelectedAnswers] = useState<Record<string, any>>({});
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [studentId, setStudentId] = useState<number | null>(null);
  
  // STATE KEAMANAN & TIMER
  const [timeLeft, setTimeLeft] = useState(0);
  const soundRef = useRef<Audio.Sound | null>(null); 
  const soundIntervalRef = useRef<any>(null); 
  const appState = useRef(AppState.currentState);
  const timerRef = useRef<any>(null);
  const pusherRef = useRef<Pusher | null>(null);
  const violationTriggered = useRef(false);

  // ANIMASI SIDE NAV
  const [sideNavVisible, setSideNavVisible] = useState(false);
  const slideAnim = useRef(new Animated.Value(-width)).current;

  const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
  const ASSET_URL = BASE_URL ? BASE_URL.replace(/\/api$/, '') : ''; 
  const REVERB_KEY = process.env.EXPO_PUBLIC_REVERB_KEY;

  const getWsHost = () => {
    if (!BASE_URL) return 'localhost';
    const matches = BASE_URL.match(/\/\/([^:]+)/);
    return matches ? matches[1] : 'localhost';
  };

  // Setup Audio Mode
  useEffect(() => {
    const setupAudio = async () => {
      try {
        await Audio.setAudioModeAsync({
          allowsRecordingIOS: false,
          playsInSilentModeIOS: true,
          shouldRouteAudioToSpeakerInShowsAudioInterface: true,
          staysActiveInBackground: true,
        });
      } catch (e) {
        console.log("Gagal konfigurasi audio mode:", e);
      }
    };
    setupAudio();
  }, []);

  // FUNGSI ALARM PERINGATAN
  async function playWarningSound() {
    try {
      if (soundRef.current) {
        await soundRef.current.unloadAsync();
        soundRef.current = null;
      }
      if (soundIntervalRef.current) {
        clearInterval(soundIntervalRef.current);
      }

      let playCount = 0;
      const triggerAudioPlay = async () => {
        if (playCount >= 2) {
          if (soundIntervalRef.current) clearInterval(soundIntervalRef.current);
          await stopWarningSound();
          return;
        }
        try {
          const { sound: newSound } = await Audio.Sound.createAsync(
             require('../assets/sounds/alert.mp3'),
             { shouldPlay: true, isLooping: false, volume: 1.0 }
          );
          soundRef.current = newSound;
          playCount++;
        } catch (err) {
          console.log("Gagal memicu play audio internal:", err);
        }
      };

      await triggerAudioPlay();
      soundIntervalRef.current = setInterval(async () => {
        await triggerAudioPlay();
      }, 2500);

    } catch (e) {
      console.log("Gagal memutar suara alarm background:", e);
    }
  }

  async function stopWarningSound() {
    if (soundIntervalRef.current) {
      clearInterval(soundIntervalRef.current);
      soundIntervalRef.current = null;
    }
    if (soundRef.current) {
      try {
        await soundRef.current.stopAsync();
        await soundRef.current.unloadAsync();
        soundRef.current = null;
      } catch (e) {
        console.log("Gagal mematikan suara alarm:", e);
      }
    }
  }

  // EKSEKUSI HUKUMAN DISKUALIFIKASI
  const eksekusiDiskualifikasi = async (alasan: string) => {
    if (violationTriggered.current || isSubmitted) return;
    violationTriggered.current = true;

    console.log(`🛑 Tindakan Kecurangan Terdeteksi: ${alasan}`);
    await playWarningSound();

    const examIdStr = Array.isArray(id) ? id[0] : id;

    try {
      // 🚩 UTAMA: Simpan status pelanggaran di storage lokal sebelum dikeluarkan dari screen ujian
      await AsyncStorage.setItem(`status_pelanggaran_${examIdStr}`, 'terdiskualifikasi');

      await api.post(`/ujian/${examIdStr}/log-pelanggaran`, { 
        type: 'KELUAR_APLIKASI',
        details: `Siswa melanggar mode ketat: ${alasan}`
      });
      if (timerRef.current) clearInterval(timerRef.current);
    } catch (e: any) { 
      console.log("Gagal mengirim data log pelanggaran:", e.message); 
    }

    router.replace('/(auth)/login');

    if (Platform.OS === 'web') {
      window.alert(`DISKUALIFIKASI: ${alasan}`);
    } else {
      Alert.alert("🛑 DISKUALIFIKASI SISTEM", `Anda otomatis dikeluarkan dari ruang ujian karena terdeteksi: ${alasan}`);
    }
  };

  // MONITORING WEBSOCKET REAL-TIME (Aktif saat siswa berada di dalam screen ujian)
  useEffect(() => {
    if (!id || !studentId || !REVERB_KEY) return;

    pusherRef.current = new Pusher(REVERB_KEY, {
      wsHost: getWsHost(),
      wsPort: 8080,
      forceTLS: false,
      disableStats: true,
      enabledTransports: ['ws', 'wss']
    });

    const examIdStr = Array.isArray(id) ? id[0] : id;
    const channel = pusherRef.current.subscribe(`exam-monitoring.${examIdStr}`);
    
    channel.bind('ExamAktivitas', async (data: any) => {
      if (parseInt(data.studentId) === parseInt(studentId.toString())) {
        if (data.actionType === 'RESET_AKSES') {
          await stopWarningSound();
          violationTriggered.current = false; 
          if (timerRef.current) clearInterval(timerRef.current);
          
          try {
            await AsyncStorage.removeItem(`answers_backup_${examIdStr}`);
            await AsyncStorage.removeItem(`status_pelanggaran_${examIdStr}`);
          } catch (err) {
            console.log("Gagal membersihkan data backup lokal saat reset real-time:", err);
          }
          setSelectedAnswers({});
          
          Alert.alert(
            "Akses Dipulihkan", 
            "Guru pengawas telah mereset status login Anda. Sesi pengerjaan baru telah dimulai dari awal.",
            [{ text: "Kembali ke Beranda", onPress: () => router.replace('/(tabs)') }]
          );
        } 
        else if (data.actionType === 'FORCE_SUBMIT') {
          await stopWarningSound();
          if (timerRef.current) clearInterval(timerRef.current);
          setIsSubmitted(true);
          
          Alert.alert(
            "Ujian Selesai Paksa", 
            "Pengerjaan lembar ujian Anda telah diselesaikan dan dikunci oleh pengawas ruangan.",
            [{ text: "Lihat Rekap Ujian", onPress: () => router.replace({ pathname: '/ujian/selesai', params: { id, token } }) }]
          );
        }
      }
    });

    return () => {
      if (pusherRef.current) {
        pusherRef.current.unsubscribe(`exam-monitoring.${examIdStr}`);
        pusherRef.current.disconnect();
      }
    };
  }, [id, studentId]);

  // ENGINE KEAMANAN PERANGKAT
  useEffect(() => {
    fetchData();

    if (Platform.OS !== 'web') {
      ScreenCapture.preventScreenCaptureAsync().catch(() => {});
    }

    const appStateSubscription = AppState.addEventListener('change', async (nextAppState) => {
      if (appState.current === 'active' && nextAppState.match(/inactive|background/)) {
        eksekusiDiskualifikasi("Membuka laci notifikasi atas, menekan Recent Apps, atau mencoba keluar aplikasi.");
      }
      appState.current = nextAppState;
    });

    const blurSubscription = navigation.addListener('blur', async () => {
      if (!isSubmitted) {
        eksekusiDiskualifikasi("Kehilangan fokus layar pengerjaan utama.");
      }
    });

    const backHandler = BackHandler.addEventListener('hardwareBackPress', () => true);

    return () => {
      appStateSubscription.remove();
      blurSubscription();
      backHandler.remove();
      if (timerRef.current) clearInterval(timerRef.current);
      if (Platform.OS !== 'web') {
        ScreenCapture.allowScreenCaptureAsync().catch(() => {});
      }
      stopWarningSound();
    };
  }, [id, isSubmitted]);

  // LOAD DATA & BALANCING REFRESH / RESET ACCESS
  const fetchData = async () => {
    setRefreshing(true);
    const examIdStr = Array.isArray(id) ? id[0] : id;
    try {
      // 🛡️ STRATEGI UTAMA: Deteksi Apakah Siswa Masuk Kembali Setelah Pelanggaran (Guru Menekan Reset Akses)
      const statusPelanggaran = await AsyncStorage.getItem(`status_pelanggaran_${examIdStr}`);
      
      if (statusPelanggaran === 'terdiskualifikasi') {
        // Jika status lokalnya terdiskualifikasi dan ternyata sekarang siswa bisa masuk layar ujian lagi,
        // itu berarti VALID bahwa guru pengawas sudah menekan tombol "Reset Akses" dari web monitoring.
        // AKSI: Hapus total data lokal lama agar pengerjaan direset dari awal!
        await AsyncStorage.removeItem(`answers_backup_${examIdStr}`);
        await AsyncStorage.removeItem(`status_pelanggaran_${examIdStr}`);
        setSelectedAnswers({});
        console.log("🔄 Deteksi Akses Guru Ter-Reset: Lembar jawaban lokal dibersihkan.");
      } else {
        // KONDISI KEDUA: Kendala koneksi internet terputus / siswa melakukan refresh manual di dalam aplikasi.
        // AKSI: Pulihkan lembar jawaban lokal agar data tidak hilang (Antiloss).
        const localBackup = await AsyncStorage.getItem(`answers_backup_${examIdStr}`);
        if (localBackup) {
          setSelectedAnswers(JSON.parse(localBackup));
          console.log("📶 Deteksi Gangguan Koneksi/Refresh: Jawaban dipulihkan dari memori HP.");
        }
      }

      const resSoal = await api.get(`/ujian/${examIdStr}/soal`, { headers: { 'X-Exam-Token': token } });
      setSoal(resSoal.data);
      
      const resUser = await api.get('/user'); 
      setStudentId(resUser.data.id);
      
      const resJadwal = await api.get('/jadwal');
      const currentJadwal = resJadwal.data.data.find((j: any) => j.id.toString() === examIdStr.toString());
      if (currentJadwal) {
        const durasiDetik = Number(currentJadwal.durasi) * 60;
        setTimeLeft(durasiDetik);
        startTimer(durasiDetik);
      }
    } catch (e) { 
      console.log("Koneksi gagal/offline mode aktif. Menggunakan cadangan data lokal.");
    } finally {
      setLoading(false); 
      setRefreshing(false);
    }
  };

  const startTimer = (initialTime: number) => {
    let time = initialTime;
    if (timerRef.current) clearInterval(timerRef.current);
    
    timerRef.current = setInterval(() => {
      time -= 1;
      setTimeLeft(time);
      if (time <= 0) {
        clearInterval(timerRef.current);
        confirmFinish(true); 
      }
    }, 1000);
  };

  const toggleSideNav = (show: boolean) => {
    setSideNavVisible(show);
    Animated.timing(slideAnim, {
      toValue: show ? 0 : -width,
      duration: 300,
      useNativeDriver: true,
    }).start();
  };

  // AUTO SAVE GANDA
  const handleAnswerChange = async (questionId: any, answer: any) => {
    if (isSubmitted || violationTriggered.current) return;
    
    const updatedAnswers = { ...selectedAnswers, [questionId]: answer };
    setSelectedAnswers(updatedAnswers);
    
    const examIdStr = Array.isArray(id) ? id[0] : id;
    try {
      await AsyncStorage.setItem(`answers_backup_${examIdStr}`, JSON.stringify(updatedAnswers));
    } catch (err) {
      console.log("Gagal menyimpan backup lokal:", err);
    }

    try {
      await api.post(`/ujian/${examIdStr}/submit-answer`, { 
        question_id: questionId, 
        answer: answer 
      }, { headers: { 'X-Exam-Token': token } });
    } catch (e) { 
      console.log("Internet Putus! Jawaban aman tersimpan di memori lokal perangkat."); 
    }
  };

  const confirmFinish = async (isAuto = false) => {
    setModalVisible(false);
    await stopWarningSound();
    const examIdStr = Array.isArray(id) ? id[0] : id;
    try {
      await api.post(`/ujian/${examIdStr}/finish`, {}, { headers: { 'X-Exam-Token': token } });
      await AsyncStorage.removeItem(`answers_backup_${examIdStr}`);
      await AsyncStorage.removeItem(`status_pelanggaran_${examIdStr}`);
      setIsSubmitted(true);
      if (timerRef.current) clearInterval(timerRef.current);
      router.replace({ pathname: '/ujian/selesai', params: { id, token } });
    } catch (e) {
      const msg = "Koneksi internet bermasalah. Jawaban Anda tetap tersimpan aman di memori HP, hubungi guru ruangan.";
      Platform.OS === 'web' ? window.alert(msg) : Alert.alert("Gagal Kirim Akhir", msg);
    }
  };

  const checkApakahSemuaSudahDikerjakan = () => {
    if (soal.length === 0) return false;
    return soal.every(s => selectedAnswers[s.id] && selectedAnswers[s.id].toString().trim() !== '');
  };

  return {
    soal,
    loading,
    refreshing,
    selectedAnswers,
    modalVisible,
    setModalVisible,
    currentIndex,
    setCurrentIndex,
    timeLeft,
    sideNavVisible,
    slideAnim,
    ASSET_URL,
    fetchData,
    toggleSideNav,
    handleAnswerChange,
    confirmFinish,
    checkApakahSemuaSudahDikerjakan
  };
}