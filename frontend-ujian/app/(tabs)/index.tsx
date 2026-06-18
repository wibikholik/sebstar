import React from 'react';
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
import { Stack } from 'expo-router'; 
import { Ionicons } from '@expo/vector-icons';
import { useDashboardLogic } from '../../hooks/useDashboardLogic'; // Sesuaikan dengan path folder hooks Anda

export default function DashboardScreen() {
  const {
    jadwal,
    user,
    loading,
    refreshing,
    modalVisible,
    setModalVisible,
    selectedExam,
    tokenInput,
    setTokenInput,
    verifying,
    customAlert,
    setCustomAlert,
    primaryRed,
    router,
    onRefresh,
    handleLogoutPress,
    formatTanggal,
    handleOpenTokenModal,
    handleVerifyToken
  } = useDashboardLogic();

  const renderEmptyList = () => (
    <View style={styles.emptyContainer}>
      <View style={styles.emptyIconCircle}>
        <Ionicons name="file-tray-outline" size={40} color="#94a3b8" />
      </View>
      <Text style={styles.emptyTitle}>Belum Ada Jadwal</Text>
      <Text style={styles.emptySubtitle}>Jadwal ujian aktif Anda akan muncul di sini.</Text>
    </View>
  );

  // 🛠️ FIX PARSING: Menyesuaikan murni dengan properti "teachers_data" dari JSON Laragon Anda
  const renderTeacherName = (item: any) => {
    if (item.teachers_data && Array.isArray(item.teachers_data) && item.teachers_data.length > 0) {
      return item.teachers_data.map((t: any) => t.name).join(', ');
    }
    return item.teacher?.name ?? item.guru_pengampu ?? 'Guru Pengampu';
  };

  const renderJadwal = ({ item }: { item: any }) => {
    const isFinished = item.is_finished;
    const isActive = item.status === 'aktif' && !isFinished;
    const themeColor = isFinished ? '#10b981' : (isActive ? primaryRed : '#64748b');
    const badgeBg = isFinished ? '#f0fdf4' : (isActive ? '#fff1f2' : '#f1f5f9');

    return (
      <View style={[styles.card, isActive && styles.cardActive]}>
        <View style={styles.cardMainInfo}>
          <View style={{ flex: 1, paddingRight: 10 }}>
            {/* NAMA MATA PELAJARAN */}
            <Text style={styles.subjectName} numberOfLines={2}>
              {item.subject?.nama_mapel ?? 'Mata Pelajaran'}
            </Text>
            
            {/* BARIS METADATA: KELAS | TIPE UJIAN | GURU */}
            <View style={styles.metaContainer}>
              {/* KELAS */}
              <View style={styles.metaItem}>
                <Ionicons name="school" size={13} color="#64748b" />
                <Text style={styles.metaText}>
                  {item.classroom?.nama_kelas ?? item.kelas ?? '-'}
                </Text>
              </View>
              <View style={styles.metaDivider} />
              
              {/* TIPE UJIAN */}
              <View style={styles.metaItem}>
                <Ionicons name="bookmark" size={13} color="#64748b" />
                <Text style={styles.metaText}>
                  {item.exam_type?.name ?? item.jenis_ujian ?? 'Ujian'}
                </Text>
              </View>
              <View style={styles.metaDivider} />

              {/* GURU PENGAMPU (MENEMBAK TEACHERS_DATA) */}
              <View style={styles.metaItem}>
                <Ionicons name="person-circle" size={13} color="#64748b" />
                <Text style={styles.metaText} numberOfLines={1}>
                  {renderTeacherName(item)}
                </Text>
              </View>
            </View>
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
      {/* MENYEMBUNYIKAN HEADER PUTIH ASLI EXPO ROUTER */}
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar barStyle="light-content" backgroundColor="#c91313" />
      
      {/* HEADER AKUN UTAMA MERAH KUSTOM */}
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

      {/* BODY BACKGROUND SLATE ORIGINAL */}
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
            ListHeaderComponent={null}
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

      {/* CUSTOM ALERT */}
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
  body: { flex: 1, backgroundColor: '#f8fafc' }, 
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
  scrollContainer: { padding: 20, paddingTop: 20, paddingBottom: 50 },
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
    borderLeftWidth: 5,
    borderLeftColor: '#c91313' 
  },
  cardMainInfo: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 15 },
  subjectName: { fontSize: 17, fontWeight: '700', color: '#1e293b', lineHeight: 24 },
  
  // METADATA GRID STYLING
  metaContainer: { flexDirection: 'row', alignItems: 'center', marginTop: 6, flexWrap: 'wrap', gap: 2 },
  metaItem: { flexDirection: 'row', alignItems: 'center', gap: 4, marginVertical: 2 },
  metaText: { fontSize: 12, fontWeight: '600', color: '#64748b' },
  metaDivider: { width: 1, height: 12, backgroundColor: '#cbd5e1', marginHorizontal: 6 },

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
  emptyContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', paddingVertical: 80 },
  emptyIconCircle: { width: 80, height: 80, borderRadius: 40, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center', marginBottom: 15 },
  emptyTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  emptySubtitle: { fontSize: 13, color: '#64748b', textAlign: 'center', marginTop: 6, paddingHorizontal: 30, lineHeight: 20 },
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