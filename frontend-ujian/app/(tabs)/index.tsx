import { useEffect, useState, useCallback } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator, Alert, RefreshControl } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig'; // Sesuaikan path ini

export default function DashboardScreen() {
  const [jadwal, setJadwal] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const router = useRouter();

  useEffect(() => {
    fetchJadwal();
  }, []);

  const fetchJadwal = async () => {
    try {
      const response = await api.get('/jadwal');
      // Pastikan backend mengembalikan array data
      setJadwal(response.data.data || response.data);
    } catch (error: any) {
      if (error.response?.status === 401) {
        handleLogout();
      } else {
        Alert.alert('Gagal', 'Gagal memuat jadwal. Cek koneksi Anda.');
      }
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = useCallback(() => {
    setRefreshing(true);
    fetchJadwal();
  }, []);

  const handleLogout = async () => {
    await AsyncStorage.removeItem('userToken');
    router.replace('/(auth)/login');
  };

  const renderJadwal = ({ item }: { item: any }) => {
    // 1. Logika Status
    const isFinished = item.is_finished === true || item.is_finished === 1;
    const isActive = item.status === 'aktif' && !isFinished;
    
    // 2. Styling Dinamis
    const statusColor = isFinished ? '#28a745' : (isActive ? '#007bff' : '#6c757d');
    const statusBg = isFinished ? '#d4edda' : (isActive ? '#cce5ff' : '#e2e3e5');

    return (
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Text style={styles.mapel}>{item.subject?.nama_mapel ?? 'Mata Pelajaran'}</Text>
          <View style={[styles.badge, { backgroundColor: statusBg }]}>
            <Text style={{ color: statusColor, fontWeight: 'bold', fontSize: 11 }}>
              {isFinished ? 'SELESAI' : item.status.toUpperCase()}
            </Text>
          </View>
        </View>

        <Text style={styles.info}>Kelas: {item.classroom?.nama_kelas ?? '-'}</Text>
        <Text style={styles.info}>
          Guru: {item.teachers_data && item.teachers_data.length > 0 
                  ? item.teachers_data.map((t: any) => t.name).join(', ') 
                  : 'Tidak ada data'}
        </Text>
        
        <View style={styles.divider} />
        <Text style={styles.timeText}>📅 {item.tanggal_ujian} | ⏰ {item.jam_mulai} - {item.jam_selesai}</Text>

        {/* --- Tombol Aksi Dinamis --- */}
        <TouchableOpacity 
          style={[
            styles.btnMulai, 
            { backgroundColor: isActive ? '#007bff' : (isFinished ? '#28a745' : '#ccc') }
          ]}
          onPress={() => {
            if (isActive) {
              // Navigasi ke Halaman Pengerjaan Ujian
              router.push({
                pathname: '/ujian/[id]', 
                params: { id: item.id.toString(), token: item.token }
              });
            } else if (isFinished) {
              // Navigasi ke Halaman Rekap Hasil
              router.push({
                pathname: '/ujian/rekap', 
                params: { id: item.id.toString(), token: item.token }
              });
            }
          }}
        >
          <Text style={styles.btnText}>
            {isFinished ? 'LIHAT HASIL' : (isActive ? 'MULAI UJIAN' : 'MENUNGGU')}
          </Text>
        </TouchableOpacity>
      </View>
    );
  };

  if (loading) return <View style={styles.center}><ActivityIndicator size="large" color="#007bff" /></View>;

  return (
    <View style={styles.container}>
      <View style={styles.headerContainer}>
        <Text style={styles.title}>Jadwal Ujian</Text>
        <TouchableOpacity onPress={handleLogout}>
            <Text style={styles.logoutText}>Logout</Text>
        </TouchableOpacity>
      </View>

      <FlatList
        data={jadwal}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderJadwal}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<Text style={styles.emptyText}>Tidak ada jadwal tersedia.</Text>}
        contentContainerStyle={{ paddingBottom: 20 }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8f9fa', padding: 20 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  headerContainer: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  title: { fontSize: 24, fontWeight: 'bold', color: '#333' },
  logoutText: { color: '#dc3545', fontWeight: 'bold' },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 16, marginBottom: 15, elevation: 3, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 8 },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 10 },
  mapel: { fontSize: 18, fontWeight: '800', color: '#2c3e50', flex: 1 },
  info: { fontSize: 14, color: '#666', marginBottom: 4 },
  divider: { height: 1, backgroundColor: '#eee', marginVertical: 10 },
  timeText: { fontSize: 13, color: '#333', fontWeight: '500' },
  badge: { paddingHorizontal: 12, paddingVertical: 4, borderRadius: 20, marginLeft: 10 },
  btnMulai: { padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 15 },
  btnText: { color: '#fff', fontWeight: 'bold' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#999' }
});