<div id="modalCreate" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }}; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(30, 30, 47, 0.4); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 8% auto; padding: 0; border-radius: 16px; width: 420px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
        
        {{-- Header Modal Gradasi Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-book-medical"></i> Tambah Mata Pelajaran
            </h3>
            <span onclick="closeModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='0.9'">&times;</span>
        </div>

        <div style="padding: 24px;">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                
                {{-- Input Kode Mata Pelajaran --}}
                <div style="margin-bottom: 18px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Kode Mapel:</label>
                    <input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}" required placeholder="Contoh: MAT-01" style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('kode_mapel') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @error('kode_mapel')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Input Nama Mata Pelajaran --}}
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Nama Mata Pelajaran:</label>
                    <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" required placeholder="Contoh: Matematika Wajib" style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('nama_mapel') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @error('nama_mapel')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Tombol Submit Gradasi SEBSTAR --}}
                <button type="submit" style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 13px; border: none; width: 100%; border-radius: 30px; cursor: pointer; font-weight: 700; font-size: 14px; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-save"></i> Simpan Mata Pelajaran
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('modalCreate').style.display = 'none';
    }
</script>