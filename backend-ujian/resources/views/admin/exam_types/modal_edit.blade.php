<div id="editTypeModal" class="modal-custom">
    <div class="modal-content-custom">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 style="margin: 0; font-weight: 700;">Edit Tipe Ujian</h4>
            <span onclick="closeEditTypeModal()" style="cursor: pointer; font-size: 24px;">&times;</span>
        </div>

        <form id="editTypeForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: #475569;">Nama Tipe Ujian</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <div style="background: #f1f5f9; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border: 1px dashed #cbd5e1;">
                <input type="checkbox" name="is_teacher_manageable" id="edit_is_teacher_manageable" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="edit_is_teacher_manageable" style="margin: 0; font-size: 13px; color: #1e293b; cursor: pointer; font-weight: 500;">Izinkan Guru mengelola sendiri</label>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeEditTypeModal()" style="flex: 1; background: #e2e8f0; color: #475569; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="flex: 2; background: #1e293b; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer;">Update Data</button>
            </div>
        </form>
    </div>
</div>