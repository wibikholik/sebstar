import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';

export default function SelesaiScreen() {
  const router = useRouter();
  const { id, token } = useLocalSearchParams();

  return (
    <View style={styles.container}>
      <View style={styles.iconContainer}><Text style={styles.emoji}>🎉</Text></View>
      <Text style={styles.title}>Ujian Selesai!</Text>
      <Text style={styles.subtitle}>Terima kasih telah mengerjakan ujian. Jawaban Anda tersimpan dengan aman.</Text>
      
      <View style={styles.buttonContainer}>
        <TouchableOpacity style={styles.primaryBtn} onPress={() => router.replace('/(tabs)')}>
          <Text style={styles.primaryBtnText}>Kembali ke Beranda</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.secondaryBtn} onPress={() => router.push({ pathname: '/ujian/rekap', params: { id, token } })}>
          <Text style={styles.secondaryBtnText}>Lihat Rekap Nilai</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 20, backgroundColor: '#fff' },
  iconContainer: { width: 120, height: 120, borderRadius: 60, backgroundColor: '#e6fffa', justifyContent: 'center', alignItems: 'center', marginBottom: 20 },
  emoji: { fontSize: 60 },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 10 },
  subtitle: { fontSize: 16, color: '#666', textAlign: 'center', marginBottom: 40 },
  buttonContainer: { width: '100%', gap: 15 },
  primaryBtn: { backgroundColor: '#007bff', padding: 18, borderRadius: 12, alignItems: 'center' },
  primaryBtnText: { color: '#fff', fontSize: 16, fontWeight: 'bold' },
  secondaryBtn: { backgroundColor: '#f8f9fa', padding: 18, borderRadius: 12, alignItems: 'center', borderWidth: 1, borderColor: '#ddd' },
  secondaryBtnText: { color: '#333', fontSize: 16, fontWeight: 'bold' }
});