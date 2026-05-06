import { useEffect, useState } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, ActivityIndicator, TextInput, Image, ScrollView, Modal, Alert } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';

export default function KerjakanScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  const [soal, setSoal] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedAnswers, setSelectedAnswers] = useState({});
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  
  const [currentIndex, setCurrentIndex] = useState(0);
  const BASE_URL = process.env.EXPO_PUBLIC_API_URL;

  useEffect(() => { fetchSoal(); }, []);

  const fetchSoal = async () => {
    try {
      const res = await api.get(`/ujian/${id}/soal`, { headers: { 'X-Exam-Token': token } });
      setSoal(res.data);
    } catch (e) { console.log('Error', e); } finally { setLoading(false); }
  };

  const handleAnswerChange = async (questionId, answer) => {
    if (isSubmitted) return;
    setSelectedAnswers(prev => ({ ...prev, [questionId]: answer }));
    try {
      await api.post(`/ujian/${id}/submit-answer`, { question_id: questionId, answer: answer }, { headers: { 'X-Exam-Token': token } });
    } catch (e) { console.log(e); }
  };

  // PERBAIKAN: Fungsi ini sekarang memanggil API backend sebelum navigasi
  const confirmFinish = async () => {
    setModalVisible(false);
    try {
      // 1. Kirim sinyal selesai ke backend
      await api.post(`/ujian/${id}/finish`, {}, { 
        headers: { 'X-Exam-Token': token } 
      });

      // 2. Tandai lokal state sebagai submitted
      setIsSubmitted(true);
      
      // 3. Pindah halaman
      router.replace({ pathname: '/ujian/selesai', params: { id, token } });
    } catch (e) {
      console.error("Gagal menyelesaikan ujian:", e);
      Alert.alert("Gagal", "Terjadi kesalahan saat mengirim jawaban. Silakan coba lagi.");
    }
  };

  if (loading) return <ActivityIndicator size="large" style={{flex: 1}} />;
  if (soal.length === 0) return <Text style={{textAlign: 'center', marginTop: 50}}>Tidak ada soal.</Text>;

  const currentItem = soal[currentIndex];
  const isPg = currentItem.type?.toLowerCase().trim() === 'pg';

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <Text style={styles.progressText}>Soal {currentIndex + 1} dari {soal.length}</Text>
        <View style={styles.card}>
          {currentItem.question_image && (
            <Image source={{ uri: `${BASE_URL}/storage/${currentItem.question_image}` }} style={styles.image} resizeMode="contain" />
          )}
          <Text style={styles.questionText}>{currentItem.question_text}</Text>
          {isPg ? (
            <View>
              {['a', 'b', 'c', 'd', 'e'].map((opt) => currentItem[`option_${opt}`] ? (
                <TouchableOpacity key={opt} style={[styles.optionBtn, selectedAnswers[currentItem.id] === opt && styles.selectedBtn]} onPress={() => handleAnswerChange(currentItem.id, opt)}>
                  <Text style={[styles.optionText, selectedAnswers[currentItem.id] === opt && styles.selectedText]}>{opt.toUpperCase()}. {currentItem[`option_${opt}`]}</Text>
                </TouchableOpacity>
              ) : null)}
            </View>
          ) : (
            <TextInput style={styles.essayInput} multiline editable={!isSubmitted} value={selectedAnswers[currentItem.id] || ''} onChangeText={(text) => handleAnswerChange(currentItem.id, text)} />
          )}
        </View>
      </ScrollView>

      <View style={styles.navContainer}>
        <TouchableOpacity style={[styles.navBtn, {opacity: currentIndex === 0 ? 0.5 : 1}]} disabled={currentIndex === 0} onPress={() => setCurrentIndex(prev => prev - 1)}>
          <Text style={styles.navBtnText}>Sebelumnya</Text>
        </TouchableOpacity>
        {currentIndex === soal.length - 1 ? (
          <TouchableOpacity style={[styles.navBtn, styles.finishBtn]} onPress={() => setModalVisible(true)}><Text style={styles.navBtnText}>Selesai</Text></TouchableOpacity>
        ) : (
          <TouchableOpacity style={styles.navBtn} onPress={() => setCurrentIndex(prev => prev + 1)}><Text style={styles.navBtnText}>Selanjutnya</Text></TouchableOpacity>
        )}
      </View>

      <Modal visible={modalVisible} transparent={true} animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Konfirmasi</Text>
            <Text style={styles.modalText}>Yakin ingin menyelesaikan ujian?</Text>
            <View style={styles.modalButtonRow}>
              <TouchableOpacity style={styles.modalCancelBtn} onPress={() => setModalVisible(false)}><Text>Batal</Text></TouchableOpacity>
              <TouchableOpacity style={styles.modalConfirmBtn} onPress={confirmFinish}><Text style={{color: '#fff', fontWeight: 'bold'}}>Ya</Text></TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f4f4f4' },
  scrollContainer: { padding: 15, paddingBottom: 100 }, 
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 12 },
  image: { width: '100%', height: 220, marginBottom: 15, borderRadius: 8, backgroundColor: '#eee' },
  questionText: { fontSize: 18, fontWeight: 'bold', marginBottom: 20 },
  optionBtn: { padding: 16, borderWidth: 1, borderColor: '#eee', borderRadius: 10, marginBottom: 10 },
  selectedBtn: { backgroundColor: '#007bff', borderColor: '#007bff' },
  optionText: { fontSize: 16, color: '#333' },
  selectedText: { color: '#fff' },
  essayInput: { borderWidth: 1, borderColor: '#ddd', borderRadius: 8, padding: 15, height: 140, textAlignVertical: 'top', fontSize: 16 },
  progressText: { textAlign: 'center', marginBottom: 10, fontSize: 14, color: '#666' },
  navContainer: { position: 'absolute', bottom: 0, left: 0, right: 0, flexDirection: 'row', justifyContent: 'space-between', padding: 20, backgroundColor: '#fff', borderTopWidth: 1, borderColor: '#ddd', zIndex: 999, elevation: 5 },
  navBtn: { padding: 15, backgroundColor: '#555', borderRadius: 8, width: '45%', alignItems: 'center' },
  finishBtn: { backgroundColor: '#d9534f' },
  navBtnText: { color: '#fff', fontWeight: 'bold' },
  modalOverlay: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: 'rgba(0,0,0,0.5)' },
  modalContent: { width: '80%', backgroundColor: '#fff', padding: 20, borderRadius: 15, alignItems: 'center' },
  modalTitle: { fontSize: 20, fontWeight: 'bold', marginBottom: 15 },
  modalText: { fontSize: 16, textAlign: 'center', marginBottom: 25 },
  modalButtonRow: { flexDirection: 'row', width: '100%', justifyContent: 'space-between' },
  modalCancelBtn: { padding: 12, width: '45%', alignItems: 'center', backgroundColor: '#eee', borderRadius: 8 },
  modalConfirmBtn: { padding: 12, width: '45%', alignItems: 'center', backgroundColor: '#d9534f', borderRadius: 8 }
});