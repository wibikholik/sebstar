// app/ujian/petunjuk.tsx
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';

export default function PetunjukScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();

  const handleMulaiUjian = () => {
    // Pindah ke kerjakan, gunakan replace agar siswa tidak bisa balik lagi ke petunjuk
    router.replace({
      pathname: '/ujian/kerjakan',
      params: { id, token }
    });
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Petunjuk Ujian</Text>
      <View style={styles.card}>
        <Text style={styles.text}>1. Pastikan koneksi internet stabil.</Text>
        <Text style={styles.text}>2. Dilarang keluar dari aplikasi saat ujian berlangsung.</Text>
        <Text style={styles.text}>3. Waktu akan berjalan otomatis setelah menekan tombol mulai.</Text>
        <Text style={styles.text}>4. Klik tombol di bawah untuk memulai.</Text>
      </View>

      <TouchableOpacity style={styles.btnMulai} onPress={handleMulaiUjian}>
        <Text style={styles.btnText}>SAYA MENGERTI, MULAI UJIAN</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, backgroundColor: '#f8f9fa', justifyContent: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 20, textAlign: 'center' },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 10, marginBottom: 30 },
  text: { fontSize: 16, marginBottom: 10, color: '#333' },
  btnMulai: { backgroundColor: '#28a745', padding: 15, borderRadius: 10, alignItems: 'center' },
  btnText: { color: '#fff', fontWeight: 'bold', fontSize: 16 }
});