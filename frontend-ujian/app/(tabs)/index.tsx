import React, { useEffect, useState } from 'react';
import { 
  View, 
  Text, 
  FlatList, 
  TouchableOpacity, 
  StyleSheet, 
  ActivityIndicator, 
  RefreshControl, 
  StatusBar, 
  Modal, 
  TextInput, 
  KeyboardAvoidingView, 
  Platform,
  SafeAreaView
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
  
  // STATE MODAL TOKEN INPUT
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedExam, setSelectedExam] = useState(null);
  const [tokenInput, setTokenInput] = useState('');
  const [verifying, setVerifying] = useState(false);

  // STATE: CUSTOM ALERT MODAL
  const [customAlert, setCustomAlert] = useState({
    visible: false,
    title: '',
    message: '',
    type: 'info', // 'info' atau 'konfirmasi'
    onConfirm: null
  });

  const router = useRouter();
  const primaryRed = '#c91313'; // Warna Identitas Sebstar

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

  const executeLogout = async () => {
    setLoading(true);
    try {
      await api.post('/logout'); 
    } catch (e) {
      console.log("Bypass API Logout.");
    } finally {
      try {
        await AsyncStorage.removeItem('userToken');
        await AsyncStorage.removeItem('userData');
        setModalVisible(false);
        setCustomAlert(prev => ({ ...prev, visible: false }));
        router.replace('/(auth)/login');
      } catch (err) {
        console.log("Gagal membersihkan storage lokal");
      } finally {
        setLoading(false);
      }
    }
  };

  const bukaAlertKustom = (title, message, type = 'info', onConfirmCallback = null) => {
    setCustomAlert({
      visible: true,
      title: title,
      message: message,
      type: type,
      onConfirm: onConfirmCallback
    });
  };

  const handleLogoutPress = () => {
    bukaAlertKustom(
      "Keluar Akun", 
      "Apakah Anda yakin ingin keluar dari akun ujian Anda?", 
      "konfirmasi", 
      executeLogout
    );
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
    if (!tokenInput) {
      bukaAlertKustom("Validasi Gagal", "Silakan masukkan kode token ujian terlebih dahulu!", "info");
      return;
    }
    
    setVerifying(true);
    try {
      await api.post(`/ujian/${selectedExam.id}/verify-token`, { token: tokenInput });
      setModalVisible(false);
      router.push({
        pathname: '/ujian/petunjuk',
        params: { id: selectedExam.id, token: tokenInput }
      });
    } catch (e) {
      const msg = e.response?.data?.message || 'Kode token yang Anda masukkan tidak valid.';
      bukaAlertKustom("Verifikasi Gagal", msg, "info");
    } finally {
      setVerifying(false);
    }
  };

  const renderEmptyList = () => (
    <View style={styles.emptyContainer}>
      <View style={styles.emptyIconCircle}>
        <Ionicons name="file-tray-outline" size={40} color="#94a3b8" />
      </View>
      <Text style={styles.emptyTitle}>Belum Ada Jadwal</Text>
      <Text style={styles.emptySubtitle}>Jadwal ujian aktif atau riwayat pengerjaan Anda akan muncul di sini.</Text>
    </View>
  );

  const renderListHeader = () => (
    <View style={styles.listHeader}>
      <Text style={styles.listHeaderTitle}>Daftar Ujian Anda</Text>
      <Text style={styles.listHeaderSubtitle}>Pilih ujian yang tersedia untuk mulai mengerjakan</Text>
    </View>
  );

  const renderJadwal = ({ item }) => {
    const isFinished = item.is_finished;
    const isActive = item.status === 'aktif' && !isFinished;
    const themeColor = isFinished ? '#10b981' : (isActive ? primaryRed : '#64748b');
    const badgeBg = isFinished ? '#f0fdf4' : (isActive ? '#fff1f2' : '#f1f5f9');

    return (
      <View style={[styles.card, isActive && styles.cardActive]}>
        <View style={styles.cardMainInfo}>
          <View style={{ flex: 1, paddingRight: 10 }}>
            <Text style={styles.subjectName} numberOfLines={2}>
              {item.subject?.nama_mapel ?? 'Mata Pelajaran'}
            </Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: badgeBg }]}>
            <Text style={[styles.statusBadgeText, { color: themeColor }]}>
              {isFinished ? 'SELESAI' : (item.status ? item.status.toUpperCase() : 'NONAKTIF')}
            </Text>
          </View>
        </View>

        <View style={styles.cardDetailsRow}>
          <View style={styles.detailItem}>
            <View style={styles.detailIconBox}>
               <Ionicons name="time" size={14} color="#64748b" />
            </View>
            <Text style={styles.detailText}>{item.durasi} Menit</Text>
          </View>
          <View style={styles.detailItem}>
            <View style={styles.detailIconBox}>
               <Ionicons name="calendar" size={14} color="#64748b" />
            </View>
            <Text style={styles.detailText}>{formatTanggal(item.tanggal_ujian)}</Text>
          </View>
        </View>

        <TouchableOpacity 
          activeOpacity={0.8}
          style={[
            styles.actionButton, 
            isFinished ? styles.btnFinished : (isActive ? styles.btnActive : styles.btnInactive)
          ]}
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
          disabled={!isActive && !isFinished}
        >
          <Text style={[styles.actionButtonText, { color: (isActive || isFinished) ? '#fff' : '#94a3b8' }]}>
            {isFinished ? 'LIHAT HASIL' : (isActive ? 'MASUK UJIAN' : 'BELUM AKTIF')}
          </Text>
          {(isActive || isFinished) && (
            <Ionicons name="chevron-forward" size={16} color="#fff" />
          )}
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#c91313" />
      
      {/* HEADER KOTAK TEGAS SEJAJAR DENGAN KERJAKAN.TSX */}
      <View style={styles.header}>
        <View style={styles.headerProfile}>
          <View style={styles.avatarCircle}>
            <Ionicons name="person" size={22} color="#c91313" />
          </View>
          <View>
            <Text style={styles.welcomeText}>Selamat Datang,</Text>
            <Text style={styles.userNameText}>{user?.name ?? 'Siswa Ujian'}</Text>
          </View>
        </View>
        <TouchableOpacity onPress={handleLogoutPress} style={styles.logoutIconButton}>
          <Ionicons name="log-out-outline" size={26} color="#fff" />
        </TouchableOpacity>
      </View>

      <View style={styles.body}>
        {loading && !refreshing ? (
          <View style={styles.center}>
            <ActivityIndicator size="large" color={primaryRed} />
          </View>
        ) : (
          <FlatList
            data={jadwal}
            renderItem={renderJadwal}
            keyExtractor={(item) => item.id.toString()}
            contentContainerStyle={styles.scrollContainer}
            ListHeaderComponent={jadwal.length > 0 ? renderListHeader : null}
            ListEmptyComponent={renderEmptyList}
            showsVerticalScrollIndicator={false}
            refreshControl={
              <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={primaryRed} colors={[primaryRed]} />
            }
          />
        )}
      </View>

      {/* MODAL TOKEN INPUT */}
      {modalVisible && (
        <Modal animationType="fade" transparent={true} visible={modalVisible} onRequestClose={() => setModalVisible(false)}>
          <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.modalOverlay}>
            <View style={styles.modalContent}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Verifikasi Token</Text>
                <TouchableOpacity onPress={() => setModalVisible(false)} style={styles.closeBtn}>
                  <Ionicons name="close" size={20} color="#64748b" />
                </TouchableOpacity>
              </View>
              
              <Text style={styles.modalSubTitle}>
                Silakan masukkan token ujian untuk:{"\n"}
                <Text style={{fontWeight: '800', color: '#1e293b'}}>{selectedExam?.subject?.nama_mapel}</Text>
              </Text>

              <View style={styles.tokenWrapper}>
                <TextInput
                  style={styles.tokenInput}
                  placeholder="TOKEN"
                  placeholderTextColor="#94a3b8"
                  value={tokenInput}
                  onChangeText={setTokenInput}
                  autoCapitalize="characters"
                  maxLength={6}
                />
              </View>

              <TouchableOpacity style={styles.modalBtn} onPress={handleVerifyToken} disabled={verifying}>
                {verifying ? (
                  <ActivityIndicator color="#fff" size="small" />
                ) : (
                  <Text style={styles.modalBtnText}>KONFIRMASI MASUK</Text>
                )}
              </TouchableOpacity>
            </View>
          </KeyboardAvoidingView>
        </Modal>
      )}

      {/* CUSTOM MODAL ALERT */}
      {customAlert.visible && (
        <Modal animationType="fade" transparent={true} visible={customAlert.visible}>
          <View style={styles.modalOverlay}>
            <View style={styles.customAlertCard}>
              <View style={[styles.alertIconWrapper, { backgroundColor: customAlert.type === 'konfirmasi' ? '#fef2f2' : '#f8fafc' }]}>
                <Ionicons 
                  name={customAlert.type === 'konfirmasi' ? "log-out" : "information-circle"} 
                  size={28} 
                  color={customAlert.type === 'konfirmasi' ? primaryRed : '#64748b'} 
                />
              </View>
              <Text style={styles.customAlertTitle}>{customAlert.title}</Text>
              <Text style={styles.customAlertMessage}>{customAlert.message}</Text>
              
              <View style={{ flexDirection: 'row', gap: 10, width: '100%' }}>
                {customAlert.type === 'konfirmasi' && (
                  <TouchableOpacity 
                    style={styles.alertCancelBtn} 
                    onPress={() => setCustomAlert(prev => ({ ...prev, visible: false }))}
                  >
                    <Text style={styles.alertCancelText}>Batal</Text>
                  </TouchableOpacity>
                )}
                <TouchableOpacity 
                  style={[styles.alertConfirmBtn, { backgroundColor: primaryRed }]} 
                  onPress={() => {
                    if (customAlert.type === 'konfirmasi' && customAlert.onConfirm) {
                      customAlert.onConfirm();
                    } else {
                      setCustomAlert(prev => ({ ...prev, visible: false }));
                    }
                  }}
                >
                  <Text style={styles.alertConfirmText}>
                    {customAlert.type === 'konfirmasi' ? 'Ya, Keluar' : 'OK'}
                  </Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </Modal>
      )}

    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  body: { flex: 1 }, 
  
  // HEADER KOTAK TEGAS SEJAJAR DENGAN KERJAKAN.TSX (TANPA BORDER RADIUS KELENGKUNGAN)
  header: { 
    backgroundColor: '#c91313', 
    height: 75,
    paddingHorizontal: 20,
    paddingTop: Platform.OS === 'android' ? 10 : 0,
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    zIndex: 10
  },
  headerProfile: { flexDirection: 'row', alignItems: 'center', gap: 12, flex: 1 },
  avatarCircle: { width: 40, height: 40, borderRadius: 20, backgroundColor: '#ffffff', justifyContent: 'center', alignItems: 'center' },
  welcomeText: { color: '#fca5a5', fontSize: 11, fontWeight: '700', textTransform: 'uppercase', letterSpacing: 0.5 },
  userNameText: { color: '#ffffff', fontSize: 16, fontWeight: '800', marginTop: 2 },
  logoutIconButton: { padding: 5, width: 35 },

  // SCROLL CONTAINER
  scrollContainer: { padding: 20, paddingBottom: 50 },
  listHeader: { marginBottom: 15 },
  listHeaderTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  listHeaderSubtitle: { fontSize: 12, color: '#64748b', marginTop: 2 },

  // KARTU UJIAN PREMIUM (SAMA SEPERTI CARD SOAL)
  card: { 
    backgroundColor: '#fff', 
    padding: 20, 
    borderRadius: 20, 
    elevation: 3, 
    shadowColor: '#000', 
    shadowOffset: { width: 0, height: 2 }, 
    shadowOpacity: 0.05, 
    shadowRadius: 3, 
    marginBottom: 15,
    borderWidth: 1,
    borderColor: '#f1f5f9'
  },
  cardActive: { 
    borderColor: '#fca5a5',
    backgroundColor: '#fffdfd',
    borderLeftWidth: 4,
    borderLeftColor: '#c91313'
  },
  cardMainInfo: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 15 },
  subjectName: { fontSize: 17, fontWeight: '700', color: '#1e293b', lineHeight: 24 },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8 },
  statusBadgeText: { fontSize: 10, fontWeight: '800', letterSpacing: 0.5 },
  
  cardDetailsRow: { flexDirection: 'row', gap: 15, marginBottom: 20 },
  detailItem: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  detailIconBox: { backgroundColor: '#f1f5f9', padding: 6, borderRadius: 8 },
  detailText: { fontSize: 13, color: '#475569', fontWeight: '600' },
  
  actionButton: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', padding: 14, borderRadius: 14, gap: 8 },
  actionButtonText: { fontWeight: '800', fontSize: 13, letterSpacing: 0.5 },
  btnActive: { backgroundColor: '#c91313' },
  btnFinished: { backgroundColor: '#1e293b' },
  btnInactive: { backgroundColor: '#f1f5f9' },

  // EMPTY STATE
  emptyContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingVertical: 80 },
  emptyIconCircle: { width: 80, height: 80, borderRadius: 40, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center', marginBottom: 15 },
  emptyTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  emptySubtitle: { fontSize: 13, color: '#64748b', textAlign: 'center', marginTop: 6, paddingHorizontal: 30, lineHeight: 20 },

  // MODAL & ALERT (Rounded 20 seperti card soal)
  modalOverlay: { flex: 1, backgroundColor: 'rgba(15, 23, 42, 0.6)', justifyContent: 'center', alignItems: 'center', padding: 20 },
  modalContent: { width: '100%', backgroundColor: '#ffffff', borderRadius: 20, padding: 25, elevation: 10 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15 },
  modalTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  modalSubTitle: { fontSize: 14, color: '#64748b', marginBottom: 20, lineHeight: 22 },
  closeBtn: { backgroundColor: '#f1f5f9', padding: 8, borderRadius: 10 },
  
  tokenWrapper: { backgroundColor: '#f8fafc', borderRadius: 15, borderWidth: 1, borderColor: '#e2e8f0', marginBottom: 20 },
  tokenInput: { padding: 15, fontSize: 24, fontWeight: '800', textAlign: 'center', letterSpacing: 8, color: '#c91313' },
  modalBtn: { backgroundColor: '#c91313', padding: 15, borderRadius: 15, alignItems: 'center' },
  modalBtnText: { color: '#ffffff', fontWeight: '800', fontSize: 14, letterSpacing: 0.5 },

  customAlertCard: { width: '90%', backgroundColor: '#ffffff', padding: 25, borderRadius: 20, alignItems: 'center', elevation: 10 },
  alertIconWrapper: { padding: 12, borderRadius: 50, marginBottom: 15 },
  customAlertTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b', marginBottom: 8, textAlign: 'center' },
  customAlertMessage: { fontSize: 14, color: '#64748b', fontWeight: '500', marginBottom: 25, textAlign: 'center', lineHeight: 20 },
  alertConfirmBtn: { flex: 1, paddingVertical: 14, borderRadius: 12, alignItems: 'center' },
  alertConfirmText: { color: '#ffffff', fontWeight: '800', fontSize: 14 },
  alertCancelBtn: { flex: 1, paddingVertical: 14, borderRadius: 12, alignItems: 'center', backgroundColor: '#f1f5f9' },
  alertCancelText: { color: '#475569', fontWeight: '800', fontSize: 14 }
});