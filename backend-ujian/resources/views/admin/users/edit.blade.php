<div id="editUserModal" class="modal" style="display: {{ $errors->any() && session('error_form_type') === 'edit' ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(30, 30, 47, 0.4); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 4% auto; padding: 0; border-radius: 16px; width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
        
        {{-- Header Modal Bertema Gradasi Merah Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-edit"></i> Edit Data Pengguna
            </h3>
            <span onclick="closeEditModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">&times;</span>
        </div>

        <form id="editForm" method="POST" style="padding: 24px;">
            @csrf
            @method('PUT')
            
            {{-- Input Nama Lengkap --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" value="{{ old('name') }}" required placeholder="Nama lengkap pengguna" style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('name') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; box-sizing: border-box; font-size: 13px; font-weight: 600; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                @error('name')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Input Alamat Email --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Alamat Email</label>
                <input type="email" name="email" id="edit_email" value="{{ old('email') }}" required placeholder="email@contoh.com" style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('email') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; box-sizing: border-box; font-size: 13px; font-weight: 600; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                @error('email')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Input Password Baru (Validasi Panjang Karakter Minimal 6 di Update) --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Ganti Password <span style="font-weight: 500; color: #a0a0b0;">(Kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" placeholder="Minimal berisi 6 karakter" style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('password') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; box-sizing: border-box; font-size: 13px; font-weight: 600; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                @error('password')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Input Pilihan Role --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Pilih Otoritas / Role</label>
                <select name="role" id="edit_role" required onchange="toggleEditFields(this.value)" style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('role') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; background: white; cursor: pointer; font-size: 13px; font-weight: 600; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                    <option value="pengawas">Pengawas</option>
                </select>
                @error('role')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Sub Form Edit Khusus Siswa --}}
            <div id="editSiswaFields" style="display: none; background: #fafafa; padding: 16px; border-radius: 12px; margin-bottom: 18px; border: 1px dashed {{ $errors->has('nis') || $errors->has('classroom_id') ? '#cd0000' : '#edf0f5' }};">
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" id="edit_nis" value="{{ old('nis') }}" placeholder="Masukkan nomor induk siswa" style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 13px; font-weight: 600; outline: none;">
                    @error('nis')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Penempatan Kelas</label>
                    <select name="classroom_id" id="edit_classroom_id" style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: white; font-size: 13px; font-weight: 600; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Sub Form Edit Khusus Guru --}}
            <div id="editGuruFields" style="display: none; background: rgba(46, 204, 113, 0.02); padding: 16px; border-radius: 12px; margin-bottom: 18px; border: 1px dashed {{ $errors->has('nip') || $errors->has('subject_id') ? '#cd0000' : 'rgba(46, 204, 113, 0.2)' }};">
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" id="edit_nip" value="{{ old('nip') }}" placeholder="Masukkan nomor induk pegawai" style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 13px; font-weight: 600; outline: none;">
                    @error('nip')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Mata Pelajaran Utama Diampu</label>
                    <select name="subject_id" id="edit_subject_id" style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background: white; font-size: 13px; font-weight: 600; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_mapel }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Tombol Perubahan Gradasi Premium SEBSTAR --}}
            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 13px; border: none; border-radius: 30px; font-weight: 700; cursor: pointer; font-size: 14px; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-check-circle"></i> Simpan Perubahan Data
            </button>
        </form>
    </div>
</div>

<script>
    // Fungsi sinkronisasi tampilan form edit berdasarkan role aktif
    function toggleEditFields(role) {
        document.getElementById('editSiswaFields').style.display = (role === 'siswa') ? 'block' : 'none';
        document.getElementById('editGuruFields').style.display = (role === 'guru') ? 'block' : 'none';
    }

    function closeEditModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    // Fungsi Utama: Dipasang pada event onclick tombol EDIT pensil di baris data tabel users utama
    function openEditModal(user) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editForm');
        
        // Pasang rute action update sesuai ID target data user secara dinamis
        form.action = "{{ url('admin/users') }}/" + user.id;
        
        // Membaca input lama (old) jika validasi error, atau isi orisinal data row table
        document.getElementById('edit_name').value = "{{ old('name') }}" || user.name;
        document.getElementById('edit_email').value = "{{ old('email') }}" || user.email;
        document.getElementById('edit_role').value = "{{ old('role') }}" || user.role;
        document.getElementById('edit_nis').value = "{{ old('nis') }}" || (user.nis || '');
        document.getElementById('edit_nip').value = "{{ old('nip') }}" || (user.nip || '');
        document.getElementById('edit_classroom_id').value = "{{ old('classroom_id') }}" || (user.classroom_id || '');
        document.getElementById('edit_subject_id').value = "{{ old('subject_id') }}" || (user.subject_id || '');
        
        // Jalankan trigger agar sub-form terbuka proporsional mengikuti data rolenya
        toggleEditFields(document.getElementById('edit_role').value);
        modal.style.display = 'block';
    }

    // Melakukan pengecekan ulang jika halaman ter-reload akibat error dari controller
    document.addEventListener('DOMContentLoaded', function() {
        const currentEditRole = document.getElementById('edit_role').value;
        if(currentEditRole) toggleEditFields(currentEditRole);
    });
</script>