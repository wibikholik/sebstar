import { Slot, useRouter, useSegments } from 'expo-router';
import { useEffect, useState } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { View, ActivityIndicator } from 'react-native';

export default function RootLayout() {
  const [isReady, setIsReady] = useState(false);
  const router = useRouter();
  const segments = useSegments();

  useEffect(() => {
    checkAuth();
  }, [segments]); 

  const checkAuth = async () => {
    try {
      const token = await AsyncStorage.getItem('userToken');
      const inAuthGroup = segments[0] === '(auth)';
      
      // LOG DEBUGGING: Lihat apa yang terjadi
      console.log("Token:", token, "Segments:", segments);

      // KITA TAMBAHKAN LOGIKA: 
      // Jangan melakukan apa-apa jika user sudah berada di halaman yang dituju
      if (!token && !inAuthGroup) {
        // Jika belum login dan tidak di grup auth, redirect ke login
        router.replace('/(auth)/login');
      } else if (token && inAuthGroup) {
        // Jika sudah login tapi masih di grup auth, redirect ke tabs
        router.replace('/(tabs)');
      }
      
      // Jika token ada ATAU user sedang menuju halaman ujian/selesai, 
      // JANGAN redirect (biarkan navigasi berjalan).
      
    } catch (e) {
      console.error("Auth check error:", e);
    } finally {
      setIsReady(true);
    }
  };

  if (!isReady) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return <Slot />;
}