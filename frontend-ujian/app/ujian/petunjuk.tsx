import { View, Text, TouchableOpacity, StyleSheet, ScrollView, StatusBar } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

export default function PetunjukScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();

  const handleMulaiUjian = () => {
    // Navigasi ke halaman pengerjaan soal
    router.replace({
      pathname: '/ujian/kerjakan',
      params: { id, token }
    });
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      
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

        <TouchableOpacity 
          activeOpacity={0.8} 
          style={styles.btnMulai} 
          onPress={handleMulaiUjian}
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
  scrollContent: { padding: 25, paddingTop: 60 },
  header: { alignItems: 'center', marginBottom: 30 },
  iconCircle: { width: 80, height: 80, borderRadius: 40, backgroundColor: '#fff', justifyContent: 'center', alignItems: 'center', elevation: 4, shadowColor: '#000', shadowOpacity: 0.1, shadowRadius: 10, marginBottom: 15 },
  title: { fontSize: 22, fontWeight: '800', color: '#1e293b', textAlign: 'center' },
  subtitle: { fontSize: 14, color: '#64748b', textAlign: 'center', marginTop: 8, lineHeight: 20 },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 20, elevation: 2, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 10 },
  instructionItem: { flexDirection: 'row', gap: 15, marginBottom: 18, alignItems: 'flex-start' },
  text: { flex: 1, fontSize: 14, color: '#334155', lineHeight: 22, fontWeight: '500' },
  warningBox: { backgroundColor: '#fff1f2', padding: 15, borderRadius: 12, marginVertical: 25, borderLeftWidth: 4, borderLeftColor: '#c91313' },
  warningText: { fontSize: 12, color: '#c91313', fontWeight: '700', textAlign: 'center', lineHeight: 18 },
  btnMulai: { backgroundColor: '#c91313', padding: 18, borderRadius: 15, alignItems: 'center', flexDirection: 'row', justifyContent: 'center', gap: 10, elevation: 4, shadowColor: '#c91313', shadowOpacity: 0.3, shadowRadius: 10 },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 14, letterSpacing: 0.5 },
  btnBatal: { marginTop: 15, padding: 10, alignItems: 'center' },
  btnBatalText: { color: '#64748b', fontSize: 14, fontWeight: '600' }
});