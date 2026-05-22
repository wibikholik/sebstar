<div id="modalMajorEdit" style="display: {{ $errors->any() && session('error_form_type') === 'edit' ? 'block' : 'none' }}; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(30, 30, 47, 0.4); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 8% auto; padding: 0; border-radius: 16px; width: 440px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
        
        {{-- Header Modal Gradasi Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit Data Jurusan
            </h3>
            <span onclick="closeMajorEditModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">&times;</span>
        </div>

        <div style="padding: 24px;">
            <form id="editMajorForm" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Input Edit Nama Jurusan --}}
                <div style="margin-bottom: 18px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Nama Jurusan:</label>
                    <input type="text" name="nama_jurusan" id="edit_major_name" value="{{ old('nama_jurusan') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak" 
                           style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('nama_jurusan') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @error('nama_jurusan')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Input Edit Singkatan Jurusan --}}
                <div style="margin-bottom: 24px;">
                    <label style="font-weight: 700; font-size: 13px; color: #1e1e2f; display: block; margin-bottom: 6px;">Singkatan:</label>
                    <input type="text" name="singkatan" id="edit_major_short" value="{{ old('singkatan') }}" required placeholder="Contoh: RPL" 
                           style="width: 100%; padding: 11px 16px; box-sizing: border-box; border: 1px solid {{ $errors->has('singkatan') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1e1e2f; outline: none; text-transform: uppercase; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @error('singkatan')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Action Buttons (Batal & Update) --}}
                <div style="display: flex; gap: 12px;">
                    <button type="button" onclick="closeMajorEditModal()" style="flex: 1; background: #f1f5f9; color: #475569; border: none; padding: 12px; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                    <button type="submit" style="flex: 2; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; border: none; padding: 12px; border-radius: 30px; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas fa-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function closeMajorEditModal() {
        document.getElementById('modalMajorEdit').style.display = 'none';
    }

    /**
     * Fungsi Pemicu Modal Edit (Dipasang pada onclick tombol pensil di tabel data jurusan)
     */
    function openMajorEditModal(major) {
        const modal = document.getElementById('modalMajorEdit');
        const form = document.getElementById('editMajorForm');
        
        // Atur URL action route update secara dinamis sesuai ID jurusan
        form.action = "{{ url('admin/majors') }}/" + major.id;
        
        // Membaca ketikan lama (old input) jika validasi reject, atau pasang nilai asli database
        document.getElementById('edit_major_name').value = "{{ old('nama_jurusan') }}" || major.nama_jurusan;
        document.getElementById('edit_major_short').value = "{{ old('singkatan') }}" || major.singkatan;
        
        modal.style.display = 'block';
    }
</script>