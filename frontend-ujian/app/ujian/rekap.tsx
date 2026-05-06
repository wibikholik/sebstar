import { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, TouchableOpacity, ScrollView, Alert } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';

export default function RekapScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  const [result, setResult] = useState(null);
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, [id]); 

  const fetchData = async () => {
    setLoading(true);
    try {
      // 1. Ambil detail hasil ujian yang sedang dilihat
      if (id) {
        const res = await api.get(`/ujian/${id}/hasil`, { headers: { 'X-Exam-Token': token } });
        setResult(res.data);
      }
      
      // 2. Ambil riwayat ujian (Backend kita nanti akan mengirim data yang lebih rapi)
      const resHistory = await api.get(`/ujian/history`);
      setHistory(resHistory.data);
    } catch (e) {
      console.error("Gagal memuat data:", e);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <ActivityIndicator size="large" style={{flex: 1}} />;

  return (
    <ScrollView contentContainerStyle={styles.container}>
      
      {/* 1. DETAIL HASIL (Muncul jika ada ID ujian) */}
      {result && (
        <View style={styles.resultSection}>
          <Text style={styles.header}>Hasil Ujian Ini</Text>
          <View style={styles.scoreCard}>
            <Text style={styles.scoreLabel}>Nilai PG</Text>
            <Text style={styles.scoreValue}>{result?.score ?? 0}</Text>
          </View>
          <View style={styles.statsRow}>
            <View style={styles.statBox}><Text>Benar</Text><Text style={[styles.statValue, {color: '#28a745'}]}>{result?.pg?.correct ?? 0}</Text></View>
            <View style={styles.statBox}><Text>Salah</Text><Text style={[styles.statValue, {color: '#dc3545'}]}>{result?.pg?.wrong ?? 0}</Text></View>
          </View>
          <View style={styles.essayBox}>
            <Text style={styles.essayText}>Essay Terjawab: {result?.essay?.answered} / {result?.essay?.total}</Text>
          </View>
        </View>
      )}

      {/* 2. RIWAYAT NILAI */}
      <Text style={styles.sectionTitle}>Riwayat Ujian Lainnya</Text>
      
      {history.length > 0 ? history.map((item, index) => (
        <TouchableOpacity 
          key={index} 
          style={styles.historyCard}
          onPress={() => router.push({ 
            pathname: '/ujian/rekap', 
            params: { id: item.id, token: item.token } 
          })}
        >
          <View style={{ flex: 1 }}>
            <Text style={styles.historySubject}>{item.nama_mapel ?? 'Mata Pelajaran'}</Text>
            <Text style={styles.historyDate}>Tanggal: {item.tanggal_ujian ?? '-'}</Text>
          </View>
          <View style={styles.scoreBadge}>
            <Text style={styles.historyScore}>{item.score ?? '-'}</Text>
          </View>
        </TouchableOpacity>
      )) : (
        <Text style={{textAlign: 'center', color: '#999', marginTop: 10}}>Belum ada riwayat ujian.</Text>
      )}

      <TouchableOpacity style={styles.btnHome} onPress={() => router.replace('/(tabs)')}>
        <Text style={styles.btnText}>KEMBALI KE BERANDA</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flexGrow: 1, padding: 20, backgroundColor: '#f9f9f9' },
  header: { fontSize: 20, fontWeight: 'bold', marginBottom: 15, color: '#333' },
  resultSection: { marginBottom: 30, backgroundColor: '#fff', padding: 15, borderRadius: 15, borderWidth: 1, borderColor: '#eee' },
  scoreCard: { alignItems: 'center', marginBottom: 15 },
  scoreLabel: { fontSize: 14, color: '#666' },
  scoreValue: { fontSize: 40, fontWeight: 'bold', color: '#007bff' },
  statsRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15 },
  statBox: { width: '48%', padding: 15, backgroundColor: '#f4f4f4', borderRadius: 10, alignItems: 'center' },
  statValue: { fontSize: 20, fontWeight: 'bold' },
  essayBox: { padding: 10, backgroundColor: '#fff3cd', borderRadius: 8, alignItems: 'center' },
  
  sectionTitle: { fontSize: 18, fontWeight: 'bold', marginBottom: 15, marginTop: 10 },
  historyCard: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 15, backgroundColor: '#fff', borderRadius: 10, marginBottom: 10, borderWidth: 1, borderColor: '#ddd' },
  historySubject: { fontSize: 16, fontWeight: '700', color: '#333' },
  historyDate: { fontSize: 12, color: '#666', marginTop: 4 },
  scoreBadge: { backgroundColor: '#e7f3ff', padding: 10, borderRadius: 8, minWidth: 50, alignItems: 'center' },
  historyScore: { fontSize: 16, fontWeight: 'bold', color: '#007bff' },
  
  btnHome: { backgroundColor: '#333', padding: 15, borderRadius: 10, alignItems: 'center', marginTop: 20 },
  btnText: { color: '#fff', fontWeight: 'bold' }
});