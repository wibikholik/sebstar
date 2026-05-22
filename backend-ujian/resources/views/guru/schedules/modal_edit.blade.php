<div id="editModal" class="modal-custom" style="display: {{ $errors->any() && session('error_form_type') === 'edit' ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(30, 30, 47, 0.4); backdrop-filter: blur(4px);">
    <div class="modal-content-custom" style="background-color: white; margin: 4% auto; padding: 0; border-radius: 16px; width: 540px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); animation: slideIn 0.3s ease-out;">
        
        {{-- Header Modal Bertema Gradasi Merah Premium SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-edit"></i> Edit Jadwal Ujian Mandiri
            </h4>
            <span onclick="closeEditModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; color: white; opacity: 0.9; transition: 0.2s;" onmouseover="this.style.opacity='0.6'" onmouseout="this.style.opacity='0.9'">&times;</span>
        </div>

        <form id="editForm" method="POST" style="padding: 24px;">
            @csrf
            @method('PUT')
            
            {{-- Input Edit Jenis Ujian --}}
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1e1e2f;">Jenis / Tipe Ujian</label>
                <select name="exam_type_id" id="edit_exam_type_id" required style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('exam_type_id') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; background: white; outline: none; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @foreach($examTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('exam_type_id')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Input Edit Target Rombel Kelas --}}
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1e1e2f;">Target Rombel Kelas</label>
                <select name="classroom_id" id="edit_classroom_id" required style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('classroom_id') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; background: white; outline: none; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#cd0000'">
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
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
                    <input type="date" name="tanggal_ujian" id="edit_tanggal_ujian" value="{{ old('tanggal_ujian') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid {{ $errors->has('tanggal_ujian') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('tanggal_ujian')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 700; color: #1e1e2f; display: block; margin-bottom: 6px;">Durasi (Menit)</label>
                    <input type="number" name="durasi" id="edit_durasi" min="5" value="{{ old('durasi') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid {{ $errors->has('durasi') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1e1e2f; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                    @error('durasi')
                        <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons Bertema SEBSTAR Premium --}}
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeEditModal()" style="flex: 1; padding: 12px; border-radius: 30px; border: none; background: #f1f5f9; color: #475569; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 30px; border: none; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-check-circle"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    /**
     * Fungsi Utama: Dipanggil dari tag onclick tombol edit di halaman index utama
     */
    function openEditModal(schedule) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        // Mengarahkan route URL action secara dinamis sesuai ID jadwal ujian mandiri terpilih
        form.action = "{{ url('guru/schedules') }}/" + schedule.id;
        
        // Memulihkan data ketikan lama (old input) jika ditolak validasi, atau pasang nilai asli database
        document.getElementById('edit_exam_type_id').value = "{{ old('exam_type_id') }}" || schedule.exam_type_id;
        document.getElementById('edit_classroom_id').value = "{{ old('classroom_id') }}" || schedule.classroom_id;
        document.getElementById('edit_tanggal_ujian').value = "{{ old('tanggal_ujian') }}" || schedule.tanggal_ujian;
        document.getElementById('edit_durasi').value = "{{ old('durasi') }}" || schedule.durasi;
        
        modal.style.display = 'block';
    }

    // Menutup modal secara intuitif apabila guru mengklik area transparan di luar kotak putih form
    window.addEventListener('click', function(event) {
        const editModal = document.getElementById('editModal');
        if (event.target === editModal) {
            closeEditModal();
        }
    });
</script>