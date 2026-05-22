<div id="createModal" class="modal-custom" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(30, 30, 47, 0.4); backdrop-filter: blur(4px);">
    <div class="modal-content-custom" style="background-color: white; margin: 4% auto; padding: 0; border-radius: 16px; width: 540px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); animation: slideIn 0.3s ease-out;">
        
        {{-- Header Modal Bertema Gradasi Merah Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-plus"></i> Buat Jadwal Ujian Mandiri Baru
            </h4>
            <span onclick="closeCreateModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; color: white; opacity: 0.9; transition: 0.2s;" onmouseover="this.style.opacity='0.6'" onmouseout="this.style.opacity='0.9'">&times;</span>
        </div>

        <form action="{{ route('guru.schedules.store') }}" method="POST" style="padding: 24px;">
            @csrf
            
            {{-- Input Jenis Ujian --}}
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1e1e2f;">Jenis / Tipe Ujian</label>
                <select name="exam_type_id" required style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('exam_type_id') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; background: white; outline: none; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    <option value="" hidden>-- Pilih Jenis --</option>
                    @foreach($examTypes as $type)
                        <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('exam_type_id')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Input Mata Pelajaran --}}
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1e1e2f;">Mata Pelajaran (Tugas Mengajar Anda)</label>
                <select name="subject_id" required style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('subject_id') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; background: #f8fafc; outline: none;">
                    @forelse($mySubjects as $sub)
                        <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->nama_mapel }}</option>
                    @empty
                        <option value="" disabled selected>Mapel belum ditugaskan (Hubungi Admin)</option>
                    @endforelse
                </select>
                @error('subject_id')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Input Pilihan Rombel Kelas --}}
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1e1e2f;">Target Rombel Kelas</label>
                <select name="classroom_id" required style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('classroom_id') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; background: white; outline: none; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    <option value="" hidden>-- Pilih Kelas Peserta --</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ old('classroom_id') == $cls->id ? 'selected' : '' }}>{{ $cls->nama_kelas }}</option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Grid Row: Tanggal Pelaksanaan & Durasi Menit --}}
            <div style="display: flex; gap: 16px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Tanggal Ujian</label>
                    <input type="date" name="tanggal_ujian" value="{{ old('tanggal_ujian', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid {{ $errors->has('tanggal_ujian') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('tanggal_ujian')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Durasi (Menit)</label>
                    <input type="number" name="durasi" placeholder="60" min="5" value="{{ old('durasi', 60) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid {{ $errors->has('durasi') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('durasi')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons Bertema SEBSTAR Premium --}}
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeCreateModal()" style="flex: 1; padding: 12px; border-radius: 30px; border: none; background: #f1f5f9; color: #475569; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                
                {{-- PERBAIKAN: Menggunakan arahan @disabled bawaan Laravel Blade agar aman dari syntax error --}}
                <button type="submit" @disabled($mySubjects->isEmpty()) style="flex: 2; padding: 12px; border-radius: 30px; border: none; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-save"></i> Daftarkan Sesi Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').style.display = 'block';
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    window.addEventListener('click', function(event) {
        const createModal = document.getElementById('createModal');
        if (event.target === createModal) {
            closeCreateModal();
        }
    });
</script>

<style>
    @keyframes slideIn {
        from { transform: translateY(-25px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>