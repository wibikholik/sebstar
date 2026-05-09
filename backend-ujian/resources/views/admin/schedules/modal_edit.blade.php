<div id="editScheduleModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 30px; border-radius: 12px; width: 600px; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; font-size: 20px;">✏️ Edit Jadwal Ujian</h3>
        
        <form id="editScheduleForm" method="POST">
            @csrf
            @method('PUT') 
            
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Jenis Ujian</label>
                <select id="edit_exam_type_id" name="exam_type_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                    <option value="">-- Pilih Jenis Ujian --</option>
                    @foreach($examTypes as $et)
                        <option value="{{ $et->id }}">{{ $et->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Mata Pelajaran</label>
                    <select id="edit_subject_id" name="subject_id" onchange="loadTeachers(this.value, 'edit_teacher_ids')" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Kelas</label>
                    <select id="edit_classroom_id" name="classroom_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                        @foreach($classes as $cl)
                            <option value="{{ $cl->id }}">{{ $cl->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Guru Pengampu <span style="color: #cd0000; font-weight: 400; font-size: 11px;">(Ctrl/Cmd utk banyak)</span></label>
                    <select id="edit_teacher_ids" name="teacher_ids[]" multiple required style="width: 100%; height: 80px; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                        <option value="" disabled>Pilih Mapel Dulu</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Pengawas Ruangan</label>
                    <select id="edit_proctor_id" name="proctor_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                        <option value="">-- Pilih Pengawas --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ strtoupper($t->role) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Tanggal Pelaksanaan</label>
                    <input type="date" id="edit_tanggal_ujian" name="tanggal_ujian" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;">Durasi (Menit)</label>
                    <input type="number" id="edit_durasi" name="durasi" required min="1" style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #1e293b; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeEditModal()" style="flex: 1; background: #f1f5f9; color: #475569; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="flex: 2; background: #1e293b; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>