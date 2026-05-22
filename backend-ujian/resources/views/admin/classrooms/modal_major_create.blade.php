<div id="modalMajor" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(30, 30, 47, 0.4); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 6% auto; padding: 0; border-radius: 16px; width: 440px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
        
        {{-- Header Modal Gradasi Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-graduation-cap"></i> Tambah Jurusan Baru
            </h3>
            <span onclick="closeMajorModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='0.9'">&times;</span>
        </div>

        <div style="padding: 24px;">
            <form action="{{ route('admin.majors.store') }}" method="POST">
                @csrf
                
                {{-- Input Nama Jurusan --}}
                <div style="margin-bottom: 18px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Nama Lengkap Jurusan:</label>
                    <input type="text" name="nama_jurusan" value="{{ old('nama_jurusan') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak" 
                           style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('nama_jurusan') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    <small style="color: #6a6a7a; font-size: 11px; display: block; margin-top: 4px; font-weight: 500;">*Gunakan nama lengkap tanpa singkatan</small>
                    @error('nama_jurusan')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Input Singkatan Jurusan --}}
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Singkatan / Akronim Jurusan:</label>
                    <input type="text" name="singkatan" value="{{ old('singkatan') }}" required placeholder="Contoh: RPL" 
                           style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('singkatan') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1e1e2f; outline: none; text-transform: uppercase; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @error('singkatan')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Tombol Submit Gradasi SEBSTAR --}}
                <button type="submit" style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 13px; border: none; width: 100%; border-radius: 30px; cursor: pointer; font-weight: 700; font-size: 14px; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-save"></i> Simpan Jurusan Baru
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openMajorModal() {
        document.getElementById('modalMajor').style.display = 'block';
    }

    function closeMajorModal() {
        document.getElementById('modalMajor').style.display = 'none';
    }

    // Menutup modal jika area abu-abu di luar box diklik
    window.onclick = function(event) {
        let modalMajor = document.getElementById('modalMajor');
        let modalEdit = document.getElementById('modalEdit'); // Id modal edit kamu jika ada di halaman yang sama
        if (event.target == modalMajor) {
            closeMajorModal();
        }
        if (event.target == modalEdit) {
            closeEditModal(); 
        }
    }
</script>

<style>
    @keyframes slideIn {
        from { transform: translateY(-25px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>