import React, { useEffect, useState } from 'react';
import { 
  View, Text, StyleSheet, ActivityIndicator, TouchableOpacity, 
  ScrollView, StatusBar, SafeAreaView 
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';
import { Ionicons } from '@expo/vector-icons';

export default function RekapScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  
  const [result, setResult] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState("");

  const primaryRed = '#c91313'; 

  useEffect(() => {
    if (id) {
      fetchData();
    } else {
      setLoading(false);
      setErrorMsg("ID Ujian tidak valid.");
    }
  }, [id]);

  const fetchData = async () => {
    try {
      setLoading(true);
      const res = await api.get(`/ujian/${id}/hasil`, { 
        headers: { 'X-Exam-Token': token } 
      });
      
      if (res.data && res.data.success) {
        setResult(res.data.data);
      }
    } catch (e: any) {
      setErrorMsg("Gagal memuat rekap nilai.");
      console.error(e);
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
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="#1e293b" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Hasil Ujian</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {result ? (
          <>
            <View style={styles.infoSection}>
              <Text style={styles.mapelName}>{result.subject_name || 'Ujian'}</Text>
              {/* Menampilkan bobot yang diterima dari API */}
              <View style={styles.weightBadge}>
                <Text style={styles.weightText}>
                  Bobot: {result.breakdown.weight_pg}% PG + {result.breakdown.weight_essay}% Essay
                </Text>
              </View>
            </View>

            <View style={styles.scoreCard}>
              <Text style={styles.scoreLabel}>NILAI AKHIR</Text>
              {/* Hanya tampilkan nilai jika sudah complete, jika belum tampilkan placeholder */}
              <Text style={[styles.scoreValue, { color: result.is_complete ? '#16a34a' : '#94a3b8' }]}>
                {result.is_complete ? result.final_score : '--'}
              </Text>
              <View style={[styles.statusBadge, { backgroundColor: result.is_complete ? '#f0fdf4' : '#fffbeb' }]}>
                <Text style={[styles.statusBadgeText, { color: result.is_complete ? '#166534' : '#854d0e' }]}>
                  {result.is_complete ? 'PENILAIAN SELESAI' : 'MENUNGGU KOREKSI GURU'}
                </Text>
              </View>
            </View>

            <View style={styles.table}>
              <View style={styles.tableRow}>
                <Text style={styles.tableLabel}>Skor Pilihan Ganda</Text>
                <Text style={styles.tableValue}>{result.breakdown.score_pg}</Text>
              </View>
              <View style={[styles.tableRow, { borderBottomWidth: 0 }]}>
                <Text style={styles.tableLabel}>Skor Essay</Text>
                <Text style={styles.tableValue}>
                  {result.is_complete ? result.breakdown.score_essay : 'Menunggu Dinilai'}
                </Text>
              </View>
            </View>
          </>
        ) : (
          <Text style={styles.errorText}>{errorMsg || "Data tidak ditemukan"}</Text>
        )}

        <TouchableOpacity style={styles.btnFinish} onPress={() => router.replace('/(tabs)')}>
          <Text style={styles.btnFinishText}>Selesai & Kembali</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { height: 60, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 20 },
  headerTitle: { fontSize: 16, fontWeight: '700' },
  scrollContent: { padding: 25 },
  infoSection: { alignItems: 'center', marginBottom: 25 },
  mapelName: { fontSize: 22, fontWeight: '900', textAlign: 'center' },
  weightBadge: { backgroundColor: '#f1f5f9', padding: 8, borderRadius: 8, marginTop: 10 },
  weightText: { fontSize: 11, fontWeight: '700', color: '#64748b' },
  scoreCard: { alignItems: 'center', padding: 35, borderWidth: 1, borderColor: '#f1f5f9', borderRadius: 20, marginBottom: 25 },
  scoreLabel: { fontSize: 11, fontWeight: '800', color: '#94a3b8', letterSpacing: 2 },
  scoreValue: { fontSize: 70, fontWeight: '900', marginVertical: 10 },
  statusBadge: { paddingHorizontal: 14, paddingVertical: 6, borderRadius: 12 },
  statusBadgeText: { fontSize: 10, fontWeight: '800' },
  table: { backgroundColor: '#f8fafc', borderRadius: 20, padding: 10, marginBottom: 25 },
  tableRow: { flexDirection: 'row', justifyContent: 'space-between', padding: 18, borderBottomWidth: 1, borderBottomColor: '#e2e8f0' },
  tableLabel: { color: '#64748b', fontWeight: '600' },
  tableValue: { fontWeight: '800', color: '#1e293b' },
  btnFinish: { backgroundColor: '#1e293b', padding: 18, borderRadius: 16, alignItems: 'center' },
  btnFinishText: { color: '#fff', fontWeight: '800' },
  errorText: { textAlign: 'center', color: '#64748b' }
});