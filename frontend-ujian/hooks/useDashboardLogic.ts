import { useEffect, useState } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import api from '../src/api/axiosConfig'; // 🛠️ Perbaikan path relatif menuju folder api Anda

export function useDashboardLogic() {
  const [jadwal, setJadwal] = useState<any[]>([]);
  const [user, setUser] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  
  // STATE MODAL TOKEN INPUT
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedExam, setSelectedExam] = useState<any>(null);
  const [tokenInput, setTokenInput] = useState('');
  const [verifying, setVerifying] = useState(false);

  // STATE: CUSTOM ALERT MODAL
  const [customAlert, setCustomAlert] = useState({
    visible: false,
    title: '',
    message: '',
    type: 'info', // 'info' atau 'konfirmasi'
    onConfirm: null as (() => void) | null
  });

  const router = useRouter();
  const primaryRed = '#c91313';

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const response = await api.get('/jadwal');
      if (response && response.data) {
        const dataJadwal = response.data.data || [];
        setJadwal(dataJadwal);

        // 🔍 KUNCI DEBUGGING: Intip struktur JSON dari Laravel langsung di terminal Metro Bundler
        if (dataJadwal.length > 0) {
          console.log("=== [DEBUG SEBSTAR] STRUKTUR JADWAL PERTAMA ===");
          console.log(JSON.stringify(dataJadwal[0], null, 2));
        } else {
          console.log("=== [DEBUG SEBSTAR] JADWAL KOSONG ===");
        }

        if (response.data.user) {
          setUser(response.data.user);
          await AsyncStorage.setItem('userData', JSON.stringify(response.data.user));
        }
      }
    } catch (error: any) {
      console.log("⚠️ Fetch Error:", error.message);
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

  const bukaAlertKustom = (title: string, message: string, type = 'info', onConfirmCallback: (() => void) | null = null) => {
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

  const formatTanggal = (dateString: string) => {
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

  const handleOpenTokenModal = (exam: any) => {
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
    } catch (e: any) {
      const msg = e.response?.data?.message || 'Kode token yang Anda masukkan tidak valid.';
      bukaAlertKustom("Verifikasi Gagal", msg, "info");
    } finally {
      setVerifying(false);
    }
  };

  return {
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
    handleVerifyToken,
  };
}