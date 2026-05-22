import React, { useEffect, useState } from 'react';
import { 
  View, Text, StyleSheet, ActivityIndicator, TouchableOpacity, 
  ScrollView, StatusBar, SafeAreaView 
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig';
import { Ionicons } from '@expo/vector-icons';

export default function RekapScreen() {
  const params = useLocalSearchParams();
  const id = params.id;
  const token = params.token;
  
  const router = useRouter();
  const [result, setResult] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState("");

  const primaryRed = '#c91313'; 

  useEffect(() => {
    console.log("DEBUG_REKAP_PARAMS:", { id, token });

    if (id && id !== "undefined") {
      fetchData();
    } else {
      setLoading(false);
      setErrorMsg("ID Ujian tidak ditemukan di parameter navigasi.");
    }
  }, [id, token]);

  const fetchData = async () => {
    try {
      setLoading(true);
      setErrorMsg("");
      
      const res = await api.get(`/ujian/${id}/hasil`, { 
        headers: { 'X-Exam-Token': token } 
      });
      
      if (res.data && res.data.success) {
        setResult(res.data.data);
      } else {
        setErrorMsg("Gagal memproses data dari server.");
      }
    } catch (e: any) {
      console.error("API_ERROR:", e.response?.data || e.message);
      setErrorMsg(e.response?.data?.message || "Koneksi ke server bermasalah.");
    } finally {
      setLoading(false);
    }
  };

  // FUNGSI DINAMIS PENENTU WARNA KATEGORI NILAI (MERAH, KUNING, HIJAU)
  const getColorByScore = (score: number) => {
    const numScore = Number(score);
    if (numScore < 60) return '#dc2626'; // Merah (Kurang)
    if (numScore < 75) return '#00aaf9'; // Oranye/Kuning (Cukup)
    return '#16a34a'; // Hijau (Bagus)
  };

  if (loading) return (
    <View style={styles.center}>
      <ActivityIndicator size="large" color={primaryRed} />
      <Text style={{marginTop: 10, color: '#64748b', fontWeight: '600'}}>Memuat Hasil...</Text>
    </View>
  );

  if (errorMsg || !result) return (
    <View style={styles.center}>
      <Ionicons name="alert-circle" size={60} color="#cbd5e1" />
      <Text style={styles.errorText}>{errorMsg || "Data tidak ditemukan"}</Text>
      <TouchableOpacity onPress={() => router.replace('/(tabs)')} style={styles.btnError}>
        <Text style={{color: '#fff', fontWeight: '700'}}>Kembali ke Beranda</Text>
      </TouchableOpacity>
    </View>
  );

  const isGraded = result.is_complete;
  
  // DETEKSI LOGIKA ADANYA SOAL PG BERDASARKAN BOBOT NYA
  const hasSoalPG = Number(result.breakdown?.weight_pg) > 0;

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />
      
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.headerBtn}>
          <Ionicons name="arrow-back" size={24} color="#1e293b" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Hasil Ujian</Text>
        <View style={{ width: 40 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.infoSection}>
          <Text style={styles.mapelName}>{result.subject_name}</Text>
          <View style={styles.weightBadge}>
             <Text style={styles.weightText}>
               Bobot: {result.breakdown?.weight_pg}% PG + {result.breakdown?.weight_essay}% Essay
             </Text>
          </View>
        </View>

        {/* SCORE CARD DENGAN WARNA WARNI INDIKATOR KELULUSAN */}
        <View style={styles.scoreCard}>
          <Text style={styles.scoreLabel}>NILAI AKHIR</Text>
          
          <Text style={[
            styles.scoreValue, 
            { color: isGraded ? getColorByScore(result.final_score) : '#94a3b8' }
          ]}>
            {isGraded ? result.final_score : '--'}
          </Text>
          
          <View style={[styles.statusBadge, { backgroundColor: isGraded ? '#f0fdf4' : '#fffbeb' }]}>
            <Text style={[styles.statusBadgeText, { color: isGraded ? '#166534' : '#854d0e' }]}>
              {isGraded ? 'PENILAIAN SELESAI' : 'MENUNGGU KOREKSI ESSAY'}
            </Text>
          </View>
        </View>

        {/* TABEL DETAIL SKOR */}
        <View style={styles.table}>
          
          {/* BARIS PILIHAN GANDA */}
          <View style={styles.tableRow}>
            <Text style={styles.tableLabel}>Skor Pilihan Ganda</Text>
            <Text style={[
              styles.tableValue, 
              !hasSoalPG && { color: '#94a3b8', fontSize: 16 } // Jika tidak ada PG, buat abu-abu lambang strip-nya
            ]}>
              {hasSoalPG ? result.breakdown?.score_pg : '-'}
            </Text>
          </View>
          
          {/* BARIS ESSAY */}
          <View style={[styles.tableRow, { borderBottomWidth: 0 }]}>
            <Text style={styles.tableLabel}>Skor Essay (Guru)</Text>
            <Text style={styles.tableValue}>
              {isGraded ? result.breakdown?.score_essay : '...'}
            </Text>
          </View>

        </View>

        <TouchableOpacity 
          style={styles.btnFinish} 
          onPress={() => router.replace('/(tabs)')}
        >
          <Text style={styles.btnFinishText}>Selesai & Kembali</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 40 },
  header: { height: 60, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 15, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  headerBtn: { width: 40, height: 40, justifyContent: 'center', alignItems: 'center' },
  headerTitle: { fontSize: 16, fontWeight: '700', color: '#1e293b' },
  scrollContent: { padding: 25 },
  infoSection: { alignItems: 'center', marginBottom: 25 },
  mapelName: { fontSize: 24, fontWeight: '900', color: '#0f172a', textAlign: 'center' },
  weightBadge: { backgroundColor: '#f1f5f9', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 8, marginTop: 10 },
  weightText: { fontSize: 11, fontWeight: '700', color: '#64748b', textTransform: 'uppercase' },
  
  scoreCard: { backgroundColor: '#fff', borderRadius: 28, padding: 35, alignItems: 'center', borderWidth: 1, borderColor: '#f1f5f9', marginBottom: 25, elevation: 3, shadowColor: '#000', shadowOpacity: 0.04, shadowRadius: 15 },
  scoreLabel: { fontSize: 11, fontWeight: '800', color: '#94a3b8', letterSpacing: 2 },
  scoreValue: { fontSize: 84, fontWeight: '900', marginVertical: 10 },
  statusBadge: { paddingHorizontal: 14, paddingVertical: 6, borderRadius: 12 },
  statusBadgeText: { fontSize: 10, fontWeight: '800' },
  
  table: { backgroundColor: '#f8fafc', borderRadius: 20, padding: 10, marginBottom: 25, borderWidth: 1, borderColor: '#f1f5f9' },
  tableRow: { flexDirection: 'row', justifyContent: 'space-between', padding: 18, borderBottomWidth: 1, borderBottomColor: '#f1f5f9', alignItems: 'center' },
  tableLabel: { fontSize: 14, color: '#64748b', fontWeight: '600' },
  tableValue: { fontSize: 15, fontWeight: '800', color: '#1e293b' },
  
  btnFinish: { backgroundColor: '#1e293b', padding: 18, borderRadius: 16, alignItems: 'center', marginTop: 10, marginBottom: 40 },
  btnFinishText: { color: '#fff', fontWeight: '800', fontSize: 15 },
  errorText: { textAlign: 'center', marginTop: 15, color: '#64748b', fontSize: 14 },
  btnError: { backgroundColor: '#c91313', paddingVertical: 12, paddingHorizontal: 25, borderRadius: 12, marginTop: 25 }
});