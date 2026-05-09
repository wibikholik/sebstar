import { useEffect, useState, useRef } from 'react';
import { 
  View, Text, TouchableOpacity, StyleSheet, ActivityIndicator, 
  TextInput, Image, ScrollView, Modal, Alert, StatusBar, 
  SafeAreaView, Dimensions, Animated, Platform 
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';
import { Ionicons } from '@expo/vector-icons';

const { width } = Dimensions.get('window');

export default function KerjakanScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  const [soal, setSoal] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedAnswers, setSelectedAnswers] = useState<any>({});
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  
  // LOGIKA SIDE NAV (DRAWER)
  const [sideNavVisible, setSideNavVisible] = useState(false);
  const slideAnim = useRef(new Animated.Value(-width)).current;

  // LOGIKA TIMER
  const [timeLeft, setTimeLeft] = useState(0);
  const timerRef = useRef<NodeJS.Timeout | null>(null);

  const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
  const primaryRed = '#c91313';

  useEffect(() => { 
    fetchData(); 
    return () => { if (timerRef.current) clearInterval(timerRef.current); };
  }, []);

  const fetchData = async () => {
    try {
      const resSoal = await api.get(`/ujian/${id}/soal`, { headers: { 'X-Exam-Token': token } });
      setSoal(resSoal.data);
      
      const resJadwal = await api.get(`/jadwal`);
      const currentJadwal = resJadwal.data.data.find((j: any) => j.id.toString() === id);
      if (currentJadwal) {
        setTimeLeft(currentJadwal.durasi * 60);
        startTimer();
      }
    } catch (e) { 
      if (Platform.OS === 'web') window.alert("Gagal memuat data ujian.");
      else Alert.alert("Gagal", "Tidak dapat memuat data ujian.");
    } finally { 
      setLoading(false); 
    }
  };

  const startTimer = () => {
    timerRef.current = setInterval(() => {
      setTimeLeft((prev) => {
        if (prev <= 1) {
          clearInterval(timerRef.current!);
          confirmFinish(true);
          return 0;
        }
        return prev - 1;
      });
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

  const handleAnswerChange = async (questionId: any, answer: any) => {
    if (isSubmitted) return;
    setSelectedAnswers((prev: any) => ({ ...prev, [questionId]: answer }));
    try {
      await api.post(`/ujian/${id}/submit-answer`, { 
        question_id: questionId, 
        answer: answer 
      }, { headers: { 'X-Exam-Token': token } });
    } catch (e) { console.log("Simpan gagal", e); }
  };

  const confirmFinish = async (isAuto = false) => {
    // LOGIKA VALIDASI: Cek ID soal yang belum ada di state atau isinya kosong/spasi
    const allQuestionIds = soal.map(s => s.id);
    const unansweredQuestions = allQuestionIds.filter(id => {
      const ans = selectedAnswers[id];
      return ans === undefined || ans === null || (typeof ans === 'string' && ans.trim() === '');
    });

    if (!isAuto && unansweredQuestions.length > 0) {
      setModalVisible(false);
      const pesan = `Jawaban Belum Lengkap! Masih ada ${unansweredQuestions.length} soal yang belum dijawab.`;

      // Gunakan window.alert jika di Web agar pasti muncul
      if (Platform.OS === 'web') {
        window.alert(pesan);
        toggleSideNav(true);
      } else {
        setTimeout(() => {
          Alert.alert("Belum Lengkap", pesan, [{ text: "Cek Soal", onPress: () => toggleSideNav(true) }]);
        }, 100);
      }
      return;
    }

    setModalVisible(false);
    try {
      await api.post(`/ujian/${id}/finish`, {}, { headers: { 'X-Exam-Token': token } });
      setIsSubmitted(true);
      if (timerRef.current) clearInterval(timerRef.current);
      router.replace({ pathname: '/ujian/selesai', params: { id, token } });
    } catch (e) {
      if (Platform.OS === 'web') window.alert("Koneksi bermasalah saat mengirim jawaban.");
      else Alert.alert("Gagal", "Koneksi bermasalah.");
    }
  };

  if (loading) return <View style={styles.center}><ActivityIndicator size="large" color={primaryRed} /></View>;

  const currentItem = soal[currentIndex];

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={primaryRed} />
      
      <View style={styles.header}>
        <TouchableOpacity onPress={() => toggleSideNav(true)} style={styles.menuIcon}>
           <Ionicons name="grid" size={24} color="#fff" />
        </TouchableOpacity>
        
        <View style={styles.schoolHeader}>
          <Image 
            source={require('../../assets/images/logosekolah.png')} 
            style={styles.logoSekolah} 
          />
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

      <ScrollView contentContainerStyle={styles.scrollContainer} showsVerticalScrollIndicator={false}>
        <View style={styles.card}>
          <Text style={styles.progressLabel}>Pertanyaan No. {currentIndex + 1}</Text>
          {currentItem?.question_image && (
            <Image source={{ uri: `${BASE_URL}/storage/${currentItem.question_image}` }} style={styles.image} resizeMode="contain" />
          )}
          <Text style={styles.questionText}>{currentItem?.question_text}</Text>
          
          {currentItem?.type?.toLowerCase() === 'pg' ? (
            <View style={styles.optionsWrapper}>
              {['a', 'b', 'c', 'd', 'e'].map((opt) => currentItem[`option_${opt}`] ? (
                <TouchableOpacity 
                  key={opt} 
                  activeOpacity={0.7}
                  style={[styles.optionBtn, selectedAnswers[currentItem.id] === opt && styles.selectedBtn]} 
                  onPress={() => handleAnswerChange(currentItem.id, opt)}
                >
                  <View style={[styles.optCircle, selectedAnswers[currentItem.id] === opt && styles.selectedCircle]}>
                    <Text style={[styles.optLetter, selectedAnswers[currentItem.id] === opt && {color: '#fff'}]}>{opt.toUpperCase()}</Text>
                  </View>
                  <Text style={[styles.optionText, selectedAnswers[currentItem.id] === opt && styles.selectedText]}>{currentItem[`option_${opt}`]}</Text>
                </TouchableOpacity>
              ) : null)}
            </View>
          ) : (
            <TextInput 
              style={styles.essayInput} 
              multiline 
              placeholder="Tulis jawaban essay..."
              value={selectedAnswers[currentItem.id] || ''} 
              onChangeText={(text) => handleAnswerChange(currentItem.id, text)} 
            />
          )}
        </View>
      </ScrollView>

      <View style={styles.footer}>
        <TouchableOpacity style={[styles.btnNav, currentIndex === 0 && {opacity: 0.3}]} disabled={currentIndex === 0} onPress={() => setCurrentIndex(prev => prev - 1)}>
          <Ionicons name="chevron-back" size={20} color="#475569" />
          <Text style={styles.btnNavText}>Kembali</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.btnFinish, {backgroundColor: primaryRed}]} onPress={() => setModalVisible(true)}>
          <Text style={styles.btnFinishText}>SELESAI</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.btnNav, currentIndex === soal.length - 1 && {opacity: 0.3}]} disabled={currentIndex === soal.length - 1} onPress={() => setCurrentIndex(prev => prev + 1)}>
          <Text style={styles.btnNavText}>Lanjut</Text>
          <Ionicons name="chevron-forward" size={20} color="#475569" />
        </TouchableOpacity>
      </View>

      {sideNavVisible && <TouchableOpacity style={styles.drawerOverlay} activeOpacity={1} onPress={() => toggleSideNav(false)} />}
      <Animated.View style={[styles.drawer, { transform: [{ translateX: slideAnim }] }]}>
        <View style={styles.drawerHeader}><Text style={styles.drawerTitle}>Daftar Soal</Text></View>
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

      <Modal visible={modalVisible} transparent={true} animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Akhiri Ujian?</Text>
            <View style={styles.modalBtnRow}>
              <TouchableOpacity style={styles.modalCancel} onPress={() => setModalVisible(false)}><Text>Batal</Text></TouchableOpacity>
              <TouchableOpacity style={[styles.modalConfirm, {backgroundColor: primaryRed}]} onPress={() => confirmFinish(false)}>
                <Text style={{color: '#fff', fontWeight: 'bold'}}>Ya, Selesai</Text>
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
  header: { backgroundColor: '#c91313', height: 70, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 15 },
  menuIcon: { width: 35 },
  schoolHeader: { flexDirection: 'row', alignItems: 'center', gap: 10, flex: 1 },
  logoSekolah: { width: 35, height: 35, borderRadius: 5, backgroundColor: '#fff' },
  schoolName: { color: '#fff', fontSize: 14, fontWeight: '800' },
  appTagline: { color: '#fecaca', fontSize: 10 },
  timerContainer: { backgroundColor: 'rgba(0,0,0,0.2)', paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8 },
  timerText: { color: '#fff', fontSize: 16, fontWeight: '900' },
  scrollContainer: { padding: 20, paddingBottom: 100 },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 20, elevation: 3 },
  progressLabel: { color: '#64748b', fontSize: 12, fontWeight: '800', marginBottom: 15 },
  image: { width: '100%', height: 200, marginBottom: 20, borderRadius: 10 },
  questionText: { fontSize: 16, fontWeight: '700', lineHeight: 24, marginBottom: 20 },
  optionsWrapper: { gap: 10 },
  optionBtn: { flexDirection: 'row', alignItems: 'center', padding: 14, borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 15, gap: 12 },
  selectedBtn: { backgroundColor: '#fff1f2', borderColor: '#c91313' },
  optCircle: { width: 30, height: 30, borderRadius: 15, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center' },
  selectedCircle: { backgroundColor: '#c91313' },
  optLetter: { fontSize: 12, fontWeight: '800', color: '#475569' },
  optionText: { flex: 1, fontSize: 14 },
  selectedText: { color: '#c91313', fontWeight: '700' },
  essayInput: { backgroundColor: '#f8fafc', borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 15, padding: 15, height: 160, textAlignVertical: 'top' },
  footer: { position: 'absolute', bottom: 0, width: '100%', height: 80, backgroundColor: '#fff', borderTopWidth: 1, borderColor: '#f1f5f9', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20 },
  btnNav: { flexDirection: 'row', alignItems: 'center', gap: 5 },
  btnNavText: { fontWeight: '700', fontSize: 13 },
  btnFinish: { paddingHorizontal: 30, paddingVertical: 12, borderRadius: 12 },
  btnFinishText: { color: '#fff', fontWeight: '800' },
  drawerOverlay: { position: 'absolute', width: '100%', height: '100%', backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 1000 },
  drawer: { position: 'absolute', width: width * 0.75, height: '100%', backgroundColor: '#fff', zIndex: 1001, padding: 25, paddingTop: 50 },
  drawerHeader: { marginBottom: 30 },
  drawerTitle: { fontSize: 20, fontWeight: '800' },
  drawerGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12 },
  gridItem: { width: 45, height: 45, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  gridText: { fontWeight: '800' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center' },
  modalContent: { width: '80%', backgroundColor: '#fff', padding: 25, borderRadius: 20, alignItems: 'center' },
  modalTitle: { fontSize: 18, fontWeight: '800', marginBottom: 20 },
  modalBtnRow: { flexDirection: 'row', gap: 10 },
  modalCancel: { flex: 1, padding: 12, alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10 },
  modalConfirm: { flex: 1, padding: 12, alignItems: 'center', borderRadius: 10 }
});