import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator, Alert, StatusBar } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig'; 
import { Ionicons } from '@expo/vector-icons';

export default function ProfileScreen() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    getProfileData();
  }, []);

  const getProfileData = async () => {
    try {
      const localUser = await AsyncStorage.getItem('userData');
      if (localUser) {
        setUser(JSON.parse(localUser));
      }
    } catch (e) {
      console.log("Gagal memuat data user lokal:", e.message);
    } finally {
      setLoading(false);
    }
  };

  // --- LOGIKA LOGOUT MANDIRI & TERISOLASI ---
  const handleSystemLogout = () => {
    Alert.alert("Konfirmasi Keluar", "Apakah Anda yakin ingin keluar dari akun ini?", [
      { text: "Batal", style: "cancel" },
      {
        text: "Keluar",
        style: "destructive",
        onPress: async () => {
          setLoading(true);
          try {
            // Ubah status is_logged_in jadi 0 di DB Laravel
            await api.post('/logout');
          } catch (e) {
            console.log("Server offline, lanjut membersihkan storage perangkat.");
          } finally {
            try {
              // Hapus semua data auth lokal
              await AsyncStorage.removeItem('userToken');
              await AsyncStorage.removeItem('userData');
              
              // Tendang ke halaman login utama
              router.replace('/(auth)/login');
            } catch (err) {
              Alert.alert("Error", "Gagal menghapus session login perangkat.");
            } finally {
              setLoading(false);
            }
          }
        }
      }
    ]);
  };

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#c91313" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />
      
      <View style={styles.avatarContainer}>
        <View style={styles.avatarIconBG}>
          <Ionicons name="person" size={60} color="#c91313" />
        </View>
        <Text style={styles.nameText}>{user?.name ?? 'Siswa Percobaan'}</Text>
        <Text style={styles.subText}>NIS: {user?.nis ?? '-'}</Text>
      </View>

      <View style={styles.infoBox}>
        <View style={styles.infoRow}>
          <Ionicons name="school-outline" size={20} color="#64748b" />
          <Text style={styles.infoLabel}>Kelas:</Text>
          <Text style={styles.infoValue}>{user?.classroom?.nama_kelas ?? 'XII RPL'}</Text>
        </View>
        <View style={styles.infoRow}>
          <Ionicons name="phone-portrait-outline" size={20} color="#64748b" />
          <Text style={styles.infoLabel}>Device Status:</Text>
          <Text style={[styles.infoValue, {color: '#10b981', fontWeight: '800'}]}>TERVERIFIKASI</Text>
        </View>
      </View>

      {/* TOMBOL LOGOUT UTAMA - GEDE, BERSIH, TERISOLASI */}
      <TouchableOpacity style={styles.logoutBtn} onPress={handleSystemLogout} activeOpacity={0.7}>
        <Ionicons name="log-out" size={22} color="#fff" style={{ marginRight: 8 }} />
        <Text style={styles.logoutText}>KELUAR DARI APLIKASI</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc', padding: 25, justifyContent: 'center' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  avatarContainer: { alignItems: 'center', marginBottom: 35 },
  avatarIconBG: { width: 110, height: 110, borderRadius: 55, backgroundColor: '#fee2e2', justifyContent: 'center', alignItems: 'center', marginBottom: 15, elevation: 3 },
  nameText: { fontSize: 22, fontWeight: '800', color: '#1e293b', textAlign: 'center' },
  subText: { fontSize: 14, color: '#64748b', marginTop: 4 },
  infoBox: { backgroundColor: '#fff', padding: 20, borderRadius: 20, elevation: 2, marginBottom: 40 },
  infoRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  infoLabel: { fontSize: 14, color: '#64748b', marginLeft: 10, flex: 1 },
  infoValue: { fontSize: 15, fontWeight: '700', color: '#1e293b' },
  logoutBtn: { backgroundColor: '#c91313', padding: 18, borderRadius: 15, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', elevation: 4, shadowColor: '#c91313', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 5 },
  logoutText: { color: '#fff', fontSize: 15, fontWeight: '800', letterSpacing: 1 }
});