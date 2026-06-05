import React, { useEffect, useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  ActivityIndicator, 
  TouchableOpacity, 
  ScrollView, 
  StatusBar, 
  SafeAreaView,
  Platform
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
      <Text style={{ marginTop: 10, color: '#64748b' }}>Menghitung skor akhir...</Text>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={primaryRed} />
      
      {/* HEADER MERAH TEGAS KOTAK (SERAGAM DENGAN KERJAKAN.TSX) */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backBtn}>
          <Ionicons name="arrow-back" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Rekapitulasi Ujian</Text>
        <View style={{ width: 34 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {result ? (
          <>
            {/* INFO MATA PELAJARAN */}
            <View style={styles.infoSection}>
              <View style={styles.iconCircle}>
                <Ionicons name="school" size={32} color={primaryRed} />
              </View>
              <Text style={styles.mapelName}>{result.subject_name || 'Mata Pelajaran'}</Text>
              <View style={styles.weightBadge}>
                <Text style={styles.weightText}>
                  Bobot Penilaian: {result.breakdown?.weight_pg ?? 0}% PG + {result.breakdown?.weight_essay ?? 0}% Essay
                </Text>
              </View>
            </View>

            {/* KARTU NILAI AKHIR */}
            <View style={styles.scoreCard}>
              <Text style={styles.scoreLabel}>NILAI AKHIR ANDA</Text>
              <Text style={[styles.scoreValue, { color: result.is_complete ? primaryRed : '#94a3b8' }]}>
                {result.is_complete ? result.final_score : '--'}
              </Text>
              <View style={[styles.statusBadge, { backgroundColor: result.is_complete ? '#fef2f2' : '#f8fafc', borderColor: result.is_complete ? '#fecaca' : '#e2e8f0' }]}>
                <Text style={[styles.statusBadgeText, { color: result.is_complete ? primaryRed : '#64748b' }]}>
                  {result.is_complete ? 'PENILAIAN SELESAI' : 'MENUNGGU KOREKSI ESSAY'}
                </Text>
              </View>
            </View>

            {/* BREAKDOWN SKOR */}
            <View style={styles.table}>
              <View style={styles.tableRow}>
                <View style={{flexDirection: 'row', alignItems: 'center', gap: 8}}>
                  <Ionicons name="checkmark-circle-outline" size={18} color="#64748b" />
                  <Text style={styles.tableLabel}>Skor Pilihan Ganda</Text>
                </View>
                <Text style={styles.tableValue}>{result.breakdown?.score_pg ?? 0}</Text>
              </View>
              <View style={[styles.tableRow, { borderBottomWidth: 0 }]}>
                <View style={{flexDirection: 'row', alignItems: 'center', gap: 8}}>
                  <Ionicons name="create-outline" size={18} color="#64748b" />
                  <Text style={styles.tableLabel}>Skor Essay</Text>
                </View>
                <Text style={[styles.tableValue, !result.is_complete && { color: '#f59e0b', fontSize: 13 }]}>
                  {result.is_complete ? (result.breakdown?.score_essay ?? 0) : 'Menunggu Dinilai'}
                </Text>
              </View>
            </View>

            {/* TOMBOL AKSI */}
            <View style={styles.actionContainer}>
              <TouchableOpacity 
                style={styles.btnReview} 
                onPress={() => router.push({ pathname: '/ujian/review', params: { id, token } })}
              >
                <Ionicons name="document-text" size={18} color={primaryRed} />
                <Text style={styles.btnReviewText}>LIHAT PEMBAHASAN</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.btnFinish} onPress={() => router.replace('/(tabs)')}>
                <Text style={styles.btnFinishText}>SELESAI & KEMBALI KE BERANDA</Text>
              </TouchableOpacity>
            </View>
          </>
        ) : (
          <View style={styles.errorContainer}>
            <Ionicons name="warning-outline" size={48} color="#cbd5e1" />
            <Text style={styles.errorText}>{errorMsg || "Data tidak ditemukan"}</Text>
            <TouchableOpacity style={styles.btnFinish} onPress={() => router.replace('/(tabs)')}>
              <Text style={styles.btnFinishText}>Kembali ke Beranda</Text>
            </TouchableOpacity>
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#f8fafc' },
  
  // HEADER KOTAK TEGAS SEJAJAR DENGAN KERJAKAN.TSX (TANPA BORDER RADIUS KELENGKUNGAN)
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
  backBtn: { padding: 5, width: 35 },
  headerTitle: { color: '#ffffff', fontSize: 16, fontWeight: '800' },
  
  scrollContent: { padding: 20, paddingBottom: 50 },
  
  infoSection: { alignItems: 'center', marginBottom: 25, marginTop: 10 },
  iconCircle: { width: 60, height: 60, borderRadius: 30, backgroundColor: '#fff1f2', justifyContent: 'center', alignItems: 'center', marginBottom: 12 },
  mapelName: { fontSize: 20, fontWeight: '800', textAlign: 'center', color: '#1e293b' },
  weightBadge: { backgroundColor: '#f1f5f9', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 8, marginTop: 8, borderWidth: 1, borderColor: '#e2e8f0' },
  weightText: { fontSize: 11, fontWeight: '700', color: '#64748b' },
  
  scoreCard: { 
    alignItems: 'center', 
    padding: 30, 
    backgroundColor: '#fff', 
    borderWidth: 1, 
    borderColor: '#e2e8f0', 
    borderRadius: 20, 
    marginBottom: 20,
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 3
  },
  scoreLabel: { fontSize: 12, fontWeight: '800', color: '#94a3b8', letterSpacing: 1.5 },
  scoreValue: { fontSize: 75, fontWeight: '900', marginVertical: 5 },
  statusBadge: { paddingHorizontal: 14, paddingVertical: 6, borderRadius: 12, borderWidth: 1 },
  statusBadgeText: { fontSize: 10, fontWeight: '800', letterSpacing: 0.5 },
  
  table: { 
    backgroundColor: '#fff', 
    borderRadius: 16, 
    marginBottom: 25,
    borderWidth: 1, 
    borderColor: '#e2e8f0',
  },
  tableRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 18, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  tableLabel: { color: '#475569', fontWeight: '600', fontSize: 14 },
  tableValue: { fontWeight: '800', color: '#1e293b', fontSize: 16 },
  
  actionContainer: { gap: 12, marginTop: 10 },
  
  btnReview: { 
    backgroundColor: '#fff1f2', 
    borderWidth: 1.5, 
    borderColor: '#c91313', 
    padding: 16, 
    borderRadius: 14, 
    flexDirection: 'row', 
    justifyContent: 'center', 
    alignItems: 'center', 
    gap: 8 
  },
  btnReviewText: { color: '#c91313', fontWeight: '800', fontSize: 14 },
  
  btnFinish: { 
    backgroundColor: '#c91313', 
    padding: 16, 
    borderRadius: 14, 
    alignItems: 'center' 
  },
  btnFinishText: { color: '#fff', fontWeight: '800', fontSize: 14 },
  
  errorContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', marginTop: 50 },
  errorText: { textAlign: 'center', color: '#64748b', marginTop: 15, marginBottom: 25, fontSize: 16, fontWeight: '600' }
});