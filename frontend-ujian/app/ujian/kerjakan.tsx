import React from 'react';
import { 
  View, 
  Text, 
  TouchableOpacity, 
  StyleSheet, 
  ActivityIndicator, 
  TextInput, 
  Image, 
  ScrollView, 
  Modal, 
  StatusBar, 
  SafeAreaView, 
  Dimensions, 
  Animated, 
  Platform 
} from 'react-native';
import { useLocalSearchParams, useRouter, useNavigation } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { useKerjakanLogic } from '../../hooks/useKerjakanLogic'; // Sesuaikan path folder hooks Anda

const { width } = Dimensions.get('window');
const primaryRed = '#c91313';

export default function KerjakanScreen() {
  const { id, token } = useLocalSearchParams();
  const router = useRouter();
  const navigation = useNavigation();

  // Memanggil semua logic dari Custom Hook
  const {
    soal,
    loading,
    refreshing,
    selectedAnswers,
    modalVisible,
    setModalVisible,
    currentIndex,
    setCurrentIndex,
    timeLeft,
    sideNavVisible,
    slideAnim,
    ASSET_URL,
    fetchData,
    toggleSideNav,
    handleAnswerChange,
    confirmFinish,
    checkApakahSemuaSudahDikerjakan
  } = useKerjakanLogic(id, token, router, navigation);

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={primaryRed} />
      </View>
    );
  }

  const currentItem = soal[currentIndex];
  const isNomorTerakhir = currentIndex === soal.length - 1;
  const semuaSoalTerisi = checkApakahSemuaSudahDikerjakan();

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar hidden={true} />
      
      {/* HEADER BAR */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => toggleSideNav(true)} style={styles.menuIcon}>
           <Ionicons name="grid" size={24} color="#fff" />
        </TouchableOpacity>
        
        <View style={styles.schoolHeader}>
          <Image source={require('../../assets/images/logosekolah.png')} style={styles.logoSekolah} />
          <View>
            <Text style={styles.schoolName}>SMKN 1 BINONG</Text>
            <Text style={styles.appTagline}>Sebstar Exam Browser</Text>
          </View>
        </View>

        {/* REFRESH MANUAL COMPONENT */}
        <TouchableOpacity onPress={fetchData} style={{ marginRight: 10 }} disabled={refreshing}>
          {refreshing ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <Ionicons name="refresh-circle" size={28} color="#fff" />
          )}
        </TouchableOpacity>

        <View style={styles.timerContainer}>
          <Text style={styles.timerText}>
            {Math.floor(timeLeft / 60)}:{String(timeLeft % 60).padStart(2, '0')}
          </Text>
        </View>
      </View>

      {/* LEMBAR AREA SOAL */}
      <ScrollView contentContainerStyle={styles.scrollContainer} showsVerticalScrollIndicator={false}>
        <View style={styles.card}>
          <Text style={styles.progressLabel}>Pertanyaan No. {currentIndex + 1}</Text>
          
          {currentItem?.question_image && (
            <Image 
              source={{ uri: `${ASSET_URL}/storage/${currentItem.question_image}` }} 
              style={styles.image} 
              resizeMode="contain" 
            />
          )}

          <Text style={styles.questionText} selectable={false}>
            {currentItem?.question_text}
          </Text>
          
          {currentItem?.type?.toLowerCase() === 'pg' ? (
            <View style={styles.optionsWrapper}>
              {['a', 'b', 'c', 'd', 'e'].map((opt) => currentItem[`option_${opt}`] ? (
                <TouchableOpacity 
                  key={opt} 
                  style={[styles.optionBtn, selectedAnswers[currentItem.id] === opt && styles.selectedBtn]} 
                  onPress={() => handleAnswerChange(currentItem.id, opt)}
                >
                  <View style={[styles.optCircle, selectedAnswers[currentItem.id] === opt && styles.selectedCircle]}>
                    <Text style={[styles.optLetter, selectedAnswers[currentItem.id] === opt && {color: '#fff'}]}>
                      {opt.toUpperCase()}
                    </Text>
                  </View>
                  <Text style={[styles.optionText, selectedAnswers[currentItem.id] === opt && styles.selectedText]}>
                    {currentItem[`option_${opt}`]}
                  </Text>
                </TouchableOpacity>
              ) : null)}
            </View>
          ) : (
            <TextInput 
              style={styles.essayInput} 
              multiline 
              placeholder="Tulis jawaban essay disini..."
              value={selectedAnswers[currentItem.id] || ''} 
              onChangeText={(text) => handleAnswerChange(currentItem.id, text)} 
            />
          )}
        </View>
      </ScrollView>

      {/* FOOTER NAVIGATION */}
      <View style={styles.footer}>
        <TouchableOpacity 
          style={[styles.btnNav, currentIndex === 0 && {opacity: 0.3}]} 
          disabled={currentIndex === 0} 
          onPress={() => setCurrentIndex(prev => prev - 1)}
        >
          <Ionicons name="chevron-back" size={20} color="#475569" />
          <Text style={styles.btnNavText}>Kembali</Text>
        </TouchableOpacity>
        
        {isNomorTerakhir && semuaSoalTerisi ? (
          <TouchableOpacity style={[styles.btnFinish, {backgroundColor: primaryRed}]} onPress={() => setModalVisible(true)}>
            <Text style={styles.btnFinishText}>SELESAI</Text>
          </TouchableOpacity>
        ) : (
          <View style={{ flex: 1 }} />
        )}
        
        <TouchableOpacity 
          style={[styles.btnNav, isNomorTerakhir && {opacity: 0.3}]} 
          disabled={isNomorTerakhir} 
          onPress={() => setCurrentIndex(prev => prev + 1)}
        >
          <Text style={styles.btnNavText}>Lanjut</Text>
          <Ionicons name="chevron-forward" size={20} color="#475569" />
        </TouchableOpacity>
      </View>

      {/* DRAWER MATRIX */}
      {sideNavVisible && <TouchableOpacity style={styles.drawerOverlay} activeOpacity={1} onPress={() => toggleSideNav(false)} />}
      <Animated.View style={[styles.drawer, { transform: [{ translateX: slideAnim }] }]}>
        <View style={styles.drawerHeader}><Text style={styles.drawerTitle}>Daftar Navigasi Soal</Text></View>
        <ScrollView contentContainerStyle={styles.drawerGrid}>
          {soal.map((item, index) => {
            const isAnswered = selectedAnswers[item.id] && selectedAnswers[item.id].toString().trim() !== '';
            return (
              <TouchableOpacity 
                key={index} 
                style={[styles.gridItem, isAnswered ? {backgroundColor: primaryRed} : {backgroundColor: '#f1f5f9'}, currentIndex === index && {borderWidth: 2, borderColor: '#1e293b'}]} 
                onPress={() => { setCurrentIndex(index); toggleSideNav(false); }}
              >
                <Text style={[styles.gridText, isAnswered && {color: '#fff'}]}>{index + 1}</Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </Animated.View>

      {/* POPUP MODAL FINISH */}
      <Modal visible={modalVisible} transparent={true} animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Akhiri Sesi Ujian?</Text>
            <View style={styles.modalBtnRow}>
              <TouchableOpacity style={styles.modalCancel} onPress={() => setModalVisible(false)}>
                <Text style={{fontWeight: '700', color: '#64748b'}}>Batal</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalConfirm, {backgroundColor: primaryRed}]} onPress={() => confirmFinish(false)}>
                <Text style={{color: '#fff', fontWeight: 'bold'}}>Ya, Kumpulkan</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { backgroundColor: '#c91313', height: 75, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 15, paddingTop: Platform.OS === 'android' ? 10 : 0 },
  menuIcon: { width: 35 },
  schoolHeader: { flexDirection: 'row', alignItems: 'center', gap: 10, flex: 1 },
  logoSekolah: { width: 35, height: 35, borderRadius: 5, backgroundColor: '#fff' },
  schoolName: { color: '#fff', fontSize: 14, fontWeight: '800' },
  appTagline: { color: '#fecaca', fontSize: 10 },
  timerContainer: { backgroundColor: 'rgba(0,0,0,0.2)', paddingHorizontal: 10, paddingVertical: 5, borderRadius: 8 },
  timerText: { color: '#fff', fontSize: 16, fontWeight: '900' },
  scrollContainer: { padding: 20, paddingBottom: 100 },
  card: { backgroundColor: '#fff', padding: 20, borderRadius: 20, elevation: 3, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 3 },
  progressLabel: { color: '#64748b', fontSize: 12, fontWeight: '800', marginBottom: 15 },
  image: { width: '100%', height: 200, marginBottom: 20, borderRadius: 10 },
  questionText: { fontSize: 16, fontWeight: '700', lineHeight: 24, color: '#1e293b', marginBottom: 20 },
  optionsWrapper: { gap: 10 },
  optionBtn: { flexDirection: 'row', alignItems: 'center', padding: 14, borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 15, gap: 12 },
  selectedBtn: { backgroundColor: '#fff1f2', borderColor: '#c91313' },
  optCircle: { width: 30, height: 30, borderRadius: 15, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center' },
  selectedCircle: { backgroundColor: '#c91313' },
  optLetter: { fontSize: 12, fontWeight: '800', color: '#475569' },
  optionText: { flex: 1, fontSize: 14, color: '#334155' },
  selectedText: { color: '#c91313', fontWeight: '700' },
  essayInput: { backgroundColor: '#f8fafc', borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 15, padding: 15, height: 160, textAlignVertical: 'top', fontSize: 14 },
  footer: { position: 'absolute', bottom: 0, width: '100%', height: 80, backgroundColor: '#fff', borderTopWidth: 1, borderColor: '#f1f5f9', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: 20 },
  btnNav: { flexDirection: 'row', alignItems: 'center', gap: 5, padding: 10 },
  btnNavText: { fontWeight: '700', fontSize: 13, color: '#475569' },
  btnFinish: { paddingHorizontal: 25, paddingVertical: 12, borderRadius: 12, minWidth: 100, alignItems: 'center' },
  btnFinishText: { color: '#fff', fontWeight: '800', fontSize: 13 },
  drawerOverlay: { position: 'absolute', width: '100%', height: '100%', backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 1000 },
  drawer: { position: 'absolute', width: width * 0.75, height: '100%', backgroundColor: '#fff', zIndex: 1001, padding: 25, paddingTop: 50 },
  drawerHeader: { marginBottom: 30 },
  drawerTitle: { fontSize: 20, fontWeight: '800', color: '#1e293b' },
  drawerGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12 },
  gridItem: { width: 45, height: 45, borderRadius: 10, justifyContent: 'center', alignItems: 'center' },
  gridText: { fontWeight: '800', color: '#475569' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center' },
  modalContent: { width: '80%', backgroundColor: '#fff', padding: 25, borderRadius: 20, alignItems: 'center', elevation: 10 },
  modalTitle: { fontSize: 18, fontWeight: '800', color: '#1e293b', marginBottom: 20 },
  modalBtnRow: { flexDirection: 'row', gap: 10 },
  modalCancel: { flex: 1, padding: 12, alignItems: 'center', backgroundColor: '#f1f5f9', borderRadius: 10 },
  modalConfirm: { flex: 1, padding: 12, alignItems: 'center', borderRadius: 10 }
});