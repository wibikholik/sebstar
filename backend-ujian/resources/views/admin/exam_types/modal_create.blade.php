<div id="typeModal" class="modal-custom" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }}; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(30, 30, 47, 0.4); backdrop-filter: blur(4px);">
    <div class="modal-content-custom" style="background: white; margin: 10% auto; padding: 0; border-radius: 16px; width: 460px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
        
        {{-- Header Modal Gradasi SEBSTAR --}}
        <div style="background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-layer-group"></i> Tambah Tipe Ujian Baru
            </h4>
            <span onclick="closeTypeModal()" style="cursor: pointer; font-size: 22px; line-height: 1; font-weight: 300; opacity: 0.9;" onmouseover="this.style.opacity='0.6'" onmouseout="this.style.opacity='0.9'">&times;</span>
        </div>

        <form action="{{ route('admin.exam-types.store') }}" method="POST" style="padding: 24px;">
            @csrf
            
            {{-- Input Nama Tipe --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1e1e2f;">Nama Tipe Ujian</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Misal: Ulangan Harian, Kuis, UTS" required style="width: 100%; padding: 11px 16px; border: 1px solid {{ $errors->has('name') ? '#cd0000' : '#cbd5e1' }}; border-radius: 10px; font-size: 13px; font-weight: 600; color: #1e1e2f; outline: none; transition: all 0.2s; box-sizing: border-box;" onfocus="this.style.borderColor='#cd0000'">
                @error('name')
                    <span style="color: #cd0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 700;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Checkbox Hak Akses Guru --}}
            <div style="background: #fafafa; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; border: 1px solid #edf0f5;">
                <input type="checkbox" name="is_teacher_manageable" id="checkTambah" {{ old('is_teacher_manageable') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer; accent-color: #cd0000;">
                <label for="checkTambah" style="margin: 0; font-size: 13px; color: #475569; cursor: pointer; font-weight: 700; user-select: none;">Izinkan Guru mengelola secara mandiri</label>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeTypeModal()" style="flex: 1; background: #f1f5f9; color: #475569; border: none; padding: 12px; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                <button type="submit" style="flex: 2; background: linear-gradient(135deg, #cd0000 0%, #950000 100%); color: white; border: none; padding: 12px; border-radius: 30px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 5px 15px rgba(205, 0, 0, 0.2); transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">Simpan Tipe Ujian</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTypeModal() {
        document.getElementById('typeModal').style.display = 'block';
    }
    function closeTypeModal() {
        document.getElementById('typeModal').style.display = 'none';
    }
</script>