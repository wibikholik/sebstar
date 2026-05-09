import { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, TouchableOpacity, ScrollView, Alert, StatusBar, SafeAreaView } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';
import { Ionicons } from '@expo/vector-icons';

export default function RekapScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  const [result, setResult] = useState<any>(null);
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(true);

  const primaryRed = '#c91313';

  useEffect(() => {
    fetchData();
  }, [id]); 

  const fetchData = async () => {
    setLoading(true);
    try {
      // 1. Ambil detail hasil ujian yang baru saja diselesaikan
      if (id) {
        const res = await api.get(`/ujian/${id}/hasil`, { 
          headers: { 'X-Exam-Token': token } 
        });
        setResult(res.data);
      }
      
      // 2. Ambil riwayat ujian siswa secara keseluruhan
      const resHistory = await api.get(`/ujian/history`);
      setHistory(resHistory.data);
    } catch (e) {
      console.error("Gagal memuat data:", e);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return (
    <View style={styles.center}>
      <ActivityIndicator size="large" color={primaryRed} />
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        
        {/* 1. HEADER RINGKASAN */}
        <View style={styles.pageHeader}>
          <Text style={styles.pageTitle}>Rekapitulasi Nilai</Text>
          <Text style={styles.pageSubtitle}>Pantau progres dan hasil ujian Anda di sini.</Text>
        </View>

        {/* 2. DETAIL HASIL TERBARU (Hanya muncul jika diakses setelah ujian) */}
        {result && (
          <View style={styles.resultCard}>
            <View style={styles.resultHeader}>
              <Ionicons name="ribbon" size={24} color={primaryRed} />
              <Text style={styles.resultHeaderTitle}>Hasil Ujian Ini</Text>
            </View>
            
            <View style={styles.scoreCircle}>
              <Text style={styles.scoreLabel}>Nilai PG</Text>
              <Text style={styles.scoreValue}>{result?.score ?? 0}</Text>
            </View>

            <View style={styles.statsRow}>
              <View style={[styles.statBox, { backgroundColor: '#f0fdf4' }]}>
                <Ionicons name="checkmark-circle" size={18} color="#15803d" />
                <Text style={styles.statLabel}>Benar</Text>
                <Text style={[styles.statValue, { color: '#15803d' }]}>{result?.pg?.correct ?? 0}</Text>
              </View>
              <View style={[styles.statBox, { backgroundColor: '#fef2f2' }]}>
                <Ionicons name="close-circle" size={18} color="#b91c1c" />
                <Text style={styles.statLabel}>Salah</Text>
                <Text style={[styles.statValue, { color: '#b91c1c' }]}>{result?.pg?.wrong ?? 0}</Text>
              </View>
            </View>

            <View style={styles.essayInfo}>
              <Ionicons name="reader-outline" size={16} color="#854d0e" />
              <Text style={styles.essayText}>
                Essay: <Text style={{ fontWeight: '800' }}>{result?.essay?.answered} / {result?.essay?.total}</Text> dijawab
              </Text>
            </View>
          </View>
        )}

        {/* 3. RIWAYAT UJIAN LAINNYA */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Riwayat Ujian</Text>
          <Ionicons name="time-outline" size={20} color="#64748b" />
        </View>
        
        {history.length > 0 ? history.map((item: any, index) => (
          <TouchableOpacity 
            key={index} 
            activeOpacity={0.7}
            style={styles.historyCard}
            onPress={() => router.push({ 
              pathname: '/ujian/rekap', 
              params: { id: item.id, token: item.token } 
            })}
          >
            <View style={styles.historyInfo}>
              <Text style={styles.historySubject} numberOfLines={1}>{item.nama_mapel ?? 'Mata Pelajaran'}</Text>
              <Text style={styles.historyDate}>
                📅 {item.tanggal_ujian ?? '-'}
              </Text>
            </View>
            <View style={styles.scoreBadge}>
              <Text style={styles.badgeScoreText}>{item.score ?? '0'}</Text>
            </View>
            <Ionicons name="chevron-forward" size={18} color="#cbd5e1" />
          </TouchableOpacity>
        )) : (
          <View style={styles.emptyState}>
            <Ionicons name="file-tray-outline" size={48} color="#cbd5e1" />
            <Text style={styles.emptyText}>Belum ada riwayat ujian yang tercatat.</Text>
          </View>
        )}

        <TouchableOpacity 
          style={[styles.btnHome, { backgroundColor: '#1e293b' }]} 
          onPress={() => router.replace('/(tabs)')}
        >
          <Ionicons name="home-outline" size={18} color="#fff" />
          <Text style={styles.btnText}>KEMBALI KE BERANDA</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scrollContent: { padding: 25, paddingBottom: 40 },
  pageHeader: { marginBottom: 25 },
  pageTitle: { fontSize: 24, fontWeight: '800', color: '#1e293b' },
  pageSubtitle: { fontSize: 14, color: '#64748b', marginTop: 4 },
  
  // Result Card Styling
  resultCard: { 
    backgroundColor: '#fff', 
    borderRadius: 24, 
    padding: 20, 
    marginBottom: 30,
    elevation: 3,
    shadowColor: '#000',
    shadowOpacity: 0.05,
    shadowRadius: 10
  },
  resultHeader: { flexDirection: 'row', alignItems: 'center', gap: 8, marginBottom: 20 },
  resultHeaderTitle: { fontSize: 16, fontWeight: '700', color: '#1e293b' },
  scoreCircle: { 
    alignItems: 'center', 
    paddingVertical: 20, 
    borderWidth: 1, 
    borderColor: '#f1f5f9', 
    borderRadius: 100, 
    width: 140, 
    height: 140, 
    alignSelf: 'center',
    justifyContent: 'center',
    marginBottom: 20
  },
  scoreLabel: { fontSize: 12, color: '#94a3b8', fontWeight: '600', textTransform: 'uppercase' },
  scoreValue: { fontSize: 48, fontWeight: '900', color: '#c91313' },
  statsRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15, gap: 12 },
  statBox: { flex: 1, padding: 15, borderRadius: 15, alignItems: 'center', gap: 4 },
  statLabel: { fontSize: 11, fontWeight: '600', color: '#64748b' },
  statValue: { fontSize: 18, fontWeight: '800' },
  essayInfo: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    justifyContent: 'center', 
    gap: 8, 
    backgroundColor: '#fefce8', 
    padding: 12, 
    borderRadius: 12 
  },
  essayText: { fontSize: 13, color: '#854d0e' },
  
  // History Section
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15 },
  sectionTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  historyCard: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    backgroundColor: '#fff', 
    padding: 16, 
    borderRadius: 16, 
    marginBottom: 12,
    borderWidth: 1,
    borderColor: '#f1f5f9'
  },
  historyInfo: { flex: 1 },
  historySubject: { fontSize: 15, fontWeight: '700', color: '#1e293b' },
  historyDate: { fontSize: 12, color: '#94a3b8', marginTop: 4 },
  scoreBadge: { 
    backgroundColor: '#f1f5f9', 
    paddingHorizontal: 12, 
    paddingVertical: 6, 
    borderRadius: 10,
    marginRight: 10
  },
  badgeScoreText: { fontSize: 15, fontWeight: '800', color: '#c91313' },
  
  btnHome: { 
    flexDirection: 'row',
    padding: 18, 
    borderRadius: 16, 
    alignItems: 'center', 
    justifyContent: 'center', 
    marginTop: 20,
    gap: 10
  },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 14 },
  emptyState: { alignItems: 'center', paddingVertical: 40 },
  emptyText: { color: '#cbd5e1', marginTop: 10, fontWeight: '500' }
});