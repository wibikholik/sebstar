import React, {
  useState,
  useEffect,
  useRef
} from 'react';

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
  StatusBar,
  Image,
  Animated,
} from 'react-native';

import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import { LinearGradient } from 'expo-linear-gradient';

import {
  User,
  Lock,
  LogIn,
} from 'lucide-react-native';

import api from '../../src/api/axiosConfig';

export default function LoginScreen() {

  const [nis, setNis] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const [nisFocused, setNisFocused] = useState(false);
  const [passwordFocused, setPasswordFocused] = useState(false);

  const router = useRouter();

  // FLOATING LOGO ANIMATION
  const floatAnim = useRef(
    new Animated.Value(0)
  ).current;

  useEffect(() => {
    Animated.loop(
      Animated.sequence([
        Animated.timing(floatAnim, {
          toValue: -10,
          duration: 2000,
          useNativeDriver: Platform.OS !== 'web', // Matikan di web jika ada kendala kompatibilitas
        }),
        Animated.timing(floatAnim, {
          toValue: 0,
          duration: 2000,
          useNativeDriver: Platform.OS !== 'web',
        }),
      ])
    ).start();
  }, []);

  // FUNGSI UNTUK MENAMPILKAN ALERT YANG AMAN DI WEB DAN HP
  const showAlert = (title: string, message: string) => {
    if (Platform.OS === 'web') {
      // Di browser web, Alert bawaan react-native kadang tidak muncul di sebagian setup. gunakan alert bawaan browser.
      alert(`${title}\n\n${message}`);
    } else {
      Alert.alert(title, message);
    }
  };

  // HANDLER LOGIN CROSS-PLATFORM (WEB & MOBILE APP)
  const handleLogin = async () => {
    if (!nis || !password) {
      showAlert('Peringatan', 'NIS dan Password harus diisi');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/login', {
        nis,
        password,
      });

      const token = response.data.access_token;
      
      // Mengamankan penyimpanan token untuk web dan mobile
      if (Platform.OS === 'web') {
        localStorage.setItem('userToken', token);
      } else {
        await AsyncStorage.setItem('userToken', token);
      }
      
      router.replace('/(tabs)');

    } catch (error: any) {
      // Mengubah seluruh log objek error menjadi string huruf kecil agar mudah disaring lintas platform
      const errorString = JSON.stringify(error).toLowerCase();
      const errorMessageFromServer = (error.response?.data?.message || error.response?.data?.error || '').toLowerCase();
      const statusCode = error.response?.status;

      console.log('--- LOG DEBUG LOGIN ---');
      console.log('Status Code:', statusCode);
      console.log('Response Data:', error.response?.data);
      console.log('Error Message Object:', error.message);

      // KONDISI 1: Terdeteksi validasi gagal dari kode status HTTP (Umum di HP & Web normal)
      if (statusCode === 401 || statusCode === 403 || statusCode === 422) {
        showAlert(
          'Login Gagal',
          'NIS atau Password yang kamu masukkan salah. Silakan periksa kembali data kamu.'
        );
      } 
      // KONDISI 2: Deteksi kata kunci sensitif jika browser web menyembunyikan status code ke "Network Error"
      else if (
        errorString.includes('forbidden') || 
        errorString.includes('unauthorized') || 
        errorString.includes('401') || 
        errorString.includes('403') ||
        errorMessageFromServer.includes('forbidden') ||
        errorMessageFromServer.includes('unauthorized')
      ) {
        showAlert(
          'Login Gagal',
          'NIS atau Password yang kamu masukkan salah. Silakan periksa kembali data kamu.'
        );
      } 
      // KONDISI 3: Benar-benar tidak ada sinyal / server Laragon mati total
      else {
        showAlert(
          'Masalah Koneksi',
          'Tidak dapat memproses login. Pastikan server backend sudah menyala, IP sudah sesuai, dan fitur CORS di backend sudah diizinkan untuk akses Web.'
        );
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <LinearGradient
      colors={[
        '#ffe8e8',
        '#f4f5f9',
        '#ffffff'
      ]}
      style={styles.gradient}
    >
      {/* BACKGROUND POLKADOT (Hanya dirender jika di HP biar performa Web tidak berat saat resize) */}
      <View style={styles.dotsContainer}>
        {[...Array(Platform.OS === 'web' ? 50 : 150)].map((_, index) => (
          <View
            key={index}
            style={[
              styles.dot,
              {
                left: Math.random() * (Platform.OS === 'web' ? 1200 : 400),
                top: Math.random() * 900,
              }
            ]}
          />
        ))}
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.container}
      >
        <StatusBar
          barStyle="dark-content"
          backgroundColor="#ffe8e8"
        />

        {/* CONTAINER CARD */}
        <View style={styles.card}>

          {/* SECTION HEADER & ANIMATED LOGO */}
          <Animated.View
            style={[
              styles.header,
              {
                transform: [
                  { translateY: floatAnim }
                ]
              }
            ]}
          >
            <Image
              source={require('../../assets/images/LOGO.png')}
              style={styles.logo}
              resizeMode="contain"
            />
            <Text style={styles.title}>SEBSTAR</Text>
            <Text style={styles.subtitle}>Sistem Ujian Digital</Text>
          </Animated.View>

          {/* INPUT FOR NIS */}
          <View
            style={[
              styles.inputGroup,
              nisFocused && styles.inputFocused
            ]}
          >
            <User
              size={18}
              color={nisFocused ? '#cd0000' : '#a0a0b0'}
              style={styles.icon}
            />
            <TextInput
              style={styles.input}
              placeholder="Masukkan NIS"
              placeholderTextColor="#999"
              value={nis}
              onChangeText={setNis}
              keyboardType="numeric"
              onFocus={() => setNisFocused(true)}
              onBlur={() => setNisFocused(false)}
            />
          </View>

          {/* INPUT FOR PASSWORD */}
          <View
            style={[
              styles.inputGroup,
              passwordFocused && styles.inputFocused
            ]}
          >
            <Lock
              size={18}
              color={passwordFocused ? '#cd0000' : '#a0a0b0'}
              style={styles.icon}
            />
            <TextInput
              style={styles.input}
              placeholder="Masukkan Password"
              placeholderTextColor="#999"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
              onFocus={() => setPasswordFocused(true)}
              onBlur={() => setPasswordFocused(false)}
            />
          </View>

          {/* ACTION BUTTON */}
          <TouchableOpacity
            style={styles.button}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.85}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <View style={styles.buttonContent}>
                <Text style={styles.buttonText}>MASUK SISTEM</Text>
                <LogIn size={18} color="#fff" />
              </View>
            )}
          </TouchableOpacity>

        </View>
      </KeyboardAvoidingView>
    </LinearGradient>
  );
}

