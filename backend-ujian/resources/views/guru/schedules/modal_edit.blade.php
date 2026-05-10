<div id="editModal" class="modal-custom">
    <div class="modal-content-custom">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 style="margin: 0; font-weight: 700; color: #0f172a;">Edit Jadwal Mandiri</h4>
            <span onclick="closeEditModal()" style="cursor: pointer; font-size: 24px; color: #64748b;">&times;</span>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">Jenis Ujian</label>
                <select name="exam_type_id" id="edit_exam_type_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    @foreach($examTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">Kelas</label>
                <select name="classroom_id" id="edit_classroom_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 600;">Tanggal</label>
                    <input type="date" name="tanggal_ujian" id="edit_tanggal_ujian" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 600;">Durasi (Menit)</label>
                    <input type="number" name="durasi" id="edit_durasi" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeEditModal()" style="flex: 1; padding: 12px; border-radius: 8px; border: none; background: #f1f5f9;">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; border: none; background: #1e293b; color: white;">Update Jadwal</button>
            </div>
        </form>
    </div>
</div>