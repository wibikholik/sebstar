<div id="scheduleModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 3% auto; padding: 20px; border-radius: 12px; width: 450px;">
        <h3 style="margin-top: 0;">Tambah Jadwal Ujian</h3>
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Mata Pelajaran</label>
                <select name="subject_id" onchange="loadTeachers(this.value, 'teacher_ids')" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Pilih Guru (Multi-select)</label>
                <select name="teacher_ids[]" id="teacher_ids" multiple required style="width: 100%; height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    <option value="">Pilih Mapel Dulu</option>
                </select>
                <small style="color: #666; font-size: 10px;">*Tahan CTRL untuk pilih banyak</small>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Kelas</label>
                <select name="classroom_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    @foreach($classes as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 600;">Tanggal</label>
                <input type="date" name="tanggal_ujian" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="time" name="jam_mulai" required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="time" name="jam_selesai" required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="number" name="durasi" placeholder="Min" required style="width: 70px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <button type="submit" style="width: 100%; background: #cd0000; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Jadwal</button>
            <button type="button" onclick="closeScheduleModal()" style="width: 100%; background: #eee; padding: 10px; border: none; border-radius: 8px; margin-top: 10px; cursor: pointer;">Batal</button>
        </form>
    </div>
</div>