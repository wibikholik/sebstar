import { Tabs } from 'expo-router';
import React from 'react';

export default function TabLayout() {
  return (
    // Gabungkan semua opsi di dalam satu screenOptions
    <Tabs 
      screenOptions={{ 
        headerShown: false,           // Menghilangkan header
        tabBarActiveTintColor: 'blue' // Warna saat tab aktif
      }}
    >
      <Tabs.Screen 
        name="index" 
        options={{ title: 'Home' }} 
      />
      <Tabs.Screen 
        name="explore" 
        options={{ title: 'Explore' }} 
      />
      <Tabs.Screen 
        name="ujian" 
        options={{ title: 'Ujian' }} 
      />
    </Tabs>
  );
}