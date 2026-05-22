<div id="scheduleModal" class="modal" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(30, 30, 47, 0.4); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 0; border-radius: 16px; width: 620px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.05);">
        
        {{-- Header Modal Gradasi Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-plus"></i> Buat Jadwal Pelaksanaan Ujian Baru
            </h3>
            <span onclick="closeScheduleModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">&times;</span>
        </div>

        <form action="{{ route('admin.schedules.store') }}" method="POST" style="padding: 24px;">
            @csrf
            
            {{-- Input Jenis Ujian --}}
            <div style="margin-bottom: 15px;">
                <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Jenis / Tipe Ujian</label>
                <select name="exam_type_id" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('exam_type_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                    <option value="">-- Pilih Jenis Ujian --</option>
                    @foreach($examTypes as $et)
                        <option value="{{ $et->id }}" {{ old('exam_type_id') == $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
                    @endforeach
                </select>
                @error('exam_type_id')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Grid Row 1: Mata Pelajaran & Penempatan Kelas --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Mata Pelajaran</label>
                    <select name="subject_id" onchange="loadTeachers(this.value, 'teacher_ids')" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('subject_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->nama_mapel }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Target Rombel / Kelas <span style="color: #6a6a7a; font-weight: 500; font-size: 11px;">(Bisa Pilih Banyak)</span></label>
                    <select name="classroom_ids[]" multiple required style="width: 100%; height: 85px; padding: 10px 14px; border-radius: 10px; border: 1px solid {{ $errors->has('classroom_ids') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;">
                        @foreach($classes as $cl)
                            <option value="{{ $cl->id }}" {{ is_array(old('classroom_ids')) && in_array($cl->id, old('classroom_ids')) ? 'selected' : '' }}>{{ $cl->nama_kelas }}</option>
                        @endforeach
                    </select>
                    @error('classroom_ids')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Grid Row 2: Guru Pengampu & Pengawas Ruangan --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Guru Pengampu Mapel <span style="color: #cd0000; font-weight: 500; font-size: 11px;">(Auto AJAX)</span></label>
                    <select name="teacher_ids[]" id="teacher_ids" multiple required style="width: 100%; height: 85px; padding: 10px 14px; border-radius: 10px; border: 1px solid {{ $errors->has('teacher_ids') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: #fafafa; cursor: pointer;">
                        <option value="" disabled>-- Silakan Pilih Mapel Terlebih Dahulu --</option>
                    </select>
                    @error('teacher_ids')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Plotting Pengawas Ruangan</label>
                    <select name="proctor_id" required style="width: 100%; padding: 11px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('proctor_id') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; background: white; cursor: pointer;" onfocus="this.style.borderColor='#cd0000'">
                        <option value="">-- Pilih Pengawas --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ old('proctor_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ strtoupper($t->role) }})</option>
                        @endforeach
                    </select>
                    @error('proctor_id')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Grid Row 3: Tanggal & Durasi Pelaksanaan --}}
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 18px; margin-bottom: 25px;">
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Tanggal Pelaksanaan Ujian</label>
                    <input type="date" name="tanggal_ujian" value="{{ old('tanggal_ujian', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('tanggal_ujian') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('tanggal_ujian')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Durasi (Menit)</label>
                    <input type="number" name="durasi" placeholder="60" min="1" required style="width: 100%; padding: 10px 16px; border-radius: 10px; border: 1px solid {{ $errors->has('durasi') ? '#cd0000' : '#cbd5e1' }}; font-size: 13px; font-weight: 700; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'" value="{{ old('durasi', 60) }}">
                    @error('durasi')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Submit & Cancel Buttons bertema SEBSTAR Premium --}}
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeScheduleModal()" style="flex: 1; background: #f1f5f9; color: #475569; padding: 12px; border: none; border-radius: 30px; cursor: pointer; font-weight: 700; font-size: 13px; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                <button type="submit" style="flex: 2; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 12px; border: none; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-save"></i> Daftarkan Jadwal Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeScheduleModal() { 
        document.getElementById('scheduleModal').style.display = 'none'; 
    }

    // Pemulihan otomatis muat data guru pengampu jika reload pasca-validasi error terdeteksi
    document.addEventListener('DOMContentLoaded', function() {
        const selectedSubject = document.querySelector('select[name="subject_id"]').value;
        const oldTeachers = @json(old('teacher_ids', []));
        
        if (selectedSubject) {
            loadTeachers(selectedSubject, 'teacher_ids', oldTeachers);
        }
    });
</script>