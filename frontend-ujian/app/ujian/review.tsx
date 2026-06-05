import React, { useEffect, useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  ActivityIndicator, 
  ScrollView, 
  TouchableOpacity, 
  Platform,
  Image,
  StatusBar
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';
import { Ionicons } from '@expo/vector-icons';

export default function ReviewScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  
  const [reviewData, setReviewData] = useState([]);
  const [subjectName, setSubjectName] = useState('');
  const [loading, setLoading] = useState(true);

  const primaryRed = '#c91313';
  const BASE_URL = process.env.EXPO_PUBLIC_API_URL;
  const ASSET_URL = BASE_URL ? BASE_URL.replace(/\/api$/, '') : '';

  useEffect(() => {
    fetchReviewData();
  }, []);

  const fetchReviewData = async () => {
    try {
      setLoading(true);
      const res = await api.get(`/ujian/${id}/hasil`, {
        headers: { 'X-Exam-Token': token }
      });
      if (res.data && res.data.success) {
        setSubjectName(res.data.data.subject_name);
        setReviewData(res.data.data.detail_jawaban || []);
      }
    } catch (error) {
      console.log("Gagal fetch data review:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={primaryRed} />
        <Text style={{ marginTop: 10, color: '#64748b' }}>Memuat Pembahasan...</Text>
      </View>
    );
  }

  return (
    // Menggunakan View biasa sebagai pembungkus utama khusus web agar tidak terkena styling bawaan SafeAreaView iOS browser
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#c91313" />

      {/* HEADER KOTAK TOTAL (SAMA PERSIS DENGAN KERJAKAN.TSX) */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backBtn}>
          <Ionicons name="arrow-back" size={24} color="#fff" />
        </TouchableOpacity>
        
        <View style={styles.headerTitleWrapper}>
          <Text style={styles.headerTitle}>Pembahasan Ujian</Text>
        </View>
        
        <View style={{ width: 35 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* SUB-HEADER UNTUK NAMA MATA PELAJARAN DI DALAM SCROLL AREA */}
        <View style={styles.subjectHeader}>
          <Ionicons name="book-outline" size={18} color="#475569" />
          <Text style={styles.subjectHeaderText}>{subjectName || 'Mata Pelajaran'}</Text>
        </View>

        {reviewData.map((item, index) => {
          const isPG = item.type === 'pg';
          const isCorrect = isPG && item.is_correct;
          const isWrong = isPG && !item.is_correct;

          return (
            <View key={item.id} style={styles.card}>
              <View style={styles.cardHeader}>
                <Text style={styles.questionNumber}>Soal No. {index + 1}</Text>
                <View style={[
                  styles.badge, 
                  isCorrect && styles.badgeSuccess,
                  isWrong && styles.badgeDanger,
                  !isPG && styles.badgeInfo
                ]}>
                  <Text style={[
                    styles.badgeText,
                    isCorrect && styles.badgeTextSuccess,
                    isWrong && styles.badgeTextDanger,
                    !isPG && styles.badgeTextInfo
                  ]}>
                    {isCorrect ? 'BENAR' : isWrong ? 'SALAH' : `NILAI: ${item.essay_score}`}
                  </Text>
                </View>
              </View>

              {item.question_image && (
                <Image 
                  source={{ uri: `${ASSET_URL}/storage/${item.question_image}` }} 
                  style={styles.questionImage} 
                  resizeMode="contain" 
                />
              )}

              <Text style={styles.questionText}>{item.question_text}</Text>

              {/* JAWABAN SISWA */}
              <View style={styles.answerBox}>
                <Text style={styles.answerLabel}>Jawaban Kamu:</Text>
                <Text style={[styles.studentAnswer, isWrong && { color: '#991b1b' }, isCorrect && { color: '#166534' }]}>
                  {item.student_answer}
                </Text>
              </View>

              {/* KUNCI JAWABAN (JIKA PG SALAH) */}
              {isWrong && (
                <View style={styles.correctionBox}>
                  <Text style={styles.correctionLabel}>Kunci Jawaban Benar:</Text>
                  <Text style={styles.correctAnswer}>{item.correct_answer}</Text>
                </View>
              )}

              {/* CATATAN GURU (JIKA ESSAY) */}
              {!isPG && item.teacher_note && (
                <View style={styles.teacherNoteBox}>
                  <View style={{flexDirection: 'row', alignItems: 'center', gap: 5, marginBottom: 4}}>
                    <Ionicons name="chatbubble-ellipses" size={14} color="#0369a1" />
                    <Text style={styles.teacherNoteLabel}>Catatan Koreksi Guru:</Text>
                  </View>
                  <Text style={styles.teacherNoteText}>{item.teacher_note}</Text>
                </View>
              )}
            </View>
          );
        })}

        {reviewData.length === 0 && (
          <Text style={{ textAlign: 'center', color: '#64748b', marginTop: 50 }}>Data pembahasan tidak tersedia.</Text>
        )}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: '#f8fafc',
    // Perbaikan fatal untuk browser web: hilangkan padding bawaan area luar browser
    padding: 0,
    margin: 0
  },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#f8fafc' },
  
  // FIX TOTAL KOTAK: Tanpa border radius, tanpa border terpotong di browser web
   header: { 
    backgroundColor: '#c91313', 
    height: 75,
    paddingHorizontal: 20,
    paddingTop: Platform.OS === 'android' ? 10 : 0,
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    zIndex: 10
  },
  backBtn: { width: 35, alignItems: 'flex-start' },
  headerTitleWrapper: { flex: 1, alignItems: 'center' },
  headerTitle: { color: '#ffffff', fontSize: 16, fontWeight: '800', textAlign: 'center' },

  scrollContent: { padding: 20, paddingBottom: 50 },
  
  // Gaya sub-header mapel baru di luar jangkauan area header utama
  subjectHeader: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    gap: 6, 
    marginBottom: 20, 
    backgroundColor: '#fff', 
    padding: 12, 
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    alignSelf: 'flex-start'
  },
  subjectHeaderText: { fontSize: 13, fontWeight: '700', color: '#475569' },

  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 20,
    marginBottom: 15,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
  },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15 },
  questionNumber: { fontSize: 13, fontWeight: '800', color: '#64748b' },
  
  badge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 6 },
  badgeSuccess: { backgroundColor: '#dcfce7' },
  badgeDanger: { backgroundColor: '#fee2e2' },
  badgeInfo: { backgroundColor: '#f1f5f9' },
  
  badgeText: { fontSize: 10, fontWeight: '800', letterSpacing: 0.5 },
  badgeTextSuccess: { color: '#166534' },
  badgeTextDanger: { color: '#991b1b' },
  badgeTextInfo: { color: '#0f172a' },

  questionImage: { width: '100%', height: 150, borderRadius: 10, marginBottom: 15 },
  questionText: { fontSize: 15, color: '#1e293b', fontWeight: '600', lineHeight: 22, marginBottom: 15 },

  answerBox: { backgroundColor: '#f8fafc', padding: 12, borderRadius: 10, borderWidth: 1, borderColor: '#f1f5f9' },
  answerLabel: { fontSize: 11, fontWeight: '700', color: '#64748b', textTransform: 'uppercase', marginBottom: 4 },
  studentAnswer: { fontSize: 14, color: '#334155', fontWeight: '600' },

  correctionBox: { backgroundColor: '#fff1f2', padding: 12, borderRadius: 10, marginTop: 10, borderWidth: 1, borderColor: '#ffe4e6' },
  correctionLabel: { fontSize: 11, fontWeight: '700', color: '#e11d48', textTransform: 'uppercase', marginBottom: 4 },
  correctAnswer: { fontSize: 14, color: '#be123c', fontWeight: '700' },

  teacherNoteBox: { backgroundColor: '#f0f9ff', padding: 12, borderRadius: 10, marginTop: 10, borderWidth: 1, borderColor: '#e0f2fe' },
  teacherNoteLabel: { fontSize: 11, fontWeight: '800', color: '#0369a1', textTransform: 'uppercase' },
  teacherNoteText: { fontSize: 13, color: '#0f172a', fontStyle: 'italic', marginTop: 2 }
});