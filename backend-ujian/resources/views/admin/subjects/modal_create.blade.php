<div id="modalCreate" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 8% auto; padding: 0; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); overflow: hidden; animation: slideIn 0.3s ease-out;">
        
        <div style="background-color: #cd0000; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px;">Tambah Mata Pelajaran</h3>
            <span onclick="closeModal()" style="cursor: pointer; font-size: 28px; line-height: 1;">&times;</span>
        </div>

        <div style="padding: 25px;">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 13px; color: #64748b;">Kode Mapel:</label>
                    <input type="text" name="kode_mapel" required placeholder="Contoh: MAT-01" style="width: 100%; padding: 12px; margin-top: 5px; box-sizing: border-box; border: 1px solid #e1e4e8; border-radius: 8px; background: #f8f9fa;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-weight: 600; font-size: 13px; color: #64748b;">Nama Mata Pelajaran:</label>
                    <input type="text" name="nama_mapel" required placeholder="Contoh: Matematika" style="width: 100%; padding: 12px; margin-top: 5px; box-sizing: border-box; border: 1px solid #e1e4e8; border-radius: 8px; background: #f8f9fa;">
                </div>

                <button type="submit" style="background-color: #cd0000; color: white; padding: 14px; border: none; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 15px;">
                    Simpan Mata Pelajaran
                </button>
            </form>
        </div>
    </div>
</div>