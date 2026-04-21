import { useEffect, useState, useRef } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator, Alert } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';

export default function DashboardScreen() {
  const [jadwal, setJadwal] = useState([]);
  const [loading, setLoading] = useState(true);
  const router = useRouter();
  
  // Ref untuk memastikan fetch hanya jalan sekali dan tidak looping
  const isFetching = useRef(false);

  useEffect(() => {
    fetchJadwal();
  }, []);

  const fetchJadwal = async () => {
    if (isFetching.current) return; // Mencegah looping
    isFetching.current = true;
    
    try {
      setLoading(true);
      const token = await AsyncStorage.getItem('userToken');
      
      const response = await axios.get('http://10.253.108.247:8000/api/jadwal', {
        headers: { 
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json' 
        }
      });

      setJadwal(response.data.data || response.data); 
      setLoading(false);
    } catch (error: any) {
      setLoading(false);
      // Jika 401, bersihkan token dan lempar ke login
      if (error.response?.status === 401) {
        console.log("Token expired/invalid, logout paksa.");
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
    // Gunakan replace agar tidak kembali ke halaman sebelumnya
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
            <Text style={styles.mapel}>ID Mapel: {item.subject_id}</Text>
            <Text style={styles.info}>Jam: {item.jam_mulai} - {item.jam_selesai}</Text>
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

// ... styles tetap sama

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, backgroundColor: '#f8f9fa' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 20, color: '#333' },
  card: { 
    backgroundColor: '#fff', 
    padding: 15, 
    borderRadius: 12, 
    marginBottom: 15,
    // Box Shadow modern
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3 
  },
  headerCard: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 10 },
  mapel: { fontSize: 16, fontWeight: '700' },
  info: { fontSize: 14, color: '#555', marginBottom: 4 },
  badge: { paddingHorizontal: 8, paddingVertical: 4, borderRadius: 5 },
  btnMulai: { padding: 12, borderRadius: 8, alignItems: 'center', marginTop: 15 },
  btnLogout: { marginTop: 20, backgroundColor: '#dc3545', padding: 15, borderRadius: 10, alignItems: 'center' }
});