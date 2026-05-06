// app/ujian/[id].tsx
import { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, ActivityIndicator, StyleSheet } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig'; // Pastikan path benar

export default function TokenScreen() {
  const { id } = useLocalSearchParams(); // Menangkap ID dari URL
  const [token, setToken] = useState('');
  const [loading, setLoading] = useState(false);
  const router = useRouter();

  const handleVerifyToken = async () => {
    if (!token) return Alert.alert('Error', 'Masukkan token ujian!');
    
    setLoading(true);
    try {
      // Kirim token ke backend untuk divalidasi
      await api.post(`/ujian/${id}/verify-token`, { token });
      
      // Jika sukses, navigasi ke halaman kerjakan
      router.replace({
        pathname: '/ujian/petunjuk',
        params: { id, token } // Kirim id dan token ke halaman berikutnya
      });
    } catch (e: any) {
      Alert.alert('Gagal', e.response?.data?.message || 'Token tidak valid');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Verifikasi Token</Text>
      <TextInput 
        style={styles.input}
        placeholder="Masukkan Token Ujian"
        value={token}
        onChangeText={setToken}
        autoCapitalize="characters"
      />
      <TouchableOpacity 
        style={styles.button} 
        onPress={handleVerifyToken} 
        disabled={loading}
      >
        {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.btnText}>MASUK UJIAN</Text>}
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, justifyContent: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 20, textAlign: 'center' },
  input: { borderWidth: 1, borderColor: '#ccc', padding: 15, borderRadius: 10, marginBottom: 20, fontSize: 16 },
  button: { backgroundColor: '#007bff', padding: 15, borderRadius: 10, alignItems: 'center' },
  btnText: { color: '#fff', fontWeight: 'bold' }
});