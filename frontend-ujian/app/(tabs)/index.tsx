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
      bukaAlertKustom("Validasi Gagal", "Silahkan masukkan kode token ujian terlebih dahulu!", "info");
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

  // RENDER SEAMLESS PROFILE HEADER INSIDE FLATLIST
  const renderHeaderComponent = () => (
    <View style={styles.profileSection}>
      <View style={styles.profileLeft}>
        <Text style={styles.welcomeText}>Selamat Datang</Text>
        <Text style={styles.userNameText}>{user?.name ?? 'Siswa Ujian'}</Text>
        <View style={styles.classRow}>
          <Ionicons name="school-outline" size={14} color="#64748b" />
          <Text style={styles.classText}>{user?.classroom?.nama_kelas ?? 'XII RPL'}</Text>
        </View>
      </View>
      
      <TouchableOpacity onPress={handleLogoutPress} style={styles.logoutIconButton} activeOpacity={0.6}>
        <Ionicons name="log-out-outline" size={20} color="#c91313" />
      </TouchableOpacity>
    </View>
  );

  const renderEmptyList = () => (
    <View style={styles.emptyContainer}>
      <Ionicons name="file-tray-outline" size={48} color="#94a3b8" />
      <Text style={styles.emptyTitle}>Belum ada jadwal ujian</Text>
      <Text style={styles.emptySubtitle}>Jadwal aktif atau riwayat ujian Anda akan terdaftar di sini.</Text>
    </View>
  );

  const renderJadwal = ({ item }) => {
    const isFinished = item.is_finished;
    const isActive = item.status === 'aktif' && !isFinished;
    const themeColor = isFinished ? '#10b981' : (isActive ? primaryRed : '#64748b');
    const badgeBg = isFinished ? '#f0fdf4' : (isActive ? '#fef2f2' : '#f8fafc');

    return (
      <View style={[styles.examCard, isActive && styles.examCardActive]}>
        <View style={styles.cardMainInfo}>
          <View style={{ flex: 1, paddingRight: 8 }}>
            <Text style={[styles.examBadgeText, { color: themeColor }]}>
              {item.exam_type?.name ?? 'UJIAN'}
            </Text>
            <Text style={styles.subjectName} numberOfLines={2}>
              {item.subject?.nama_mapel ?? 'Mata Pelajaran'}
            </Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: badgeBg, borderColor: themeColor + '25' }]}>
            <Text style={[styles.statusBadgeText, { color: themeColor }]}>
              {isFinished ? 'SELESAI' : (item.status ? item.status.toUpperCase() : 'NONAKTIF')}
            </Text>
          </View>
        </View>

        <View style={styles.cardDetailsRow}>
          <View style={styles.detailItem}>
            <Ionicons name="time-outline" size={14} color="#94a3b8" />
            <Text style={styles.detailText}>{item.durasi} Menit</Text>
          </View>
          <View style={styles.detailItem}>
            <Ionicons name="calendar-outline" size={14} color="#94a3b8" />
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
            <Ionicons name="arrow-forward" size={14} color="#fff" />
          )}
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
      
      <View style={styles.body}>
        {loading && !refreshing ? (
          <View style={styles.center}>
            <ActivityIndicator size="small" color={primaryRed} />
          </View>
        ) : (
          <FlatList
            data={jadwal}
            renderItem={renderJadwal}
            keyExtractor={(item) => item.id.toString()}
            contentContainerStyle={{ paddingHorizontal: 20, paddingBottom: 30 }}
            ListHeaderComponent={renderHeaderComponent}
            ListEmptyComponent={renderEmptyList}
            showsVerticalScrollIndicator={false}
            refreshControl={
              <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={primaryRed} colors={[primaryRed]} />
            }
          />
        )}
      </View>

      {/* MODAL TOKEN INPUT (MODERN FLAT STYLE) */}
      {modalVisible && (
        <Modal animationType="fade" transparent={true} visible={modalVisible} onRequestClose={() => setModalVisible(false)}>
          <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.modalOverlay}>
            <View style={styles.modalContent}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Verifikasi Token</Text>
                <TouchableOpacity onPress={() => setModalVisible(false)} style={styles.closeBtn}>
                  <Ionicons name="close" size={18} color="#64748b" />
                </TouchableOpacity>
              </View>
              
              <Text style={styles.modalSubTitle}>
                Silahkan masukkan kode token ujian untuk mata pelajaran:{"\n"}
                <Text style={{fontWeight: '700', color: '#0f172a'}}>{selectedExam?.subject?.nama_mapel}</Text>
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

      {/* CUSTOM MODAL ALERT (MODERN FLAT STYLE) */}
      {customAlert.visible && (
        <Modal animationType="fade" transparent={true} visible={customAlert.visible}>
          <View style={styles.modalOverlay}>
            <View style={styles.customAlertCard}>
              <View style={[styles.alertIconWrapper, { backgroundColor: customAlert.type === 'konfirmasi' ? '#fef2f2' : '#f8fafc' }]}>
                <Ionicons 
                  name={customAlert.type === 'konfirmasi' ? "log-out" : "information-circle"} 
                  size={24} 
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

    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#ffffff' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center', minHeight: 200 },
  body: { flex: 1 }, 
  
  // SEAMLESS INLINE PROFILE HEADER
  profileSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: Platform.OS === 'web' ? 20 : 50,
    paddingBottom: 20,
    borderBottomWidth: 1,
    borderColor: '#f1f5f9',
    marginBottom: 20,
  },
  profileLeft: { flex: 1 },
  welcomeText: { fontSize: 11, color: '#94a3b8', fontWeight: '600', textTransform: 'uppercase', letterSpacing: 0.5 },
  userNameText: { fontSize: 20, fontWeight: '700', color: '#0f172a', marginTop: 2 },
  classRow: { flexDirection: 'row', alignItems: 'center', gap: 4, marginTop: 4 },
  classText: { fontSize: 13, color: '#64748b', fontWeight: '500' },
  logoutIconButton: { padding: 8, borderRadius: 8, borderWidth: 1, borderColor: '#fee2e2', backgroundColor: '#fff5f5' },

  // EMPTY STATE
  emptyContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingVertical: 60 },
  emptyTitle: { fontSize: 15, fontWeight: '600', color: '#334155', marginTop: 10 },
  emptySubtitle: { fontSize: 13, color: '#94a3b8', textAlign: 'center', marginTop: 2, paddingHorizontal: 20 },

  // CRISP ULTRA-MODERN EXAM CARDS
  examCard: { 
    backgroundColor: '#ffffff', 
    padding: 16, 
    borderRadius: 12, 
    marginBottom: 12, 
    borderWidth: 1, 
    borderColor: '#e2e8f0' 
  },
  examCardActive: { 
    borderColor: '#c91313',
    backgroundColor: '#fffdfd'
  },
  cardMainInfo: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 12 },
  examBadgeText: { fontSize: 10, fontWeight: '700', letterSpacing: 0.5, marginBottom: 2 },
  subjectName: { fontSize: 16, fontWeight: '600', color: '#0f172a', lineHeight: 22 },
  statusBadge: { paddingHorizontal: 8, paddingVertical: 4, borderRadius: 6, borderWidth: 1 },
  statusBadgeText: { fontSize: 9, fontWeight: '700', letterSpacing: 0.5 },
  
  cardDetailsRow: { flexDirection: 'row', gap: 16, marginBottom: 16 },
  detailItem: { flexDirection: 'row', alignItems: 'center', gap: 5 },
  detailText: { fontSize: 13, color: '#64748b', fontWeight: '500' },
  
  actionButton: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', padding: 12, borderRadius: 8, gap: 6 },
  actionButtonText: { fontWeight: '600', fontSize: 13, letterSpacing: 0.2 },
  btnActive: { backgroundColor: '#c91313' },
  btnFinished: { backgroundColor: '#0f172a' },
  btnInactive: { backgroundColor: '#f1f5f9' },
  
  // MODAL TOKEN OVERLAYS (FLAT & MODERN)
  modalOverlay: { flex: 1, backgroundColor: 'rgba(15, 23, 42, 0.4)', justifyContent: 'center', alignItems: 'center', padding: 20 },
  modalContent: { width: Platform.OS === 'web' ? '380px' : '100%', backgroundColor: '#ffffff', borderRadius: 12, padding: 20, borderWidth: 1, borderColor: '#e2e8f0' },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  modalTitle: { fontSize: 18, fontWeight: '700', color: '#0f172a' },
  modalSubTitle: { fontSize: 13, color: '#64748b', marginBottom: 16, lineHeight: 18 },
  closeBtn: { backgroundColor: '#f1f5f9', padding: 6, borderRadius: 6 },
  
  tokenWrapper: { backgroundColor: '#f8fafc', borderRadius: 8, borderWidth: 1, borderColor: '#e2e8f0', marginBottom: 16 },
  tokenInput: { padding: 12, fontSize: 22, fontWeight: '700', textAlign: 'center', letterSpacing: 6, color: '#c91313' },
  modalBtn: { backgroundColor: '#c91313', padding: 14, borderRadius: 8, alignItems: 'center' },
  modalBtnText: { color: '#ffffff', fontWeight: '600', fontSize: 14 },

  // STYLES CUSTOM ALERT MODAL
  customAlertCard: { width: Platform.OS === 'web' ? '340px' : '90%', backgroundColor: '#ffffff', padding: 20, borderRadius: 12, alignItems: 'center', borderWidth: 1, borderColor: '#e2e8f0' },
  alertIconWrapper: { padding: 10, borderRadius: 50, marginBottom: 12 },
  customAlertTitle: { fontSize: 16, fontWeight: '700', color: '#0f172a', marginBottom: 6, textAlign: 'center' },
  customAlertMessage: { fontSize: 13, color: '#64748b', fontWeight: '500', marginBottom: 20, textAlign: 'center', lineHeight: 18 },
  alertConfirmBtn: { flex: 1, paddingVertical: 12, borderRadius: 8, alignItems: 'center' },
  alertConfirmText: { color: '#ffffff', fontWeight: '600', fontSize: 14 },
  alertCancelBtn: { flex: 1, paddingVertical: 12, borderRadius: 8, alignItems: 'center', backgroundColor: '#f1f5f9' },
  alertCancelText: { color: '#475569', fontWeight: '600', fontSize: 14 }
});