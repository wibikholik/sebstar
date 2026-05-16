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
  Platform,
  StatusBar
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';

// Konfigurasi API Axios kamu
import api from '../../src/api/axiosConfig'; 

export default function LoginScreen() {
  const [nis, setNis] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const router = useRouter();

  const handleLogin = async () => {
    if (!nis || !password) {
      Alert.alert('Peringatan', 'NIS dan Password harus diisi');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/login', {
        nis: nis,
        password: password,
      });

      const token = response.data.access_token;
      await AsyncStorage.setItem('userToken', token);

      console.log("Login Berhasil!");
      router.replace('/(tabs)'); 

    } catch (error: any) {
      console.log("Login Error Log:", error.response?.status, error.response?.data);

      if (error.response) {
        const statusCode = error.response.status;
        const serverMessage = error.response.data.message;

        if (statusCode === 403) {
          Alert.alert(
            'Akses Ditolak', 
            serverMessage || 'Akun Anda terdeteksi sedang aktif di perangkat lain. Silahkan logout terlebih dahulu atau hubungi proktor.'
          );
        } else if (statusCode === 401) {
          Alert.alert('Login Gagal', serverMessage || 'NIS atau Password salah');
        } else {
          Alert.alert('Gagal', serverMessage || `Terjadi kesalahan pada server (Error: ${statusCode})`);
        }
      } else if (error.request) {
        Alert.alert('Gagal Jaringan', 'Server tidak merespons. Pastikan IP di axiosConfig sudah benar dan PC Server menyala.');
      } else {
        Alert.alert('Error', 'Terjadi kesalahan sistem internal.');
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
      <StatusBar barStyle="dark-content" backgroundColor="#f8fafc" />
      
      {/* Ornamen Estetik Latar Belakang */}
      <View style={[styles.circleDecor, { top: -100, right: -100, backgroundColor: '#dbeafe' }]} />
      <View style={[styles.circleDecor, { bottom: -150, left: -100, backgroundColor: '#eff6ff' }]} />

      <View style={styles.headerSection}>
        <Text style={styles.brandTitle}>SEB<Text style={styles.brandAccent}>STAR</Text></Text>
        <Text style={styles.brandSubtitle}>Computer Assisted Test Platform</Text>
      </View>

      <View style={styles.formContainer}>
        <Text style={styles.loginHeader}>Silahkan Masuk</Text>
        <Text style={styles.loginSubheader}>Gunakan nomor induk siswa resmi dari sekolah</Text>

        {/* Input NIS */}
        <Text style={styles.inputLabel}>Nomor Induk Siswa (NIS)</Text>
        <View style={styles.inputWrapper}>
          <Text style={styles.inputIcon}>👤</Text>
          <TextInput 
            style={styles.input} 
            placeholder="Contoh: 212210234" 
            placeholderTextColor="#94a3b8"
            value={nis} 
            onChangeText={setNis}
            keyboardType="numeric"
            autoCapitalize="none"
          />
        </View>
        
        {/* Input Password */}
        <Text style={styles.inputLabel}>Kata Sandi</Text>
        <View style={styles.inputWrapper}>
          <Text style={styles.inputIcon}>🔒</Text>
          <TextInput 
            style={styles.input} 
            placeholder="Masukkan kata sandi Anda" 
            placeholderTextColor="#94a3b8"
            value={password} 
            onChangeText={setPassword}
            secureTextEntry 
            autoCapitalize="none"
          />
        </View>

        {/* Tombol Masuk */}
        <TouchableOpacity 
          style={[styles.button, loading && styles.buttonDisabled]} 
          onPress={handleLogin}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>MASUK KE UJIAN</Text>
          )}
        </TouchableOpacity>
      </View>

      <View style={styles.footerContainer}>
        <Text style={styles.footerText}>Secure Exam Browser Node</Text>
        <Text style={styles.schoolText}>SMKN 1 BINONG</Text>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    justifyContent: 'center', 
    backgroundColor: '#f8fafc', // Warna dasar putih abu slate yang bersih
    paddingHorizontal: 20
  },
  circleDecor: {
    position: 'absolute',
    width: 300,
    height: 300,
    borderRadius: 150,
    opacity: 0.6,
  },
  headerSection: {
    alignItems: 'center',
    marginBottom: 35
  },
  brandTitle: { 
    fontSize: 40, 
    fontWeight: '900', 
    color: '#1e293b',
    letterSpacing: 3,
  },
  brandAccent: {
    color: '#2563eb' // Warna biru aksen cerah
  },
  brandSubtitle: { 
    fontSize: 13, 
    color: '#64748b',
    marginTop: 6,
    textTransform: 'uppercase',
    letterSpacing: 1.5,
    fontWeight: '600'
  },
  formContainer: {
    padding: 28,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    elevation: 8,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.06,
    shadowRadius: 20,
    borderWidth: 1,
    borderColor: '#f1f5f9'
  },
  loginHeader: {
    fontSize: 20,
    fontWeight: '700',
    color: '#0f172a'
  },
  loginSubheader: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 4,
    marginBottom: 24,
    lineHeight: 18
  },
  inputLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: '#475569',
    marginBottom: 6,
    textTransform: 'uppercase',
    letterSpacing: 0.5
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1.5,
    borderColor: '#e2e8f0',
    borderRadius: 14,
    backgroundColor: '#f8fafc',
    marginBottom: 18,
    paddingHorizontal: 14,
  },
  inputIcon: {
    fontSize: 16,
    marginRight: 10,
    color: '#64748b'
  },
  input: { 
    flex: 1,
    paddingVertical: 14, 
    fontSize: 15,
    color: '#0f172a',
    fontWeight: '500'
  },
  button: { 
    backgroundColor: '#2563eb', 
    padding: 16, 
    borderRadius: 14,
    alignItems: 'center',
    marginTop: 10,
    shadowColor: '#2563eb',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 3
  },
  buttonDisabled: { 
    backgroundColor: '#93c5fd',
    shadowOpacity: 0
  },
  buttonText: { 
    color: '#fff', 
    fontSize: 15, 
    fontWeight: '700', 
    letterSpacing: 1
  },
  footerContainer: {
    position: 'absolute',
    bottom: 30,
    left: 0,
    right: 0,
    alignItems: 'center'
  },
  footerText: {
    color: '#94a3b8',
    fontSize: 12,
    fontWeight: '500'
  },
  schoolText: {
    color: '#64748b',
    fontSize: 13,
    fontWeight: '700',
    marginTop: 2,
    letterSpacing: 1
  }
});