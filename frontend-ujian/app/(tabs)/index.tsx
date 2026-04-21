import { useEffect, useState, useRef } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator, Alert } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';

// Pastikan path ini benar sesuai struktur foldermu
import api from '../../src/api/axiosConfig'; 

export default function DashboardScreen() {
  const [jadwal, setJadwal] = useState([]);
  const [loading, setLoading] = useState(true);
  const router = useRouter();
  
  const isFetching = useRef(false);

  useEffect(() => {
    fetchJadwal();
  }, []);

  const fetchJadwal = async () => {
    if (isFetching.current) return;
    isFetching.current = true;
    
    try {
      setLoading(true);
      // Asumsi endpoint /jadwal mengembalikan array data
      const response = await api.get('/jadwal'); 
      setJadwal(response.data.data || response.data); 
      setLoading(false);
    } catch (error: any) {
      setLoading(false);
      if (error.response?.status === 401) {
        await handleLogout();
      } else {
        Alert.alert('Gagal', 'Server error. Cek koneksi.');
      }
    } finally {
      isFetching.current = false;
    }
  };

  const handleLogout = async () => {
    await AsyncStorage.removeItem('userToken');
    router.replace('/(auth)/login');
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#007bff" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Jadwal Ujian</Text>
     <FlatList
  data={jadwal}
  keyExtractor={(item) => item.id.toString()}
  renderItem={({ item }) => (
    <View style={styles.card}>
      {/* Akses nama mapel dari relasi subject */}
      <Text style={styles.mapel}>
        Mapel: {item.subject ? item.subject.nama_mapel : 'Tidak ada data'}
      </Text>

      {/* Akses nama kelas dari relasi classroom */}
      <Text style={styles.info}>
        Kelas: {item.classroom ? item.classroom.nama_kelas : 'Tidak ada data'}
      </Text>

      {/* Menampilkan daftar guru dari data yang kita map di controller */}
      <Text style={styles.info}>
        Guru: {item.teachers_data && item.teachers_data.length > 0 
                ? item.teachers_data.map(t => t.name).join(', ') 
                : 'Tidak ada guru'}
      </Text>

      <Text style={styles.info}>Tanggal: {item.tanggal_ujian}</Text>
      <Text style={styles.info}>Jam: {item.jam_mulai} - {item.jam_selesai}</Text>
            
            {/* Badge Status */}
            <View style={[styles.badge, { backgroundColor: item.status === 'aktif' ? '#d1e7dd' : '#f8d7da' }]}>
                <Text style={{color: item.status === 'aktif' ? '#0f5132' : '#842029'}}>
                    Status: {item.status}
                </Text>
            </View>

            <TouchableOpacity 
              style={[styles.btnMulai, { backgroundColor: item.status === 'aktif' ? '#007bff' : '#ccc' }]}
              disabled={item.status !== 'aktif'}
              onPress={() => router.push(`/ujian/${item.id}`)}
            >
              <Text style={{color: '#fff', fontWeight: 'bold'}}>
                {item.status === 'aktif' ? 'MULAI UJIAN' : 'MENUNGGU'}
              </Text>
            </TouchableOpacity>
          </View>
        )}
      />
      <TouchableOpacity style={styles.btnLogout} onPress={handleLogout}>
        <Text style={{color: '#fff'}}>LOGOUT</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, backgroundColor: '#f8f9fa' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 20, color: '#333' },
  card: { 
    backgroundColor: '#fff', 
    padding: 15, 
    borderRadius: 12, 
    marginBottom: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3 
  },
  mapel: { fontSize: 16, fontWeight: '700', marginBottom: 5 },
  info: { fontSize: 14, color: '#555', marginBottom: 4 },
  badge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 15, alignSelf: 'flex-start', marginVertical: 10 },
  btnMulai: { padding: 12, borderRadius: 8, alignItems: 'center', marginTop: 15 },
  btnLogout: { marginTop: 20, backgroundColor: '#dc3545', padding: 15, borderRadius: 10, alignItems: 'center' }
});