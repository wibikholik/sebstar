import React, { useEffect, useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  TouchableOpacity, 
  ActivityIndicator, 
  Alert, 
  StatusBar, 
  Modal, 
  TextInput, 
  Platform, 
  KeyboardAvoidingView, 
  ScrollView 
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import api from '../../src/api/axiosConfig'; 
import { Ionicons } from '@expo/vector-icons';

export default function ProfileScreen() {
  const [user, setUser] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [btnLoading, setBtnLoading] = useState(false);
  
  // STATE UNTUK INPUT MODAL EDIT PROFILE
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [inputName, setInputName] = useState('');
  const [inputPassword, setInputPassword] = useState('');

  const router = useRouter();

  useEffect(() => {
    getProfileData();
  }, []);

  const getProfileData = async () => {
    try {
      const localUser = await AsyncStorage.getItem('userData');
      if (localUser) {
        const parsedUser = JSON.parse(localUser);
        setUser(parsedUser);
        // Set default value input nama sesuai user terlogin
        setInputName(parsedUser?.name || '');
      }
    } catch (e: any) {
      console.log("Gagal memuat data user lokal:", e.message);
    } finally {
      setLoading(false);
    }
  };

  // CROSS-PLATFORM ALERT SYSTEM
  const showNotification = (title: string, message: string) => {
    if (Platform.OS === 'web') {
      alert(`${title}\n\n${message}`);
    } else {
      Alert.alert(title, message);
    }
  };

  // LOGIKA SIMPAN PERUBAHAN PROFILE KE LARAVEL
  const handleUpdateProfile = async () => {
    if (!inputName.trim()) {
      showNotification('Peringatan', 'Nama tidak boleh kosong');
      return;
    }

    setBtnLoading(true);
    try {
      // Mengirim data perubahan ke endpoint API laravel
      const response = await api.post('/update-profile', {
        name: inputName,
        password: inputPassword || undefined, // password dikirim hanya jika diisi siswa
      });

      // Anggap backend mengembalikan data user terbaru di response.data.user
      const updatedUser = {
        ...user,
        name: inputName,
      };

      // Perbarui data di Local Storage agar halaman lain ikut berubah otomatis
      if (Platform.OS === 'web') {
        localStorage.setItem('userData', JSON.stringify(updatedUser));
      } else {
        await AsyncStorage.setItem('userData', JSON.stringify(updatedUser));
      }

      setUser(updatedUser);
      setIsModalOpen(false);
      setInputPassword(''); // Reset input password kembali kosong
      showNotification('Berhasil', 'Profil kamu berhasil diperbarui!');
      
    } catch (error: any) {
      console.log('Gagal update profile:', error.response?.data);
      showNotification(
        'Gagal Menyimpan', 
        error.response?.data?.message || 'Terjadi kesalahan sistem saat memperbarui profil.'
      );
    } finally {
      setBtnLoading(false);
    }
  };

  // LOGIKA LOGOUT
  const handleSystemLogout = () => {
    const processLogout = async () => {
      setLoading(true);
      try {
        await api.post('/logout');
      } catch (e) {
        console.log("Server offline, lanjut membersihkan storage perangkat.");
      } finally {
        try {
          if (Platform.OS === 'web') {
            localStorage.removeItem('userToken');
            localStorage.removeItem('userData');
          } else {
            await AsyncStorage.removeItem('userToken');
            await AsyncStorage.removeItem('userData');
          }
          router.replace('/(auth)/login');
        } catch (err) {
          showNotification("Error", "Gagal menghapus session login perangkat.");
        } finally {
          setLoading(false);
        }
      }
    };

    if (Platform.OS === 'web') {
      if (confirm("Apakah Anda yakin ingin keluar dari akun ini?")) {
        processLogout();
      }
    } else {
      Alert.alert("Konfirmasi Keluar", "Apakah Anda yakin ingin keluar dari akun ini?", [
        { text: "Batal", style: "cancel" },
        { text: "Keluar", style: "destructive", onPress: processLogout }
      ]);
    }
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
      <StatusBar barStyle="dark-content" backgroundColor="#f8fafc" />
      
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        
        {/* AVATAR HEADER */}
        <View style={styles.avatarContainer}>
          <View style={styles.avatarIconBG}>
            <Ionicons name="person" size={55} color="#c91313" />
          </View>
          <Text style={styles.nameText}>{user?.name ?? 'Siswa Percobaan'}</Text>
          <Text style={styles.subText}>NIS: {user?.nis ?? '-'}</Text>
        </View>

        {/* INFO DATA UTAMA (BISA DI-EDIT & PERMANEN) */}
        <View style={styles.infoBox}>
          
          {/* Kelompok Data Permanen (ReadOnly) */}
          <Text style={styles.sectionTitle}>Informasi Akademik (Permanen)</Text>
          
          <View style={styles.infoRowDisabled}>
            <Ionicons name="id-card-outline" size={20} color="#94a3b8" />
            <Text style={styles.infoLabelDisabled}>NIS:</Text>
            <Text style={styles.infoValueDisabled}>{user?.nis ?? '-'}</Text>
          </View>

          <View style={styles.infoRowDisabled}>
            <Ionicons name="school-outline" size={20} color="#94a3b8" />
            <Text style={styles.infoLabelDisabled}>Kelas:</Text>
            <Text style={styles.infoValueDisabled}>{user?.classroom?.nama_kelas ?? 'XII RPL'}</Text>
          </View>

          

        </View>

        {/* TOMBOL EDIT PROFILE POP-UP */}
        <TouchableOpacity 
          style={styles.editBtn} 
          onPress={() => {
            setInputName(user?.name || '');
            setIsModalOpen(true);
          }}
          activeOpacity={0.8}
        >
          <Ionicons name="create-outline" size={20} color="#fff" style={{ marginRight: 8 }} />
          <Text style={styles.editBtnText}>UBAH NAMA / PASSWORD</Text>
        </TouchableOpacity>

        {/* TOMBOL LOGOUT */}
        <TouchableOpacity style={styles.logoutBtn} onPress={handleSystemLogout} activeOpacity={0.7}>
          <Ionicons name="log-out" size={20} color="#c91313" style={{ marginRight: 8 }} />
          <Text style={styles.logoutText}>KELUAR DARI AKUN</Text>
        </TouchableOpacity>

      </ScrollView>

      {/* POPUP MODAL PENGUBAHAN DATA PROFILE */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={isModalOpen}
        onRequestClose={() => setIsModalOpen(false)}
      >
        <View style={styles.modalOverlay}>
          <KeyboardAvoidingView
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
            style={styles.modalContainer}
          >
            <View style={styles.modalCard}>
              
              {/* MODAL HEADER */}
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Ubah Profil</Text>
                <TouchableOpacity onPress={() => setIsModalOpen(false)}>
                  <Ionicons name="close" size={24} color="#64748b" />
                </TouchableOpacity>
              </View>

              {/* INPUT NAMA */}
              <Text style={styles.inputLabelText}>Nama Lengkap Siswa</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="person-outline" size={18} color="#94a3b8" style={{ marginRight: 10 }} />
                <TextInput
                  style={styles.textInput}
                  value={inputName}
                  onChangeText={setInputName}
                  placeholder="Ketik nama lengkap baru"
                  placeholderTextColor="#94a3b8"
                />
              </View>

              {/* INPUT PASSWORD */}
              <Text style={styles.inputLabelText}>Password Baru (Kosongkan jika tidak diganti)</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="lock-closed-outline" size={18} color="#94a3b8" style={{ marginRight: 10 }} />
                <TextInput
                  style={styles.textInput}
                  value={inputPassword}
                  onChangeText={setInputPassword}
                  placeholder="Minimal 6 karakter"
                  placeholderTextColor="#94a3b8"
                  secureTextEntry
                />
              </View>

              {/* ACTION ACTION BUTTON DI MODAL */}
              <View style={styles.modalActionRow}>
                <TouchableOpacity 
                  style={styles.cancelActionBtn} 
                  onPress={() => setIsModalOpen(false)}
                >
                  <Text style={styles.cancelActionText}>Batal</Text>
                </TouchableOpacity>

                <TouchableOpacity 
                  style={styles.saveActionBtn} 
                  onPress={handleUpdateProfile}
                  disabled={btnLoading}
                >
                  {btnLoading ? (
                    <ActivityIndicator color="#fff" size="small" />
                  ) : (
                    <Text style={styles.saveActionText}>Simpan</Text>
                  )}
                </TouchableOpacity>
              </View>

            </View>
          </KeyboardAvoidingView>
        </View>
      </Modal>

    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  scrollContent: { padding: 24, paddingBottom: 40 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  avatarContainer: { alignItems: 'center', marginTop: 20, marginBottom: 30 },
  avatarIconBG: { width: 100, height: 100, borderRadius: 50, backgroundColor: '#fee2e2', justifyContent: 'center', alignItems: 'center', marginBottom: 15, elevation: 2 },
  nameText: { fontSize: 22, fontWeight: '800', color: '#1e293b', textAlign: 'center' },
  subText: { fontSize: 14, color: '#64748b', marginTop: 4, fontWeight: '600' },
  
  infoBox: { backgroundColor: '#fff', padding: 20, borderRadius: 24, elevation: 1, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 10, marginBottom: 25 },
  sectionTitle: { fontSize: 13, fontWeight: '700', color: '#94a3b8', marginBottom: 12, textTransform: 'uppercase', letterSpacing: 0.5 },
  
  infoRowDisabled: { flexDirection: 'row', alignItems: 'center', paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#f1f5f9', opacity: 0.85 },
  infoLabelDisabled: { fontSize: 14, color: '#64748b', marginLeft: 10, flex: 1, fontWeight: '500' },
  infoValueDisabled: { fontSize: 14, fontWeight: '600', color: '#475569' },
  
  editBtn: { backgroundColor: '#c91313', padding: 16, borderRadius: 16, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', elevation: 3, shadowColor: '#c91313', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.2, shadowRadius: 8, marginBottom: 14, cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  editBtnText: { color: '#fff', fontSize: 14, fontWeight: '800', letterSpacing: 0.5 },
  
  logoutBtn: { backgroundColor: '#fff', padding: 16, borderRadius: 16, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', borderWidth: 1.5, borderColor: '#fee2e2', cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  logoutText: { color: '#c91313', fontSize: 14, fontWeight: '800', letterSpacing: 0.5 },

  // STYLING MODAL POPUP
  modalOverlay: { flex: 1, backgroundColor: 'rgba(15, 23, 42, 0.4)', justifyContent: 'center', alignItems: 'center', padding: 20 },
  modalContainer: { width: '100%', maxWidth: 420 },
  modalCard: { backgroundColor: '#fff', borderRadius: 24, padding: 24, shadowColor: '#000', shadowOffset: { width: 0, height: 10 }, shadowOpacity: 0.1, shadowRadius: 20, elevation: 10 },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20, borderBottomWidth: 1, borderBottomColor: '#f1f5f9', paddingBottom: 12 },
  modalTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b' },
  
  inputLabelText: { fontSize: 13, fontWeight: '700', color: '#64748b', marginBottom: 8, marginTop: 10 },
  inputContainer: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#f8fafc', borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 14, paddingHorizontal: 14, marginBottom: 15 },
  textInput: { flex: 1, paddingVertical: 12, fontSize: 14, color: '#1e293b', fontWeight: '600', outlineStyle: Platform.OS === 'web' ? 'none' : undefined },
  
  modalActionRow: { flexDirection: 'row', gap: 12, marginTop: 15 },
  cancelActionBtn: { flex: 1, backgroundColor: '#f1f5f9', paddingStyle: 14, paddingVertical: 14, borderRadius: 14, alignItems: 'center', cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  cancelActionText: { color: '#475569', fontSize: 14, fontWeight: '700' },
  saveActionBtn: { flex: 1, backgroundColor: '#c91313', paddingVertical: 14, borderRadius: 14, alignItems: 'center', cursor: Platform.OS === 'web' ? 'pointer' : 'auto' },
  saveActionText: { color: '#fff', fontSize: 14, fontWeight: '700' }
});