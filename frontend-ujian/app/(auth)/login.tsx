import React, { useState } from 'react';
import { 
  View, 
  TextInput, 
  TouchableOpacity, 
  Text, 
  StyleSheet, 
  Alert, 
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform 
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';

// SESUAIKAN PATH INI dengan lokasi file axiosConfig.ts kamu
import api from '../../src/api/axiosConfig'; 

export default function LoginScreen() {
  const [nis, setNis] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const router = useRouter();

  const handleLogin = async () => {
    // 1. Validasi input
    if (!nis || !password) {
      Alert.alert('Peringatan', 'NIS dan Password harus diisi');
      return;
    }

    setLoading(true);

    try {
      // 2. Request API menggunakan instance 'api'
      // URL sudah otomatis menggunakan baseURL dari .env
      const response = await api.post('/login', {
        nis: nis,
        password: password,
      });

      console.log("Login Berhasil!");

      // 3. Simpan token
      await AsyncStorage.setItem('userToken', response.data.access_token);
      
      // 4. Navigasi
      router.replace('/(tabs)'); 

    } catch (error: any) {
      setLoading(false);
      
      // 5. Error handling yang informatif
      if (error.response) {
        // Server merespon dengan status selain 2xx
        console.log("Error Status:", error.response.status);
        Alert.alert('Login Gagal', error.response.data.message || 'NIS/Password salah');
      } else if (error.request) {
        // Request dibuat tapi tidak ada respon (cek koneksi/IP)
        console.log("Error Request:", error.request);
        Alert.alert('Gagal', 'Server tidak merespons. Pastikan HP dan Laptop di Wi-Fi yang sama.');
      } else {
        Alert.alert('Error', 'Terjadi kesalahan sistem');
      }
    }
  };

  return (
    <KeyboardAvoidingView 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <View style={styles.formContainer}>
        <Text style={styles.title}>Sebstar Login</Text>
        <Text style={styles.subtitle}>Masukkan NIS dan Password</Text>

        <TextInput 
          style={styles.input} 
          placeholder="NIS" 
          value={nis} 
          onChangeText={setNis}
          keyboardType="numeric"
          autoCapitalize="none"
        />
        
        <TextInput 
          style={styles.input} 
          placeholder="Password" 
          value={password} 
          onChangeText={setPassword}
          secureTextEntry 
          autoCapitalize="none"
        />

        <TouchableOpacity 
          style={[styles.button, loading && styles.buttonDisabled]} 
          onPress={handleLogin}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>MASUK</Text>
          )}
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    justifyContent: 'center', 
    backgroundColor: '#f8f9fa' 
  },
  formContainer: {
    padding: 25,
    margin: 20,
    backgroundColor: '#fff',
    borderRadius: 15,
    elevation: 5,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  title: { fontSize: 28, fontWeight: 'bold', textAlign: 'center', marginBottom: 10, color: '#333' },
  subtitle: { fontSize: 14, textAlign: 'center', marginBottom: 30, color: '#666' },
  input: { 
    borderWidth: 1, 
    borderColor: '#ddd', 
    padding: 15, 
    marginBottom: 15, 
    borderRadius: 10,
    backgroundColor: '#fff',
    fontSize: 16
  },
  button: { 
    backgroundColor: '#007bff', 
    padding: 15, 
    borderRadius: 10,
    alignItems: 'center',
    marginTop: 10
  },
  buttonDisabled: { backgroundColor: '#99ccff' },
  buttonText: { color: '#fff', fontSize: 16, fontWeight: 'bold' }
});