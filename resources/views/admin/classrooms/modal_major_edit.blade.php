<div id="modalMajorEdit" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px);">
    <div style="background-color: white; margin: 8% auto; padding: 0; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); overflow: hidden;">
        
        <div style="background-color: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;">Edit Data Jurusan</h3>
            <span onclick="closeMajorEditModal()" style="cursor: pointer; font-size: 24px; line-height: 1;">&times;</span>
        </div>

        <div style="padding: 20px;">
            <form id="editMajorForm" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Nama Jurusan:</label>
                    <input type="text" name="nama_jurusan" id="edit_major_name" required 
                           style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 6px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Singkatan:</label>
                    <input type="text" name="singkatan" id="edit_major_short" required 
                           style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 6px; text-transform: uppercase;">
                </div>

                <button type="submit" style="background-color: #2c3e50; color: white; padding: 12px; border: none; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    Update Jurusan
                </button>
            </form>
        </div>
    </div>
</div>