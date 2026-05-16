import { useEffect, useState } from 'react';
import { 
  View, 
  Text, 
  FlatList, 
  TouchableOpacity, 
  StyleSheet, 
  ActivityIndicator, 
  Alert, 
  RefreshControl, 
  StatusBar, 
  Modal, 
  TextInput, 
  KeyboardAvoidingView, 
  Platform 
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig'; 
import { Ionicons } from '@expo/vector-icons';

export default function DashboardScreen() {
  const [jadwal, setJadwal] = useState([]);
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedExam, setSelectedExam] = useState(null);
  const [tokenInput, setTokenInput] = useState('');
  const [verifying, setVerifying] = useState(false);

  const router = useRouter();

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const response = await api.get('/jadwal');
      if (response && response.data) {
        setJadwal(response.data.data || []);
        if (response.data.user) {
          setUser(response.data.user);
          await AsyncStorage.setItem('userData', JSON.stringify(response.data.user));
        }
      }
    } catch (error) {
      console.log("Fetch Error:", error.message);
      try {
        const localUser = await AsyncStorage.getItem('userData');
        if (localUser) setUser(JSON.parse(localUser));
      } catch (storageErr) {
        console.log("Gagal ambil storage lokal");
      }
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  // --- LOGIKA UTAMA EKSEKUSI API LOGOUT ---
  const executeLogout = async () => {
    setLoading(true);
    try {
      // Tembak server backend Laravel untuk reset status device is_logged_in = 0
      await api.post('/logout'); 
      console.log("Server merespon sukses logout.");
    } catch (e) {
      console.log("Bypass API: Server tidak merespon atau offline, langsung paksa logout lokal.");
    } finally {
      try {
        // Hapus token session di HP agar bersih total
        await AsyncStorage.removeItem('userToken');
        await AsyncStorage.removeItem('userData');
        setModalVisible(false);
        
        // Kembalikan siswa ke screen login
        router.replace('/(auth)/login');
      } catch (err) {
        console.log("Gagal membersihkan storage lokal");
      } finally {
        setLoading(false);
      }
    }
  };

  // --- FUNGSI TRIGGER ALERTS YANG COMPATIBLE UNTUK WEB & APK ANDROID ---
  const handleLogoutPress = () => {
    console.log("Tombol Keluar Ditekan!");

    if (Platform.OS === 'web') {
      // Jalankan konfirmasi window browser jika diuji coba di Chrome laptop
      const confirmWeb = window.confirm("Apakah Anda yakin ingin keluar dari aplikasi?");
      if (confirmWeb) {
        executeLogout();
      }
    } else {
      // Jalankan Alert Native UI jika dijalankan dalam bentuk APK HP Android
      Alert.alert("Konfirmasi", "Apakah Anda yakin ingin keluar?", [
        { text: "Batal", style: "cancel" },
        { text: "Keluar", style: "destructive", onPress: executeLogout }
      ]);
    }
  };

  const formatTanggal = (dateString) => {
    if (!dateString) return '-';
    try {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      }).format(date);
    } catch (e) {
      return dateString;
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    fetchData();
  };

  const handleOpenTokenModal = (exam) => {
    setSelectedExam(exam);
    setTokenInput('');
    setModalVisible(true);
  };

  const handleVerifyToken = async () => {
    if (!tokenInput) return Alert.alert('Error', 'Masukkan token ujian!');
    setVerifying(true);
    try {
      await api.post(`/ujian/${selectedExam.id}/verify-token`, { token: tokenInput });
      setModalVisible(false);
      router.push({
        pathname: '/ujian/petunjuk',
        params: { id: selectedExam.id, token: tokenInput }
      });
    } catch (e) {
      Alert.alert('Gagal', e.response?.data?.message || 'Token tidak valid');
    } finally {
      setVerifying(false);
    }
  };

  const renderJadwal = ({ item }) => {
    const isFinished = item.is_finished;
    const isActive = item.status === 'aktif' && !isFinished;
    const primaryRed = '#c91313';
    const themeColor = isFinished ? '#10b981' : (isActive ? primaryRed : '#94a3b8');

    return (
      <View style={[styles.card, isActive && styles.cardActive]}>
        <View style={styles.cardHeader}>
          <View style={{ flex: 1 }}>
            <Text style={styles.mapel}>{item.subject?.nama_mapel ?? 'Mapel'}</Text>
            <Text style={[styles.examType, { color: isActive ? primaryRed : '#64748b' }]}>
                {item.exam_type?.name ?? 'Ujian'}
            </Text>
          </View>
          <View style={[styles.badge, { backgroundColor: themeColor + '15' }]}>
            <Text style={[styles.badgeText, { color: themeColor }]}>
              {isFinished ? 'SELESAI' : (item.status ? item.status.toUpperCase() : 'NONAKTIF')}
            </Text>
          </View>
        </View>

        <View style={styles.footer}>
            <View style={styles.timeGroup}>
                <Ionicons name="timer-outline" size={16} color="#1e293b" />
                <Text style={styles.timeText}>{item.durasi} Menit</Text>
            </View>
            <View style={styles.timeGroup}>
                <Ionicons name="calendar-outline" size={14} color="#94a3b8" />
                <Text style={styles.dateText}>{formatTanggal(item.tanggal_ujian)}</Text>
            </View>
        </View>

        <TouchableOpacity 
          activeOpacity={0.8}
          style={[styles.btnAction, { backgroundColor: isFinished ? '#1e293b' : (isActive ? primaryRed : '#e2e8f0') }]}
          onPress={() => {
            if (isActive) {
              handleOpenTokenModal(item);
            } else if (isFinished) {
              router.push({
                pathname: '/ujian/rekap',
                params: { id: item.id, token: item.token }
              });
            }
          }}
        >
          <Text style={[styles.btnText, { color: (isActive || isFinished) ? '#fff' : '#94a3b8' }]}>
            {isFinished ? 'LIHAT HASIL' : (isActive ? 'MASUK UJIAN' : 'BELUM AKTIF')}
          </Text>
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />
      
      {/* HEADER CONTAINER FIXED TOP */}
      <View style={styles.header}>
        <View style={styles.headerLeft}>
            <Text style={styles.welcome}>Selamat Datang,</Text>
            <Text style={styles.userName} numberOfLines={1}>{user?.name ?? 'Siswa Percobaan'}</Text>
            <View style={styles.classBadge}>
                <Text style={styles.classText}>{user?.classroom?.nama_kelas ?? 'XII RPL'}</Text>
            </View>
        </View>
        
        {/* Tombol Logout Terisolasi Sempurna */}
        <TouchableOpacity 
          onPress={handleLogoutPress} 
          style={styles.logoutBtn} 
          activeOpacity={0.4}
        >
            <Ionicons name="log-out-outline" size={26} color="#c91313" />
        </TouchableOpacity>
      </View>

      {/* BODY CONTENT CONTAINER */}
      <View style={styles.body}>
        {loading && !refreshing ? (
          <View style={styles.center}>
            <ActivityIndicator size="large" color="#c91313" />
          </View>
        ) : (
          <FlatList
            data={jadwal}
            renderItem={renderJadwal}
            keyExtractor={(item) => item.id.toString()}
            contentContainerStyle={{ padding: 20 }}
            refreshControl={
              <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#c91313" />
            }
          />
        )}
      </View>

      {/* MODAL TOKEN */}
      <Modal animationType="fade" transparent={true} visible={modalVisible} onRequestClose={() => setModalVisible(false)}>
        <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Verifikasi Token</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={24} color="#64748b" />
              </TouchableOpacity>
            </View>
            
            <Text style={styles.modalSubTitle}>
              Silahkan masukkan kode token untuk mata pelajaran:{"\n"}
              <Text style={{fontWeight: '800', color: '#1e293b'}}>{selectedExam?.subject?.nama_mapel}</Text>
            </Text>

            <TextInput
              style={styles.tokenInput}
              placeholder="TOKEN"
              value={tokenInput}
              onChangeText={setTokenInput}
              autoCapitalize="characters"
              maxLength={6}
            />

            <TouchableOpacity style={styles.modalBtn} onPress={handleVerifyToken}>
              <Text style={styles.modalBtnText}>KONFIRMASI MASUK</Text>
            </TouchableOpacity>
          </View>
        </KeyboardAvoidingView>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingTop: 50 },
  body: { flex: 1, zIndex: 1 }, 
  header: { 
    flexDirection: 'row', 
    alignItems: 'center',
    justifyContent: 'space-between', 
    paddingHorizontal: 25, 
    paddingTop: 60, 
    paddingBottom: 25, 
    backgroundColor: '#fff', 
    borderBottomLeftRadius: 30, 
    borderBottomRightRadius: 30, 
    elevation: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 5,
    zIndex: 999, 
  },
  headerLeft: {
    flex: 1,
    alignItems: 'flex-start',
  },
  logoutBtn: {
    padding: 12,
    backgroundColor: '#fee2e2',
    borderRadius: 12,
    marginLeft: 15,
    elevation: 2,
  },
  welcome: { fontSize: 13, color: '#64748b' },
  userName: { fontSize: 20, fontWeight: '800', color: '#1e293b' },
  classBadge: { alignSelf: 'flex-start', backgroundColor: '#c91313', paddingHorizontal: 12, paddingVertical: 4, borderRadius: 8, marginTop: 5 },
  classText: { color: '#fff', fontSize: 12, fontWeight: '700' },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 24, marginBottom: 18, elevation: 3 },
  cardActive: { borderLeftWidth: 6, borderLeftColor: '#c91313' },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15 },
  mapel: { fontSize: 18, fontWeight: '700', color: '#1e293b' },
  examType: { fontSize: 11, fontWeight: '700', textTransform: 'uppercase' },
  badge: { paddingHorizontal: 10, paddingVertical: 6, borderRadius: 12 },
  badgeText: { fontSize: 10, fontWeight: '800' },
  footer: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15, alignItems: 'center' },
  timeGroup: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  timeText: { fontSize: 15, fontWeight: '800' },
  dateText: { fontSize: 12, color: '#64748b', fontWeight: '600' },
  btnAction: { padding: 16, borderRadius: 15, alignItems: 'center' },
  btnText: { fontWeight: '800', fontSize: 14 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', padding: 20 },
  modalContent: { width: '100%', backgroundColor: '#fff', borderRadius: 25, padding: 25, elevation: 10 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 },
  modalTitle: { fontSize: 20, fontWeight: '800', color: '#1e293b' },
  modalSubTitle: { fontSize: 14, color: '#64748b', marginBottom: 20, lineHeight: 20 },
  tokenInput: { backgroundColor: '#f1f5f9', padding: 18, borderRadius: 15, fontSize: 22, fontWeight: '800', textAlign: 'center', letterSpacing: 5, color: '#c91313', marginBottom: 20, borderWidth: 1, borderColor: '#e2e8f0' },
  modalBtn: { backgroundColor: '#c91313', padding: 18, borderRadius: 15, alignItems: 'center' },
  modalBtnText: { color: '#fff', fontWeight: '800', fontSize: 16 }
});