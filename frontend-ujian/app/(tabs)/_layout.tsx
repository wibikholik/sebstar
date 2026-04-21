import { Tabs } from 'expo-router';
import React from 'react';

export default function TabLayout() {
  return (
    <Tabs screenOptions={{ tabBarActiveTintColor: 'blue' }}>
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