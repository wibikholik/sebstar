<div id="modalMajor" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 5% auto; padding: 0; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); overflow: hidden; animation: slideIn 0.3s ease-out;">
        
        <div style="background-color: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;">Tambah Jurusan Baru</h3>
            <span onclick="closeMajorModal()" style="cursor: pointer; font-size: 24px; line-height: 1;">&times;</span>
        </div>

        <div style="padding: 20px;">
            <form action="{{ route('admin.majors.store') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Nama Lengkap Jurusan:</label>
                    <input type="text" name="nama_jurusan" required placeholder="Contoh: Rekayasa Perangkat Lunak" 
                           style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    <small style="color: #666; font-size: 11px;">*Gunakan nama lengkap tanpa singkatan</small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Singkatan:</label>
                    <input type="text" name="singkatan" required placeholder="Contoh: RPL" 
                           style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; text-transform: uppercase;">
                </div>

                <button type="submit" style="background-color: #2c3e50; color: white; padding: 12px; border: none; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px; transition: background 0.3s;">
                    Simpan Jurusan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function closeMajorModal() {
        document.getElementById('modalMajor').style.display = 'none';
    }

    // Menutup modal jika area di luar box diklik
    window.onclick = function(event) {
        let modalMajor = document.getElementById('modalMajor');
        let modalClass = document.getElementById('modalClass'); // Asumsi ID modal kelas
        if (event.target == modalMajor) {
            closeMajorModal();
        }
        if (event.target == modalClass) {
            closeClassModal(); // Fungsi penutup modal kelas kamu
        }
    }
</script>

<style>
    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    #modalMajor button:hover {
        background-color: #1a252f !important;
    }
</style>