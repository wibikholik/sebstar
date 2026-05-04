import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from 'react-native';

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
        nis,
        password,
      });

      await AsyncStorage.setItem('userToken', response.data.access_token);

      router.replace('/(tabs)');
    } catch (error: any) {
      setLoading(false);

      if (error.response) {
        Alert.alert('Login Gagal', error.response.data.message || 'NIS/Password salah');
      } else if (error.request) {
        Alert.alert('Gagal', 'Server tidak merespons');
      } else {
        Alert.alert('Error', 'Terjadi kesalahan');
      }
    }
  };

  return (
    <LinearGradient
      colors={['#c91313', '#ff4d4d']}
      style={styles.container}
    >
      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={{ width: '100%' }}
      >
        <View style={styles.card}>

          {/* LOGO / TITLE */}
          <View style={styles.header}>
            <Ionicons name="lock-closed" size={40} color="#c91313" />
            <Text style={styles.title}>SEBSTAR</Text>
            <Text style={styles.subtitle}>Masuk ke akun kamu</Text>
          </View>

          {/* INPUT NIS */}
          <View style={styles.inputGroup}>
            <Ionicons name="person" size={20} color="#888" style={styles.icon} />
            <TextInput 
              placeholder="NIS"
              value={nis}
              onChangeText={setNis}
              style={styles.input}
              keyboardType="numeric"
            />
          </View>

          {/* INPUT PASSWORD */}
          <View style={styles.inputGroup}>
            <Ionicons name="lock-closed" size={20} color="#888" style={styles.icon} />
            <TextInput 
              placeholder="Password"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
              style={styles.input}
            />
          </View>

          {/* BUTTON */}
          <TouchableOpacity 
            style={styles.button} 
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
    </LinearGradient>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },

  card: {
    width: '90%',
    backgroundColor: '#fff',
    padding: 25,
    borderRadius: 20,
    elevation: 10,
  },

  header: {
    alignItems: 'center',
    marginBottom: 30,
  },

  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginTop: 10,
  },

  subtitle: {
    fontSize: 14,
    color: '#666',
  },

  inputGroup: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 12,
    marginBottom: 15,
    paddingHorizontal: 10,
  },

  icon: {
    marginRight: 8,
  },

  input: {
    flex: 1,
    padding: 12,
  },

  button: {
    backgroundColor: '#c91313',
    padding: 15,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 10,
  },

  buttonText: {
    color: '#fff',
    fontWeight: 'bold',
  },
});