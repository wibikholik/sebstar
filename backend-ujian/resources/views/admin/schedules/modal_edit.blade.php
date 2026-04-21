<div id="editScheduleModal" class="modal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 3% auto; padding: 20px; border-radius: 12px; width: 450px;">
        <h3 style="margin-top: 0;">Edit Jadwal Ujian</h3>
        <form id="editScheduleForm" method="POST">
            @csrf @method('PUT')
            
            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Mata Pelajaran</label>
                <select name="subject_id" id="edit_subject_id" onchange="loadTeachers(this.value, 'edit_teacher_ids')" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Pilih Guru (Multi-select)</label>
                <select name="teacher_ids[]" id="edit_teacher_ids" multiple required style="width: 100%; height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Kelas</label>
                <select name="classroom_id" id="edit_classroom_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    @foreach($classes as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Tanggal</label>
                <input type="date" name="tanggal_ujian" id="edit_tanggal_ujian" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="time" name="jam_mulai" id="edit_jam_mulai" required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="time" name="jam_selesai" id="edit_jam_selesai" required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="number" name="durasi" id="edit_durasi" required style="width: 70px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <button type="submit" style="width: 100%; background: #2563eb; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Perubahan</button>
            <button type="button" onclick="closeEditModal()" style="width: 100%; background: #eee; padding: 10px; border: none; border-radius: 8px; margin-top: 10px; cursor: pointer;">Batal</button>
        </form>
    </div>
</div>