const styles = StyleSheet.create({
  gradient: {
    flex: 1,
  },
  container: {
    flex: 1,
    justifyContent: 'center',
    paddingHorizontal: 24,
    maxWidth: Platform.OS === 'web' ? 450 : '100%', // Biar di web tampilannya proporsional di tengah layaknya aplikasi mobile
    alignSelf: 'center',
    width: '100%',
  },
  dotsContainer: {
    position: 'absolute',
    width: '100%',
    height: '100%',
  },
  dot: {
    position: 'absolute',
    width: 4,
    height: 4,
    borderRadius: 999,
    backgroundColor: 'rgba(205,0,0,0.12)',
  },
  card: {
    backgroundColor: 'rgba(255,255,255,0.85)', // Ditingkatkan opacity-nya agar kontras di web aman
    borderRadius: 30,
    padding: 30,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.7)',
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 12,
    },
    shadowOpacity: 0.1,
    shadowRadius: 25,
    elevation: 10,
    overflow: 'hidden',
  },
  header: {
    alignItems: 'center',
    marginBottom: 28,
  },
  logo: {
    width: 90,
    height: 90,
    marginBottom: 15,
  },
  title: {
    fontSize: 30,
    fontWeight: '900',
    color: '#1e1e2f',
    letterSpacing: 2,
  },
  subtitle: {
    marginTop: 6,
    fontSize: 13,
    color: '#cd0000',
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  inputGroup: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.9)',
    borderWidth: 1,
    borderColor: 'rgba(0,0,0,0.08)',
    borderRadius: 16,
    paddingHorizontal: 16,
    marginBottom: 18,
  },
  inputFocused: {
    borderColor: '#cd0000',
    backgroundColor: '#fff',
    shadowColor: '#cd0000',
    shadowOffset: {
      width: 0,
      height: 0,
    },
    shadowOpacity: 0.15,
    shadowRadius: 10,
    elevation: 4,
  },
  icon: {
    marginRight: 10,
  },
  input: {
    flex: 1,
    paddingVertical: 15,
    fontSize: 14,
    color: '#1e1e2f',
    fontWeight: '600',
    outlineStyle: Platform.OS === 'web' ? 'none' : undefined, // Hilangkan outline bawaan browser di web
  },
  button: {
    marginTop: 10,
    backgroundColor: '#cd0000',
    borderRadius: 16,
    paddingVertical: 16,
    alignItems: 'center',
    shadowColor: '#cd0000',
    shadowOffset: {
      width: 0,
      height: 6,
    },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 5,
    cursor: Platform.OS === 'web' ? 'pointer' : 'auto', // Memberi efek pointer mouse kalau dibuka di web
  },
  buttonContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  buttonText: {
    color: '#fff',
    fontSize: 15,
    fontWeight: '700',
    letterSpacing: 1,
  },
});