import React, { useState } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, ScrollView, StatusBar, Platform } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

export default function PetunjukScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();

  // STATE UNTUK MENYIMPAN STATUS CENTANG (PROFIT PERSETUJUAN)
  const [isChecked, setIsChecked] = useState(false);

  const handleMulaiUjian = () => {
    // Sebagai perlindungan ganda di level fungsi
    if (!isChecked) return;

    // Navigasi ke halaman pengerjaan soal
    router.replace({
      pathname: '/ujian/kerjakan',
      params: { id, token }
    });
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#f8fafc" />
      
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.header}>
          <View style={styles.iconCircle}>
            <Ionicons name="document-text" size={40} color="#c91313" />
          </View>
          <Text style={styles.title}>Instruksi & Tata Tertib Ujian</Text>
          <Text style={styles.subtitle}>Harap baca dengan teliti sebelum memulai sesi ujian Anda.</Text>
        </View>

        <View style={styles.card}>
          <View style={styles.instructionItem}>
            <Ionicons name="wifi" size={20} color="#c91313" />
            <Text style={styles.text}>Pastikan Anda terhubung dengan koneksi internet yang stabil selama ujian berlangsung.</Text>
          </View>

          <View style={styles.instructionItem}>
            <Ionicons name="lock-closed" size={20} color="#c91313" />
            <Text style={styles.text}>Aplikasi akan mendeteksi jika Anda mencoba keluar atau berpindah aplikasi (Multitasking).</Text>
          </View>

          <View style={styles.instructionItem}>
            <Ionicons name="time" size={20} color="#c91313" />
            <Text style={styles.text}>Waktu ujian akan dihitung mundur secara otomatis sejak Anda menekan tombol mulai.</Text>
          </View>

          <View style={styles.instructionItem}>
            <Ionicons name="save" size={20} color="#c91313" />
            <Text style={styles.text}>Jawaban Anda akan tersimpan otomatis setiap kali Anda berpindah ke nomor soal berikutnya.</Text>
          </View>

          <View style={styles.instructionItem}>
            <Ionicons name="alert-circle" size={20} color="#c91313" />
            <Text style={styles.text}>Pelanggaran terhadap sistem keamanan aplikasi dapat menyebabkan sesi ujian Anda dihentikan secara sepihak.</Text>
          </View>
        </View>

        <View style={styles.warningBox}>
          <Text style={styles.warningText}>
            DENGAN MENEKAN TOMBOL DI BAWAH, ANDA MENYATAKAN AKAN MENGERJAKAN UJIAN DENGAN JUJUR DAN MEMATUHI SEGALA PERATURAN.
          </Text>
        </View>

        {/* SECTION CHECKBOX CENTANG PERSETUJUAN */}
        <TouchableOpacity 
          style={styles.checkboxContainer} 
          onPress={() => setIsChecked(!isChecked)}
          activeOpacity={0.8}
        >
          <View style={[styles.checkbox, isChecked && styles.checkboxActive]}>
            {isChecked && <Ionicons name="checkmark" size={16} color="#fff" />}
          </View>
          <Text style={styles.checkboxLabel}>
            Saya telah membaca, memahami, dan menyetujui seluruh tata tertib ujian di atas.
          </Text>
        </TouchableOpacity>

        {/* TOMBOL MULAI DENGAN VALIDASI DISABLED */}
        <TouchableOpacity 
          activeOpacity={0.8} 
          style={[styles.btnMulai, !isChecked && styles.btnMulaiDisabled]} 
          onPress={handleMulaiUjian}
          disabled={!isChecked} // Mengunci tombol secara sistem jika belum dicentang
        >
          <Text style={styles.btnText}>SAYA MENGERTI, MULAI SEKARANG</Text>
          <Ionicons name="play-circle" size={20} color="#fff" />
        </TouchableOpacity>

        <TouchableOpacity 
          style={styles.btnBatal} 
          onPress={() => router.back()}
        >
          <Text style={styles.btnBatalText}>Kembali ke Jadwal</Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  scrollContent: { padding: 25, paddingTop: Platform.OS === 'web' ? 40 : 60, maxWidth: 500, alignSelf: 'center', width: '100%' },
  header: { alignItems: 'center', marginBottom: 30 },
  iconCircle: { width: 80, height: 80, borderRadius: 40, backgroundColor: '#fff', justifyContent: 'center', alignItems: 'center', elevation: 4, shadowColor: '#000', shadowOpacity: 0.1, shadowRadius: 10, marginBottom: 15 },
  title: { fontSize: 22, fontWeight: '800', color: '#1e293b', textAlign: 'center' },
  subtitle: { fontSize: 14, color: '#64748b', textAlign: 'center', marginTop: 8, lineHeight: 20 },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 20, elevation: 2, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 10 },
  instructionItem: { flexDirection: 'row', gap: 15, marginBottom: 18, alignItems: 'flex-start' },
  text: { flex: 1, fontSize: 14, color: '#334155', lineHeight: 22, fontWeight: '500' },
  warningBox: { backgroundColor: '#fff1f2', padding: 15, borderRadius: 12, marginTop: 25, marginBottom: 15, borderLeftWidth: 4, borderLeftColor: '#c91313' },
  warningText: { fontSize: 12, color: '#c91313', fontWeight: '700', textAlign: 'center', lineHeight: 18 },
  
  // STYLING KOMPONEN CHECKBOX BARU
  checkboxContainer: { flexDirection: 'row', alignItems: 'center', marginBottom: 25, paddingHorizontal: 4, gap: 12, cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  checkbox: { width: 22, height: 22, borderRadius: 6, borderWidth: 2, borderColor: '#cbd5e1', backgroundColor: '#fff', justifyContent: 'center', alignItems: 'center' },
  checkboxActive: { backgroundColor: '#c91313', borderColor: '#c91313' },
  checkboxLabel: { flex: 1, fontSize: 13, color: '#475569', fontWeight: '600', lineHeight: 18 },

  // STYLING TOMBOL UTAMA
  btnMulai: { backgroundColor: '#c91313', padding: 18, borderRadius: 15, alignItems: 'center', flexDirection: 'row', justifyContent: 'center', gap: 10, elevation: 4, shadowColor: '#c91313', shadowOpacity: 0.3, shadowRadius: 10, cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  btnMulaiDisabled: { backgroundColor: '#94a3b8', shadowOpacity: 0, elevation: 0, opacity: 0.6, cursor: 'not-allowed' }, // Warna abu-abu saat terkunci
  btnText: { color: '#fff', fontWeight: '800', fontSize: 14, letterSpacing: 0.5 },
  btnBatal: { marginTop: 15, padding: 10, alignItems: 'center', cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  btnBatalText: { color: '#64748b', fontSize: 14, fontWeight: '600' }
});