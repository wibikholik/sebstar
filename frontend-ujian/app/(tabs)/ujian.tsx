import React, { useEffect, useState } from 'react';
import { StyleSheet, Text, View, ActivityIndicator, FlatList, SafeAreaView } from 'react-native';
import axios from 'axios';

export default function UjianScreen() {
  const [soal, setSoal] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchSoal = async () => {
      try {
        setLoading(true);
        setError(null);

        // Pastikan IP dan Route sesuai dengan Laravel kamu
        const response = await axios.get('http://10.253.108.247:8000/api/ambil-soal/2');
        
        // LOG INI PENTING: Lihat di terminal VS Code!
        console.log("Data diterima dari Laravel:", response.data);

        // Jika data dibungkus dalam objek {data: [...]}, ambil array-nya saja
        const dataArray = response.data.data || response.data;
        
        if (Array.isArray(dataArray)) {
          setSoal(dataArray);
        } else {
          setError("Data yang diterima bukan berupa daftar (array). Periksa respons API.");
        }
      } catch (err: any) {
        if (err.response) {
          setError(`Server error (Status ${err.response.status}): Pastikan route api.php benar.`);
        } else {
          setError("Gagal terhubung ke server: " + err.message);
        }
      } finally {
        setLoading(false);
      }
    };

    fetchSoal();
  }, []);

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color="#0000ff" />
        <Text style={{ marginTop: 10 }}>Loading soal...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.center}>
        <Text style={{ color: 'red', textAlign: 'center', padding: 20 }}>{error}</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <Text style={styles.header}>Daftar Soal Ujian</Text>
      
      {soal.length === 0 ? (
        <Text style={{ textAlign: 'center', marginTop: 20 }}>Tidak ada data soal ditemukan.</Text>
      ) : (
        <FlatList
          data={soal}
          keyExtractor={(item: any) => item.id ? item.id.toString() : Math.random().toString()}
          renderItem={({ item, index }) => (
            <View style={styles.card}>
              <Text style={styles.questionText}>
                {index + 1}. {item.question_text || item.pertanyaan || "Data tidak terbaca"}
              </Text>
            </View>
          )}
        />
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 15 },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { fontSize: 20, fontWeight: 'bold', marginBottom: 15, textAlign: 'center' },
  card: { backgroundColor: '#fff', padding: 15, marginBottom: 10, borderRadius: 8, elevation: 3 },
  questionText: { fontSize: 16, fontWeight: '500' }
});