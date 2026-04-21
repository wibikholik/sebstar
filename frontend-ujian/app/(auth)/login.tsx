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
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';

export default function LoginScreen() {
  const [nis, setNis] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const router = useRouter();

  const handleLogin = async () => {
    // Validasi input
    if (!nis || !password) {
      Alert.alert('Peringatan', 'NIS dan Password harus diisi');
      return;
    }

    setLoading(true);
    console.log("Mencoba login dengan NIS:", nis); // Log untuk debug

    try {
      const response = await axios.post('http://10.253.108.247:8000/api/login', {
        nis: nis,
        password: password,
      }, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        timeout: 10000 // Timeout 10 detik agar tidak "gantung"
      });

      console.log("Login Berhasil! Token diterima.");

      // Simpan token ke storage HP
      await AsyncStorage.setItem('userToken', response.data.access_token);
      
      // Beri jeda sedikit agar navigasi lebih stabil
      setTimeout(() => {
        router.replace('/(tabs)'); 
      }, 500);

    } catch (error: any) {
      setLoading(false);
      
      // Log detail error ke terminal untuk membantumu melacak masalah
      if (error.response) {
        console.log("Error Status:", error.response.status);
        console.log("Error Data:", error.response.data);
        Alert.alert('Login Gagal', error.response.data.message || 'NIS/Password salah');
      } else if (error.request) {
        console.log("Error Request:", error.request);
        Alert.alert('Gagal', 'Server tidak merespons. Cek IP/Wi-Fi.');
      } else {
        console.log("Error:", error.message);
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
    elevation: 5, // Android shadow
    // Menggunakan syntax shadow standar agar tidak ada warning deprecated
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