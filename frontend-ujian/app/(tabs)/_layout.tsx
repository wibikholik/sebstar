import { Tabs } from 'expo-router';
import { Ionicons } from '@expo/vector-icons'; // <-- SUDAH DIPERBAIKI (MENGGUNAKAN @expo/vector-icons)

export default function TabLayout() {
  return (
    <Tabs screenOptions={{ 
      tabBarActiveTintColor: '#c91313',
      headerShown: true,
      headerStyle: { backgroundColor: '#fff' },
      headerTitleStyle: { fontWeight: '800', color: '#1e293b' }
    }}>
      {/* 1. TAB DASHBOARD (JADWAL) */}
      <Tabs.Screen
        name="index"
        options={{
          title: 'Dashboard',
          headerTitle: 'Jadwal Ujian',
          tabBarIcon: ({ color }) => <Ionicons name="home-outline" size={24} color={color} />,
        }}
      />
      
      {/* 2. TAB PROFIL (LOGOUT NYAMAN & TERISOLASI) */}
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profil',
          headerTitle: 'Akun Siswa',
          tabBarIcon: ({ color }) => <Ionicons name="person-outline" size={24} color={color} />,
        }}
      />
    </Tabs>
  );
}