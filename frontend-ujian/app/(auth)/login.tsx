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

  // FLOATING LOGO
  const floatAnim = useRef(
    new Animated.Value(0)
  ).current;

  useEffect(() => {

    Animated.loop(
      Animated.sequence([
        Animated.timing(floatAnim, {
          toValue: -10,
          duration: 2000,
          useNativeDriver: true,
        }),

        Animated.timing(floatAnim, {
          toValue: 0,
          duration: 2000,
          useNativeDriver: true,
        }),
      ])
    ).start();

  }, []);

  const handleLogin = async () => {

    if (!nis || !password) {

      Alert.alert(
        'Peringatan',
        'NIS dan Password harus diisi'
      );

      return;
    }

    setLoading(true);

    try {

      const response = await api.post(
        '/login',
        {
          nis,
          password,
        }
      );

      const token =
        response.data.access_token;

      await AsyncStorage.setItem(
        'userToken',
        token
      );

      router.replace('/(tabs)');

    } catch (error: any) {

      if (error.response) {

        const statusCode =
          error.response.status;

        const serverMessage =
          error.response.data.message;

        if (statusCode === 401) {

          Alert.alert(
            'Login Gagal',
            serverMessage ||
            'NIS atau Password salah'
          );

        } else {

          Alert.alert(
            'Gagal',
            serverMessage ||
            'Terjadi kesalahan server'
          );

        }

      } else {

        Alert.alert(
          'Gagal',
          'Server tidak merespons'
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

      {/* POLKADOT */}
      <View style={styles.dotsContainer}>

        {[...Array(150)].map((_, index) => (

          <View
            key={index}
            style={[
              styles.dot,
              {
                left: Math.random() * 400,
                top: Math.random() * 900,
              }
            ]}
          />

        ))}

      </View>

      <KeyboardAvoidingView
        behavior={
          Platform.OS === 'ios'
            ? 'padding'
            : 'height'
        }
        style={styles.container}
      >

        <StatusBar
          barStyle="dark-content"
          backgroundColor="#ffe8e8"
        />

        {/* CARD */}
        <View style={styles.card}>

          {/* HEADER */}
          <Animated.View
            style={[
              styles.header,
              {
                transform: [
                  {
                    translateY: floatAnim
                  }
                ]
              }
            ]}
          >

            <Image
              source={require('../../assets/images/LOGO.png')}
              style={styles.logo}
              resizeMode="contain"
            />

            <Text style={styles.title}>
              SEBSTAR
            </Text>

            <Text style={styles.subtitle}>
              Sistem Ujian Digital
            </Text>

          </Animated.View>

          {/* INPUT NIS */}
          <View
            style={[
              styles.inputGroup,
              nisFocused &&
              styles.inputFocused
            ]}
          >

            <User
              size={18}
              color={
                nisFocused
                  ? '#cd0000'
                  : '#a0a0b0'
              }
              style={styles.icon}
            />

            <TextInput
              style={styles.input}
              placeholder="Masukkan NIS"
              placeholderTextColor="#999"
              value={nis}
              onChangeText={setNis}
              keyboardType="numeric"
              onFocus={() =>
                setNisFocused(true)
              }
              onBlur={() =>
                setNisFocused(false)
              }
            />

          </View>

          {/* INPUT PASSWORD */}
          <View
            style={[
              styles.inputGroup,
              passwordFocused &&
              styles.inputFocused
            ]}
          >

            <Lock
              size={18}
              color={
                passwordFocused
                  ? '#cd0000'
                  : '#a0a0b0'
              }
              style={styles.icon}
            />

            <TextInput
              style={styles.input}
              placeholder="Masukkan Password"
              placeholderTextColor="#999"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
              onFocus={() =>
                setPasswordFocused(true)
              }
              onBlur={() =>
                setPasswordFocused(false)
              }
            />

          </View>

          {/* BUTTON */}
          <TouchableOpacity
            style={styles.button}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.85}
          >

            {loading ? (

              <ActivityIndicator
                color="#fff"
              />

            ) : (

              <View
                style={
                  styles.buttonContent
                }
              >

                <Text
                  style={
                    styles.buttonText
                  }
                >
                  MASUK SISTEM
                </Text>

                <LogIn
                  size={18}
                  color="#fff"
                />

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
  },

  // POLKADOT
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
    backgroundColor:
      'rgba(205,0,0,0.12)',
  },

  // CARD
  card: {

    backgroundColor:
      'rgba(255,255,255,0.58)',

    borderRadius: 30,

    padding: 30,

    borderWidth: 1,
    borderColor:
      'rgba(255,255,255,0.7)',

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

  // HEADER
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

  // INPUT
  inputGroup: {

    flexDirection: 'row',

    alignItems: 'center',

    backgroundColor:
      'rgba(255,255,255,0.9)',

    borderWidth: 1,

    borderColor:
      'rgba(0,0,0,0.08)',

    borderRadius: 16,

    paddingHorizontal: 16,

    marginBottom: 18,

    transitionDuration: '300ms',
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

    transform: [
      {
        scale: 1.02
      }
    ],
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
  },

  // BUTTON
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