<div id="editScheduleModal" class="modal" style="display: {{ $errors->any() && session('error_form_type') === 'edit' ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(30, 30, 47, 0.4); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 0; border-radius: 16px; width: 620px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.05);">
        
        {{-- Header Modal Bertema Gradasi Merah Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-edit"></i> Edit Jadwal Pelaksanaan Ujian
            </h3>
            <span onclick="closeEditModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">&times;</span>
        </div>

        <form id="editScheduleForm" method="POST" style="padding: 24px;">
            @csrf
            @method('PUT') 
            
            {{-- Input Jenis Ujian --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Jenis / Tipe Ujian</label>
                <select id="edit_exam_type_id" name="exam_type_id" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('exam_type_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                    <option value="">-- Pilih Jenis Ujian --</option>
                    @foreach($examTypes as $et)
                        <option value="{{ $et->id }}">{{ $et->name }}</option>
                    @endforeach
                </select>
                @error('exam_type_id')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Grid Row 1: Mata Pelajaran & Kelas Peserta --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Mata Pelajaran</label>
                    <select id="edit_subject_id" name="subject_id" onchange="loadTeachers(this.value, 'edit_teacher_ids')" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('subject_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->nama_mapel }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Target Kelas Peserta</label>
                    <select id="edit_classroom_id" name="classroom_id" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('classroom_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                        @foreach($classes as $cl)
                            <option value="{{ $cl->id }}">{{ $cl->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Grid Row 2: Guru Pengampu & Pengawas Ruangan --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Guru Pengampu <span style="color: #6a6a7a; font-weight: 500; font-size: 11px;">(Ctrl/Cmd utk banyak)</span></label>
                    <select id="edit_teacher_ids" name="teacher_ids[]" multiple required style="width: 100%; height: 85px; padding: 10px 14px; border-radius: 10px; border: 1px solid {{ $errors->has('teacher_ids') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;">
                        <option value="" disabled>Pilih Mapel Dulu</option>
                    </select>
                    @error('teacher_ids')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Plotting Pengawas Ruangan</label>
                    <select id="edit_proctor_id" name="proctor_id" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('proctor_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                        <option value="">-- Pilih Pengawas --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ strtoupper($t->role) }})</option>
                        @endforeach
                    </select>
                    @error('proctor_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Grid Row 3: Tanggal Pelaksanaan & Durasi Ujian --}}
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 18px; margin-bottom: 25px;">
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Tanggal Pelaksanaan</label>
                    <input type="date" id="edit_tanggal_ujian" name="tanggal_ujian" value="{{ old('tanggal_ujian') }}" required style="width: 100%; padding: 10px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('tanggal_ujian') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('tanggal_ujian')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Durasi (Menit)</label>
                    <input type="number" id="edit_durasi" name="durasi" value="{{ old('durasi') }}" required min="1" style="width: 100%; padding: 10px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('durasi') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 700; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('durasi')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons (Batal & Simpan Perubahan) --}}
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeEditModal()" style="flex: 1; background: #f1f5f9; color: #475569; padding: 12px; border: none; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                <button type="submit" style="flex: 2; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 12px; border: none; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-check-circle"></i> Simpan Perubahan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeEditModal() { 
        document.getElementById('editScheduleModal').style.display = 'none'; 
    }

    // Melakukan pemulihan data guru pengampu secara otomatis pasca-reload apabila validasi edit error
    document.addEventListener('DOMContentLoaded', function() {
        const hasError = "{{ session('error_form_type') === 'edit' }}";
        if (hasError) {
            const currentSubjectId = document.getElementById('edit_subject_id').value;
            const oldTeachers = @json(old('teacher_ids', []));
            if (currentSubjectId) {
                loadTeachers(currentSubjectId, 'edit_teacher_ids', oldTeachers);
            }
        }
    });
</script>