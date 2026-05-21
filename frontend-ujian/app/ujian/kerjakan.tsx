import React, { useEffect, useState, useRef } from 'react';
import { 
  View, 
  Text, 
  TouchableOpacity, 
  StyleSheet, 
  ActivityIndicator, 
  TextInput, 
  Image, 
  ScrollView, 
  Modal, 
  Alert, 
  StatusBar, 
  SafeAreaView, 
  Dimensions, 
  Animated, 
  Platform, 
  AppState, 
  BackHandler 
} from 'react-native';
import { useLocalSearchParams, useRouter, useNavigation } from 'expo-router'; // Modifikasi: Import useNavigation untuk deteksi blur fokus
import api from '../../src/api/axiosConfig';
import { Ionicons } from '@expo/vector-icons';
import * as ScreenCapture from 'expo-screen-capture';
import { Audio } from 'expo-av';
import Pusher from 'pusher-js';

const { width } = Dimensions.get('window');

export default function KerjakanScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  const navigation = useNavigation(); // Mengaktifkan pengontrol fokus navigasi layar
  
  // STATE SOAL & JAWABAN
  const [soal, setSoal] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedAnswers, setSelectedAnswers] = useState({});
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [studentId, setStudentId] = useState(null);
  
  // STATE KEAMANAN & TIMER
  const [timeLeft, setTimeLeft] = useState(0);
  const soundRef = useRef(null); 
  const appState = useRef(AppState.currentState);
  const timerRef = useRef(null);
  const pusherRef = useRef(null);
  const violationTriggered = useRef(false); // Flag pengunci agar request pelanggaran tidak menembak berkali-kali (anti-spam data)

  // ANIMASI SIDE NAV
  const [sideNavVisible, setSideNavVisible] = useState(false);
  const slideAnim = useRef(new Animated.Value(-width)).current;

  const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
  const REVERB_KEY = process.env.EXPO_PUBLIC_REVERB_KEY;
  const primaryRed = '#c91313';

  const getWsHost = () => {
    if (!BASE_URL) return 'localhost';
    const matches = BASE_URL.match(/\/\/([^:]+)/);
    return matches ? matches[1] : 'localhost';
  };

  // --- 1. FUNGSI ALARM PERINGATAN KELUAR APPLICATION ---
  async function playWarningSound() {
    try {
      if (soundRef.current) {
        await soundRef.current.unloadAsync();
      }
      const { sound: newSound } = await Audio.Sound.createAsync(
         require('../../assets/sounds/alert.mp3'),
         { shouldPlay: true, isLooping: true, volume: 1.0 }
      );
      soundRef.current = newSound;
    } catch (e) {
      console.log("Gagal memutar suara alarm background:", e);
    }
  }

  async function stopWarningSound() {
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

  // --- 2. LOGIKA UTAMA EKSEKUSI HUKUMAN DISKUALIFIKASI ---
  const eksekusiDiskualifikasi = async (alasan) => {
    // Jika flag pengaman sudah true, hentikan proses biar tidak tabrakan
    if (violationTriggered.current || isSubmitted) return;
    violationTriggered.current = true;

    console.log(`🛑 Tindakan Kecurangan Terditeksi: ${alasan}`);
    
    // Mainkan sirine alarm lokal kencang-kencang
    playWarningSound();

    // Kirim bukti kecurangan ke server pengawas secara live
    try {
      await api.post(`/ujian/${id}/log-pelanggaran`, { 
        type: 'KELUAR_APLIKASI',
        details: `Siswa melanggar mode ketat: ${alasan}`
      });
      console.log("Log pelanggaran sukses dilaporkan ke database.");
    } catch (e) { 
      console.log("Gagal kirim log pelanggaran ke backend:", e.message); 
    }

    // Tendang keluar paksa ke login
    router.replace('/(auth)/login');

    if (Platform.OS === 'web') {
      window.alert(`DISKUALIFIKASI: ${alasan}`);
    } else {
      Alert.alert("🛑 DISKUALIFIKASI SISTEM", `Anda otomatis dikeluarkan dari ruang ujian karena terdeteksi: ${alasan}`);
    }
  };

  // --- 3. LOGIKA MONITORING REAL-TIME (WEBSOCKET REVERB) ---
  useEffect(() => {
    if (!id || !studentId || !REVERB_KEY) return;

    pusherRef.current = new Pusher(REVERB_KEY, {
      wsHost: getWsHost(),
      wsPort: 8080,
      forceTLS: false,
      disableStats: true,
      enabledTransports: ['ws', 'wss']
    });

    const channel = pusherRef.current.subscribe(`exam-monitoring.${id}`);
    
    channel.bind('ExamAktivitas', async (data) => {
      console.log("Sinyal Real-time masuk ke HP Siswa:", data);

      if (parseInt(data.studentId) === parseInt(studentId)) {
        if (data.actionType === 'RESET_AKSES') {
          await stopWarningSound();
          violationTriggered.current = false; // Buka kembali kunci flag pelanggaran
          if (timerRef.current) clearInterval(timerRef.current);
          
          Alert.alert(
            "Akses Dipulihkan", 
            "Guru pengawas telah mereset status login Anda.",
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
        pusherRef.current.unsubscribe(`exam-monitoring.${id}`);
        pusherRef.current.disconnect();
      }
    };
  }, [id, studentId]);

  // --- 4. ENGINE KEAMANAN BARU (LAPIS BAJA MULTI-DETEKSI) ---
  useEffect(() => {
    fetchData();

    if (Platform.OS !== 'web') {
      ScreenCapture.preventScreenCaptureAsync().catch(() => {});
    }

    // 🛡️ LAPIS 1: Deteksi via Siklus Hidup OS (AppState)
    const appStateSubscription = AppState.addEventListener('change', async (nextAppState) => {
      // Menangkap status 'background' (klik Home) atau status 'inactive' (tarik status bar / buka menu split view)
      if (appState.current === 'active' && nextAppState.match(/inactive|background/)) {
        eksekusiDiskualifikasi("Membuka laci notifikasi atas, menekan Recent Apps, atau mencoba membelah layar (Split Screen).");
      }
      appState.current = nextAppState;
    });

    // 🛡️ LAPIS 2: Deteksi via Hilangnya Fokus Navigasi (Anti-Split Screen Jari Menyentuh Aplikasi Sebelah)
    const blurSubscription = navigation.addListener('blur', () => {
      eksekusiDiskualifikasi("Kehilangan fokus layar pengerjaan utama (mencoba interaksi dengan aplikasi melayang/jendela disamping).");
    });

    // Mengunci tombol hardware back bawaan HP Android
    const backHandler = BackHandler.addEventListener('hardwareBackPress', () => true);

    return () => {
      appStateSubscription.remove();
      blurSubscription();
      backHandler.remove();
      if (timerRef.current) clearInterval(timerRef.current);
      if (Platform.OS !== 'web') {
        ScreenCapture.allowScreenCaptureAsync().catch(() => {});
      }
      if (soundRef.current) {
        soundRef.current.unloadAsync();
      }
    };
  }, [id]);

  // --- 5. HTTP REQUEST AMBIL DATA SOAL & USER PROFILE ---
  const fetchData = async () => {
    try {
      const resSoal = await api.get(`/ujian/${id}/soal`, { headers: { 'X-Exam-Token': token } });
      setSoal(resSoal.data);
      
      const resUser = await api.get('/user'); 
      setStudentId(resUser.data.id);
      
      const resJadwal = await api.get('/jadwal');
      const currentJadwal = resJadwal.data.data.find((j) => j.id.toString() === id.toString());
      if (currentJadwal) {
        const durasiDetik = Number(currentJadwal.durasi) * 60;
        setTimeLeft(durasiDetik);
        startTimer(durasiDetik);
      }
    } catch (e) { 
      const errorMsg = e.response?.data?.message || "Token ujian salah atau sesi pengerjaan Anda tidak valid.";
      if (Platform.OS === 'web') {
        window.alert("AKSES DITOLAK: " + errorMsg);
        router.replace('/(tabs)'); 
      } else {
        Alert.alert("Akses Ditolak", errorMsg, [{ text: "Kembali", onPress: () => router.replace('/(tabs)') }]);
      }
    } finally { 
      setLoading(false); 
    }
  };

  const startTimer = (initialTime) => {
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

  const toggleSideNav = (show) => {
    setSideNavVisible(show);
    Animated.timing(slideAnim, {
      toValue: show ? 0 : -width,
      duration: 300,
      useNativeDriver: true,
    }).start();
  };

  const handleAnswerChange = async (questionId, answer) => {
    if (isSubmitted || violationTriggered.current) return;
    setSelectedAnswers((prev) => ({ ...prev, [questionId]: answer }));
    try {
      await api.post(`/ujian/${id}/submit-answer`, { 
        question_id: questionId, 
        answer: answer 
      }, { headers: { 'X-Exam-Token': token } });
    } catch (e) { 
      console.log("Gagal auto-save jawaban ke server"); 
    }
  };

  const confirmFinish = async (isAuto = false) => {
    setModalVisible(false);
    try {
      await api.post(`/ujian/${id}/finish`, {}, { headers: { 'X-Exam-Token': token } });
      setIsSubmitted(true);
      if (timerRef.current) clearInterval(timerRef.current);
      router.replace({ pathname: '/ujian/selesai', params: { id, token } });
    } catch (e) {
      const msg = "Koneksi internet bermasalah saat mengirimkan final berkas ujian.";
      Platform.OS === 'web' ? window.alert(msg) : Alert.alert("Gagal Kirim", msg);
    }
  };

  const checkApakahSemuaSudahDikerjakan = () => {
    if (soal.length === 0) return false;
    return soal.every(s => selectedAnswers[s.id] && selectedAnswers[s.id].toString().trim() !== '');
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={primaryRed} />
      </View>
    );
  }

  const currentItem = soal[currentIndex];
  const isNomorTerakhir = currentIndex === soal.length - 1;
  const semuaSoalTerisi = checkApakahSemuaSudahDikerjakan();

  return (
    <SafeAreaView style={styles.container}>
      {/* 🛡️ LAPIS 3: Agresif menyembunyikan status bar agar area penarikan notifikasi terkunci/hilang */}
      <StatusBar hidden={true} />
      
      {/* HEADER BAR */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => toggleSideNav(true)} style={styles.menuIcon}>
           <Ionicons name="grid" size={24} color="#fff" />
        </TouchableOpacity>
        
        <View style={styles.schoolHeader}>
          <Image source={require('../../assets/images/logosekolah.png')} style={styles.logoSekolah} />
          <View>
            <Text style={styles.schoolName}>SMKN 1 BINONG</Text>
            <Text style={styles.appTagline}>Sebstar Exam Browser</Text>
          </View>
        </View>

        <View style={styles.timerContainer}>
          <Text style={styles.timerText}>
            {Math.floor(timeLeft / 60)}:{String(timeLeft % 60).padStart(2, '0')}
          </Text>
        </View>
      </View>

      {/* LEMBAR AREA SOAL */}
      <ScrollView contentContainerStyle={styles.scrollContainer} showsVerticalScrollIndicator={false}>
        <View style={styles.card}>
          <Text style={styles.progressLabel}>Pertanyaan No. {currentIndex + 1}</Text>
          
          {currentItem?.question_image && (
            <Image 
              source={{ uri: `${BASE_URL}/storage/${currentItem.question_image}` }} 
              style={styles.image} 
              resizeMode="contain" 
            />
          )}

          <Text style={styles.questionText} selectable={false}>
            {currentItem?.question_text}
          </Text>
          
          {currentItem?.type?.toLowerCase() === 'pg' ? (
            <View style={styles.optionsWrapper}>
              {['a', 'b', 'c', 'd', 'e'].map((opt) => currentItem[`option_${opt}`] ? (
                <TouchableOpacity 
                  key={opt} 
                  style={[styles.optionBtn, selectedAnswers[currentItem.id] === opt && styles.selectedBtn]} 
                  onPress={() => handleAnswerChange(currentItem.id, opt)}
                >
                  <View style={[styles.optCircle, selectedAnswers[currentItem.id] === opt && styles.selectedCircle]}>
                    <Text style={[styles.optLetter, selectedAnswers[currentItem.id] === opt && {color: '#fff'}]}>
                      {opt.toUpperCase()}
                    </Text>
                  </View>
                  <Text style={[styles.optionText, selectedAnswers[currentItem.id] === opt && styles.selectedText]}>
                    {currentItem[`option_${opt}`]}
                  </Text>
                </TouchableOpacity>
              ) : null)}
            </View>
          ) : (
            <TextInput 
              style={styles.essayInput} 
              multiline 
              placeholder="Tulis jawaban essay disini..."
              value={selectedAnswers[currentItem.id] || ''} 
              onChangeText={(text) => handleAnswerChange(currentItem.id, text)} 
            />
          )}
        </View>
      </ScrollView>

      {/* FOOTER NAVIGATION BUTTONS */}
      <View style={styles.footer}>
        <TouchableOpacity 
          style={[styles.btnNav, currentIndex === 0 && {opacity: 0.3}]} 
          disabled={currentIndex === 0} 
          onPress={() => setCurrentIndex(prev => prev - 1)}
        >
          <Ionicons name="chevron-back" size={20} color="#475569" />
          <Text style={styles.btnNavText}>Kembali</Text>
        </TouchableOpacity>
        
        {isNomorTerakhir && semuaSoalTerisi ? (
          <TouchableOpacity style={[styles.btnFinish, {backgroundColor: primaryRed}]} onPress={() => setModalVisible(true)}>
            <Text style={styles.btnFinishText}>SELESAI</Text>
          </TouchableOpacity>
        ) : (
          <View style={{ flex: 1 }} />
        )}
        
        <TouchableOpacity 
          style={[styles.btnNav, isNomorTerakhir && {opacity: 0.3}]} 
          disabled={isNomorTerakhir} 
          onPress={() => setCurrentIndex(prev => prev + 1)}
        >
          <Text style={styles.btnNavText}>Lanjut</Text>
          <Ionicons name="chevron-forward" size={20} color="#475569" />
        </TouchableOpacity>
      </View>

      {/* DRAWER MATRIX NOMOR SOAL */}
      {sideNavVisible && <TouchableOpacity style={styles.drawerOverlay} activeOpacity={1} onPress={() => toggleSideNav(false)} />}
      <Animated.View style={[styles.drawer, { transform: [{ translateX: slideAnim }] }]}>
        <View style={styles.drawerHeader}><Text style={styles.drawerTitle}>Daftar Navigasi Soal</Text></View>
        <ScrollView contentContainerStyle={styles.drawerGrid}>
          {soal.map((item, index) => {
            const isAnswered = selectedAnswers[item.id] && selectedAnswers[item.id].toString().trim() !== '';
            return (
              <TouchableOpacity 
                key={index} 
                style={[styles.gridItem, isAnswered ? {backgroundColor: primaryRed} : {backgroundColor: '#f1f5f9'}, currentIndex === index && {borderWidth: 2, borderColor: '#1e293b'}]} 
                onPress={() => { setCurrentIndex(index); toggleSideNav(false); }}
              >
                <Text style={[styles.gridText, isAnswered && {color: '#fff'}]}>{index + 1}</Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </Animated.View>

      {/* POPUP MODAL KONFIRMASI SELESAI */}
      <Modal visible={modalVisible} transparent={true} animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Akhiri Sesi Ujian?</Text>
            <View style={styles.modalBtnRow}>
              <TouchableOpacity style={styles.modalCancel} onPress={() => setModalVisible(false)}>
                <Text style={{fontWeight: '700', color: '#64748b'}}>Batal</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalConfirm, {backgroundColor: primaryRed}]} onPress={() => confirmFinish(false)}>
                <Text style={{color: '#fff', fontWeight: 'bold'}}>Ya, Kumpulkan</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { backgroundColor: '#c91313', height: 75, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 15, paddingTop: Platform.OS === 'android' ? 10 : 0 },
  menuIcon: { width: 35 },
  schoolHeader: { flexDirection: 'row', alignItems: 'center', gap: 10, flex: 1 },
  logoSekolah: { width: 35, height: 35, borderRadius: 5, backgroundColor: '#fff' },
  schoolName: { color: '#fff', fontSize: 14, fontWeight: '800' },
  appTagline: { color: '#fecaca', fontSize: 10 },
  timerContainer: { backgroundColor: 'rgba(0,0,0,0.2)', paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8 },
  timerText: { color: '#fff', fontSize: 16, fontWeight: '900' },
  scrollContainer: { padding: 20, paddingBottom: 100 },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 20, elevation: 3, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 3 },
  progressLabel: { color: '#64748b', fontSize: 12, fontWeight: '800', marginBottom: 15 },
  image: { width: '100%', height: 200, marginBottom: 20, borderRadius: 10 },
  questionText: { fontSize: 16, fontWeight: '700', lineHeight: 24, color: '#1e293b', marginBottom: 20 },
  optionsWrapper: { gap: 10 },
  optionBtn: { flexDirection: 'row', alignItems: 'center', padding: 14, borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 15, gap: 12 },
  selectedBtn: { backgroundColor: '#fff1f2', borderColor: '#c91313' },
  optCircle: { width: 30, height: 30, borderRadius: 15, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center' },
  selectedCircle: { backgroundColor: '#c91313' },
  optLetter: { fontSize: 12, fontWeight: '800', color: '#475569' },
  optionText: { flex: 1, fontSize: 14, color: '#334155' },
  selectedText: { color: '#c91313', fontWeight: '700' },
  essayInput: { backgroundColor: '#f8fafc', borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 15, padding: 15, height: 160, textAlignVertical: 'top', fontSize: 14 },
  footer: { position: 'absolute', bottom: 0, width: '100%', height: 80, backgroundColor: '#fff', borderTopWidth: 1, borderColor: '#f1f5f9', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20 },
  btnNav: { flexDirection: 'row', alignItems: 'center', gap: 5, padding: 10 },
  btnNavText: { fontWeight: '700', fontSize: 13, color: '#475569' },
  btnFinish: { paddingHorizontal: 25, paddingVertical: 12, borderRadius: 12, minWidth: 100, alignItems: 'center' },
  btnFinishText: { color: '#fff', fontWeight: '800', fontSize: 13 },
  drawerOverlay: { position: 'absolute', width: '100%', height: '100%', backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 1000 },
  drawer: { position: 'absolute', width: width * 0.75, height: '100%', backgroundColor: '#fff', zIndex: 1001, padding: 25, paddingTop: 50 },
  drawerHeader: { marginBottom: 30 },
  drawerTitle: { fontSize: 20, fontWeight: '800', color: '#1e293b' },
  drawerGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12 },
  gridItem: { width: 45, height: 45, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  gridText: { fontWeight: '800', color: '#475569' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center' },
  modalContent: { width: '80%', backgroundColor: '#fff', padding: 25, borderRadius: 20, alignItems: 'center', elevation: 10 },
  modalTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b', marginBottom: 20 },
  modalBtnRow: { flexDirection: 'row', gap: 10 },
  modalCancel: { flex: 1, padding: 12, alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10 },
  modalConfirm: { flex: 1, padding: 12, alignItems: 'center', borderRadius: 10 }
});