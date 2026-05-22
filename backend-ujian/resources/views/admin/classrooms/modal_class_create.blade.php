<div id="modalCreate" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }}; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(30, 30, 47, 0.4); backdrop-filter: blur(3px); transition: 0.3s;">
    <div style="background-color: white; margin: 8% auto; padding: 0; border-radius: 16px; width: 440px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); animation: slideInClass 0.3s ease-out;">
        
        {{-- Header Modal Gradasi Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-door-open"></i> Tambah Rombel Kelas Baru
            </h3>
            <span onclick="closeModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='0.9'">&times;</span>
        </div>

        <div style="padding: 24px;">
            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                @csrf
                
                {{-- Input Nama Kelas --}}
                <div style="margin-bottom: 18px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Nama / Tingkatan Kelas:</label>
                    <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" required placeholder="Contoh: XII TRPL 1" style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('nama_kelas') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @error('nama_kelas')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Input Pilihan Jurusan/Major --}}
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Kompetensi Keahlian / Jurusan:</label>
                    <select name="major_id" required style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('major_id') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; background: white; outline: none; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                        <option value="">-- Pilih Jurusan Pengikat --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}" {{ old('major_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_jurusan }} ({{ $m->singkatan }})</option>
                        @endforeach
                    </select>
                    @error('major_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 12px;">
                    <button type="button" onclick="closeModal()" style="flex: 1; background: #f1f5f9; color: #475569; border: none; padding: 12px; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                    <button type="submit" style="flex: 2; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; border: none; padding: 12px; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas fa-save"></i> Simpan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT PENGGERAK MODAL BARU: DIJAMIN BERHASIL DIKLIK --}}
<script>
    // Pastikan tombol utama "Tambah Kelas" di file index.blade.php memanggil fungsi openModal() ini pada tag onclick-nya
    // Contoh: <button onclick="openModal()">Tambah Kelas</button>
    function openModal() {
        const modal = document.getElementById('modalCreate');
        if (modal) {
            modal.style.display = 'block';
        }
    }

    function closeModal() {
        const modal = document.getElementById('modalCreate');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Pengaman: Menutup modal otomatis jika admin mengklik area transparan di luar kotak putih form
    window.addEventListener('click', function(event) {
        const modalCreate = document.getElementById('modalCreate');
        if (event.target === modalCreate) {
            closeModal();
        }
    });
</script>

<style>
    @keyframes slideInClass {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>