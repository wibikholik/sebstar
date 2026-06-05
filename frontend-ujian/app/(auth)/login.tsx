import React, { useState } from 'react';
import {
  View,
  TextInput,
  TouchableOpacity,
  Text,
  StyleSheet,
  Alert,
  ActivityIndicator,
  Platform,
  StatusBar,
  Image,
  ScrollView,
  KeyboardAvoidingView
} from 'react-native';

import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import { User, Lock, LogIn, Info } from 'lucide-react-native';

import api from '../../src/api/axiosConfig';

export default function LoginScreen() {
  const [nis, setNis] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const [nisFocused, setNisFocused] = useState(false);
  const [passwordFocused, setPasswordFocused] = useState(false);

  const router = useRouter();

  const showAlert = (title: string, message: string) => {
    if (Platform.OS === 'web') {
      alert(`${title}\n\n${message}`);
    } else {
      Alert.alert(title, message);
    }
  };

  const handleLogin = async () => {
    if (!nis || !password) {
      showAlert('Peringatan', 'NIS dan Password harus diisi');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/login', { nis, password });
      const token = response.data.access_token;
      
      if (Platform.OS === 'web') {
        localStorage.setItem('userToken', token);
      } else {
        await AsyncStorage.setItem('userToken', token);
      }
      
      router.replace('/(tabs)');

    } catch (error: any) {
      const statusCode = error.response?.status;
      if (statusCode === 401 || statusCode === 403 || statusCode === 422) {
        showAlert('Login Gagal', 'NIS atau Password yang kamu masukkan salah.');
      } else {
        showAlert('Masalah Koneksi', 'Tidak dapat memproses login. Pastikan server menyala.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView 
      style={styles.container} 
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <StatusBar barStyle="dark-content" backgroundColor="#f4f5f9" />

      {/* REPRODUKSI POLKADOT GRID Khas Web ke Mobile (Menggunakan Dot Kecil Berulang) */}
      <View style={styles.dotGridContainer}>
        {Array.from({ length: 45 }).map((_, i) => (
          <View key={i} style={styles.gridDot} />
        ))}
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="always"
        showsVerticalScrollIndicator={false}
        bounces={false}
      >
        {/* LOGIN HEADER */}
        <View style={styles.header}>
          <Image
            source={require('../../assets/images/icon.png')}
            style={styles.logo}
            resizeMode="contain"
          />
          <Text style={styles.title}>SEBSTAR</Text>
          <Text style={styles.subtitle}>Sistem Ujian Digital</Text>
        </View>

        {/* CONTAINER FORM FLAT (Rata Layar, Bebas Intercept Touch Android) */}
        <View style={styles.formContainer}>
          
          {/* INPUT NIS */}
          <Text style={[styles.inputLabel, nisFocused && styles.labelFocused]}>NOMOR INDUK SISWA (NIS)</Text>
          <View style={[styles.inputGroup, nisFocused && styles.inputFocused]}>
            <User size={18} color={nisFocused ? '#cd0000' : '#a0a0b0'} style={styles.icon} />
            <TextInput
              style={styles.input}
              placeholder="Masukkan NIS Anda"
              placeholderTextColor="#a0a0b0"
              value={nis}
              onChangeText={setNis}
              keyboardType="numeric"
              onFocus={() => setNisFocused(true)}
              onBlur={() => setNisFocused(false)}
              returnKeyType="next"
              disableFullscreenKeyboard={true}
            />
          </View>

          {/* INPUT PASSWORD */}
          <Text style={[styles.inputLabel, passwordFocused && styles.labelFocused]}>PASSWORD AKUN</Text>
          <View style={[styles.inputGroup, passwordFocused && styles.inputFocused]}>
            <Lock size={18} color={passwordFocused ? '#cd0000' : '#a0a0b0'} style={styles.icon} />
            <TextInput
              style={styles.input}
              placeholder="Masukkan Password"
              placeholderTextColor="#a0a0b0"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
              onFocus={() => setPasswordFocused(true)}
              onBlur={() => setPasswordFocused(false)}
              returnKeyType="done"
              onSubmitEditing={handleLogin}
              disableFullscreenKeyboard={true}
            />
          </View>

          {/* TOMBOL LOGIN GRADASI GRADIENT IMITASI MERAH TEGAS */}
          <TouchableOpacity
            style={styles.button}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.85}
          >
            {loading ? (
              <ActivityIndicator color="#ffffff" />
            ) : (
              <View style={styles.buttonContent}>
                <Text style={styles.buttonText}>MASUK SISTEM</Text>
                <LogIn size={18} color="#ffffff" />
              </View>
            )}
          </TouchableOpacity>
        </View>

        
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: '#f4f5f9' /* Warna dasar abu-abu web */
  },
  dotGridContainer: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    flexDirection: 'row',
    flexWrap: 'wrap',
    padding: 15,
    gap: 24,
    opacity: 0.7
  },
  gridDot: {
    width: 2,
    height: 2,
    borderRadius: 1,
    backgroundColor: 'rgba(230, 57, 70, 0.2)' /* Mengimitasi efek polkadot grid merah di CSS */
  },
  scrollContent: {
    flexGrow: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 30,
    paddingTop: 50,
    paddingBottom: 30,
  },
  header: {
    alignItems: 'center',
    marginBottom: 40,
  },
  logo: {
    width: 75,
    height: 75,
    borderRadius: 16,
    marginBottom: 15,
    // Bayangan merah lembut untuk logo sesuai css web
    shadowColor: '#cd0000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 15,
    elevation: 4,
    backgroundColor: '#fff'
  },
  title: {
    fontSize: 24,
    fontWeight: '800',
    color: '#1e1e2f',
    letterSpacing: 1,
  },
  subtitle: {
    marginTop: 5,
    fontSize: 13,
    color: '#cd0000',
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  formContainer: {
    width: '100%',
    maxWidth: 380,
  },
  inputLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: '#475569',
    letterSpacing: 0.5,
    marginBottom: 6,
    marginLeft: 2,
  },
  labelFocused: {
    color: '#cd0000'
  },
  inputGroup: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff', /* Kotak Putih Bersih Solid */
    borderWidth: 1.5,
    borderColor: 'rgba(0, 0, 0, 0.06)',
    borderRadius: 12,
    paddingHorizontal: 16,
    marginBottom: 18,
    // Shadow tipis bawah
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.02,
    shadowRadius: 10,
    elevation: 1,
  },
  inputFocused: {
    borderColor: '#cd0000', /* Nyala merah tegas khas SEBSTAR */
    backgroundColor: '#ffffff',
    shadowColor: '#cd0000',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.12,
    shadowRadius: 4,
    elevation: 3,
  },
  icon: { marginRight: 12 },
  input: {
    flex: 1,
    paddingVertical: 14,
    fontSize: 14,
    color: '#1e1e2f',
    fontWeight: '600',
    ...Platform.select({
      web: { outlineStyle: 'none' }
    }),
  },
  button: {
    marginTop: 15,
    backgroundColor: '#cd0000', /* Base warna merah tua web */
    borderRadius: 12,
    paddingVertical: 15,
    alignItems: 'center',
    shadowColor: '#cd0000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 18,
    elevation: 4,
  },
  buttonContent: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  buttonText: { color: '#ffffff', fontSize: 14, fontWeight: '700', letterSpacing: 0.5 },
  forgotFooter: {
    width: '100%',
    maxWidth: 380,
    marginTop: 25,
    paddingHorizontal: 4,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  infoIcon: {
    marginTop: 2,
    marginRight: 6,
  },
  forgotText: {
    flex: 1,
    fontSize: 12,
    color: '#64748b',
    fontWeight: '600',
    lineHeight: 18,
  },
  highlightText: {
    color: '#cd0000',
    fontWeight: '700',
  }
});