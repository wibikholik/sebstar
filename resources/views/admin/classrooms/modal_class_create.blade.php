<div id="modalClass" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 8% auto; padding: 0; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); overflow: hidden; animation: slideIn 0.3s ease-out;">
        
        <div style="background-color: #cd0000; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;">Tambah Kelas Baru</h3>
            <span onclick="closeClassModal()" style="cursor: pointer; font-size: 24px; line-height: 1;">&times;</span>
        </div>

        <div style="padding: 20px;">
            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Nama Kelas:</label>
                    <input type="text" name="nama_kelas" required placeholder="Contoh: XII RPL 1" style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Pilih Jurusan:</label>
                    <select name="major_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer;">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_jurusan }} ({{ $m->singkatan }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" style="background-color: #cd0000; color: white; padding: 12px; border: none; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    Simpan Kelas
                </button>
            </form>
        </div>
    </div>
</div>