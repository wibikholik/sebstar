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

// Keluar 2 tingkat: dari (auth) -> app -> root, lalu masuk ke src
import api from '../../src/api/axiosConfig'; 

export default function LoginScreen() {
  const [nis, setNis] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const router = useRouter();

  const handleLogin = async () => {
    // 1. Validasi input sederhana
    if (!nis || !password) {
      Alert.alert('Peringatan', 'NIS dan Password harus diisi');
      return;
    }

    setLoading(true);

    try {
      // 2. Request ke endpoint login di Laravel backend
      const response = await api.post('/login', {
        nis: nis,
        password: password,
      });

      // Simpan token ke storage
      const token = response.data.access_token;
      await AsyncStorage.setItem('userToken', token);

      console.log("Login Berhasil!");
      
      // 3. Arahkan ke halaman utama (tabs)
      router.replace('/(tabs)'); 

    } catch (error: any) {
      setLoading(false);
      
      if (error.response) {
        // Error dari server (misal: 401 Unauthorized)
        Alert.alert('Login Gagal', error.response.data.message || 'NIS atau Password salah');
      } else if (error.request) {
        // Tidak ada respon dari server
        Alert.alert('Gagal', 'Server tidak merespons. Pastikan IP di axiosConfig sudah benar.');
      } else {
        // Error lainnya
        Alert.alert('Error', 'Terjadi kesalahan sistem');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <View style={styles.formContainer}>
        <Text style={styles.title}>SEBSTAR</Text>
        <Text style={styles.subtitle}>Selamat Datang di Aplikasi Ujian</Text>

        <TextInput 
          style={styles.input} 
          placeholder="Masukkan NIS" 
          value={nis} 
          onChangeText={setNis}
          keyboardType="numeric"
          autoCapitalize="none"
        />
        
        <TextInput 
          style={styles.input} 
          placeholder="Masukkan Password" 
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
        
        <Text style={styles.footerText}>Ver. 1.0 - SMKN 1 Binong</Text>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    justifyContent: 'center', 
    backgroundColor: '#f5f5f5' 
  },
  formContainer: {
    padding: 25,
    margin: 20,
    backgroundColor: '#fff',
    borderRadius: 20,
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 10,
  },
  title: { 
    fontSize: 32, 
    fontWeight: 'bold', 
    textAlign: 'center', 
    color: '#1a73e8',
    letterSpacing: 2
  },
  subtitle: { 
    fontSize: 14, 
    textAlign: 'center', 
    marginBottom: 30, 
    color: '#666',
    marginTop: 5
  },
  input: { 
    borderWidth: 1, 
    borderColor: '#e0e0e0', 
    padding: 15, 
    marginBottom: 15, 
    borderRadius: 12,
    backgroundColor: '#fafafa',
    fontSize: 16
  },
  button: { 
    backgroundColor: '#1a73e8', 
    padding: 16, 
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 10
  },
  buttonDisabled: { 
    backgroundColor: '#a0c4ff' 
  },
  buttonText: { 
    color: '#fff', 
    fontSize: 18, 
    fontWeight: 'bold' 
  },
  footerText: {
    textAlign: 'center',
    marginTop: 20,
    color: '#999',
    fontSize: 12
  }
});