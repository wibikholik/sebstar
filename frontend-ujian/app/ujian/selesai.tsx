import { View, Text, TouchableOpacity, StyleSheet, StatusBar } from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

export default function SelesaiScreen() {
  const router = useRouter();
  const { id, token } = useLocalSearchParams();
  const primaryRed = '#c91313'; // Tema Merah Sebstar

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      
      <View style={styles.content}>
        <View style={styles.iconWrapper}>
          <View style={styles.pulseCircle} />
          <View style={styles.mainCircle}>
            <Ionicons name="checkmark-done" size={60} color="#fff" />
          </View>
        </View>

        <Text style={styles.title}>Ujian Berhasil Dikirim!</Text>
        <Text style={styles.subtitle}>
          Kerja bagus! Jawaban Anda telah tersinkronisasi ke server SMKN 1 Binong. Silakan kembali ke beranda atau cek hasil sementara.
        </Text>

        <View style={styles.buttonContainer}>
          <TouchableOpacity 
            activeOpacity={0.8}
            style={[styles.primaryBtn, { backgroundColor: primaryRed }]} 
            onPress={() => router.replace('/(tabs)')}
          >
            <Ionicons name="home" size={20} color="#fff" />
            <Text style={styles.primaryBtnText}>Kembali ke Beranda</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            activeOpacity={0.6}
            style={styles.secondaryBtn} 
            onPress={() => router.push({ pathname: '/ujian/rekap', params: { id, token } })}
          >
            <Text style={styles.secondaryBtnText}>Lihat Rekap Nilai</Text>
            <Ionicons name="stats-chart" size={18} color="#64748b" />
          </TouchableOpacity>
        </View>
      </View>

      <Text style={styles.footerText}>Sebstar Exam Browser • Professional Edition</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: '#f8fafc', 
    justifyContent: 'center', 
    alignItems: 'center', 
    padding: 30 
  },
  content: { 
    width: '100%', 
    alignItems: 'center' 
  },
  iconWrapper: { 
    width: 150, 
    height: 150, 
    justifyContent: 'center', 
    alignItems: 'center', 
    marginBottom: 30 
  },
  mainCircle: { 
    width: 100, 
    height: 100, 
    borderRadius: 50, 
    backgroundColor: '#c91313', 
    justifyContent: 'center', 
    alignItems: 'center', 
    elevation: 10, 
    shadowColor: '#c91313', 
    shadowOpacity: 0.3, 
    shadowRadius: 15,
    zIndex: 2
  },
  pulseCircle: { 
    position: 'absolute',
    width: 130, 
    height: 130, 
    borderRadius: 65, 
    backgroundColor: '#fee2e2', 
    opacity: 0.6,
    zIndex: 1
  },
  title: { 
    fontSize: 24, 
    fontWeight: '800', 
    color: '#1e293b', 
    marginBottom: 12, 
    textAlign: 'center' 
  },
  subtitle: { 
    fontSize: 15, 
    color: '#64748b', 
    textAlign: 'center', 
    lineHeight: 22, 
    marginBottom: 40,
    paddingHorizontal: 10 
  },
  buttonContainer: { 
    width: '100%', 
    gap: 12 
  },
  primaryBtn: { 
    flexDirection: 'row',
    padding: 18, 
    borderRadius: 16, 
    alignItems: 'center', 
    justifyContent: 'center',
    gap: 10,
    elevation: 4,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 10
  },
  primaryBtnText: { 
    color: '#fff', 
    fontSize: 16, 
    fontWeight: '800',
    letterSpacing: 0.5
  },
  secondaryBtn: { 
    flexDirection: 'row',
    backgroundColor: '#fff', 
    padding: 18, 
    borderRadius: 16, 
    alignItems: 'center', 
    justifyContent: 'center',
    borderWidth: 1.5, 
    borderColor: '#e2e8f0',
    gap: 10
  },
  secondaryBtnText: { 
    color: '#64748b', 
    fontSize: 15, 
    fontWeight: '700' 
  },
  footerText: {
    position: 'absolute',
    bottom: 40,
    fontSize: 12,
    color: '#cbd5e1',
    fontWeight: '600',
    letterSpacing: 1
  }
});