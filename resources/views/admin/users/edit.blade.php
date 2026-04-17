<div id="editUserModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 0; border-radius: 12px; width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;">Edit Data Pengguna</h3>
            <span onclick="closeEditModal()" style="cursor: pointer; font-size: 24px;">&times;</span>
        </div>

        <form id="editForm" method="POST" style="padding: 20px;">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Email</label>
                <input type="email" name="email" id="edit_email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Ganti Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Role</label>
                <select name="role" id="edit_role" required onchange="toggleEditFields(this.value)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                    <option value="pengawas">Pengawas</option>
                </select>
            </div>

            <div id="editSiswaFields" style="display: none; background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 12px;">
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 13px; font-weight: 600;">NIS</label>
                    <input type="text" name="nis" id="edit_nis" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600;">Kelas</label>
                    <select name="classroom_id" id="edit_classroom_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="editGuruFields" style="display: none; background: #f0fdf4; padding: 15px; border-radius: 8px; margin-bottom: 12px;">
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 13px; font-weight: 600;">NIP</label>
                    <input type="text" name="nip" id="edit_nip" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600;">Mata Pelajaran</label>
                    <select name="subject_id" id="edit_subject_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #2c3e50; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    function toggleEditFields(role) {
        document.getElementById('editSiswaFields').style.display = (role === 'siswa') ? 'block' : 'none';
        document.getElementById('editGuruFields').style.display = (role === 'guru') ? 'block' : 'none';
    }
</script>