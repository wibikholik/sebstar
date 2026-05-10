<div id="createModal" class="modal-custom">
    <div class="modal-content-custom">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 style="margin: 0; font-weight: 700; color: #0f172a;">Buat Jadwal Mandiri</h4>
            <span onclick="closeCreateModal()" style="cursor: pointer; font-size: 24px; color: #64748b;">&times;</span>
        </div>

        <form action="{{ route('guru.schedules.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">Jenis Ujian</label>
                <select name="exam_type_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <option value="" hidden>-- Pilih Jenis --</option>
                    @foreach($examTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">Mata Pelajaran (Tugas Anda)</label>
                <select name="subject_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;">
                    @forelse($mySubjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->nama_mapel }}</option>
                    @empty
                        <option value="" disabled selected>Mapel belum ditugaskan (Cek profil User)</option>
                    @endforelse
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">Kelas</label>
                <select name="classroom_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    <option value="" hidden>-- Pilih Kelas --</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 600;">Tanggal</label>
                    <input type="date" name="tanggal_ujian" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 13px; font-weight: 600;">Durasi (Menit)</label>
                    <input type="number" name="durasi" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeCreateModal()" style="flex: 1; padding: 12px; border-radius: 8px; border: none; background: #f1f5f9;">Batal</button>
                <button type="submit" @if($mySubjects->isEmpty()) disabled @endif style="flex: 2; padding: 12px; border-radius: 8px; border: none; background: #c91313; color: white;">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>