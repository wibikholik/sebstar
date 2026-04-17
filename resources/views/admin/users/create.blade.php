<div id="userModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 0; border-radius: 12px; width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
        
        <div style="background: #cd0000; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;">Tambah Pengguna Baru</h3>
            <span onclick="closeModal()" style="cursor: pointer; font-size: 24px; line-height: 1;">&times;</span>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" style="padding: 20px;">
            @csrf
            
            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap pengguna" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Pilih Role</label>
                <select name="role" id="role_select" required onchange="toggleCreateFields(this.value)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer;">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="pengawas" {{ old('role') == 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                </select>
            </div>

            <div id="createSiswaFields" style="display: none; background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px dashed #cbd5e1;">
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 12px; font-weight: 600;">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" value="{{ old('nis') }}" placeholder="Masukkan NIS" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600;">Pilih Kelas</label>
                    <select name="classroom_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background: white;">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="createGuruFields" style="display: none; background: #f0fdf4; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px dashed #bbf7d0;">
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 12px; font-weight: 600;">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Masukkan NIP" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600;">Mata Pelajaran Diampu</label>
                    <select name="subject_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; background: white;">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #cd0000; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; transition: 0.3s;">
                Simpan Pengguna
            </button>
        </form>
    </div>
</div>

<script>
    // Fungsi untuk menampilkan field berdasarkan role yang dipilih
    function toggleCreateFields(role) {
        const siswaDiv = document.getElementById('createSiswaFields');
        const guruDiv = document.getElementById('createGuruFields');
        
        // Reset display
        siswaDiv.style.display = 'none';
        guruDiv.style.display = 'none';

        if (role === 'siswa') {
            siswaDiv.style.display = 'block';
        } else if (role === 'guru') {
            guruDiv.style.display = 'block';
        }
    }

    // Jalankan fungsi saat halaman dimuat (untuk menangani old input setelah error validasi)
    document.addEventListener('DOMContentLoaded', function() {
        const currentRole = document.getElementById('role_select').value;
        if(currentRole) toggleCreateFields(currentRole);
    });
</script>