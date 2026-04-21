@extends('layouts.app')
@section('title', 'Manajemen Kelas & Jurusan')
@section('content')
<div class="content-box" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e293b;">Jadwal Pelaksanaan Ujian</h3>
        <button onclick="openScheduleModal()" style="background: #cd0000; color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: 600;">
            + Tambah Jadwal
        </button>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 15px;">Mapel & Kelas</th>
                <th style="padding: 15px;">Guru Pengampu</th>
                <th style="padding: 15px;">Waktu</th>
                <th style="padding: 15px; text-align: center;">Token</th>
                <th style="padding: 15px; text-align: center;">Status</th>
                <th style="padding: 15px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $s)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 15px;">
                    <div style="font-weight: 700;">{{ $s->subject->nama_mapel }}</div>
                    <div style="font-size: 11px; color: #64748b;">{{ $s->classroom->nama_kelas }}</div>
                </td>
                <td style="padding: 15px;">
                    @php $teachers = \App\Models\User::whereIn('id', $s->teacher_ids ?? [])->get(); @endphp
                    @foreach($teachers as $t)
                        <div style="font-size: 11px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; margin-bottom: 3px; border: 1px solid #e2e8f0; display: inline-block;">
                            {{ $t->name }}
                        </div>
                    @endforeach
                </td>
                <td style="padding: 15px;">
                    <div style="font-size: 13px;">📅 {{ date('d/m/Y', strtotime($s->tanggal_ujian)) }}</div>
                    <div style="font-size: 12px; color: #64748b;">⏰ {{ $s->jam_mulai }} ({{ $s->durasi }}m)</div>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <span style="font-family: monospace; font-weight: 800; background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 6px;">{{ $s->token }}</span>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <form action="{{ route('admin.schedules.status', $s->id) }}" method="POST">
                        @csrf
                        <select name="status" onchange="this.form.submit()" style="padding: 5px; border-radius: 20px; font-size: 10px; font-weight: 800; cursor: pointer; {{ $s->status == 'aktif' ? 'background: #dcfce7; color: #15803d;' : 'background: #fee2e2; color: #991b1b;' }}">
                            <option value="aktif" {{ $s->status == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                            <option value="nonaktif" {{ $s->status == 'nonaktif' ? 'selected' : '' }}>OFF</option>
                        </select>
                    </form>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <button onclick="openEditModal({{ $s->toJson() }})" style="background: none; border: none; cursor: pointer; font-size: 18px;">✏️</button>
                    <form action="{{ route('admin.schedules.destroy', $s->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 18px;">🗑️</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.schedules.modal_create')
@include('admin.schedules.modal_edit')

<script>
    function openScheduleModal() { document.getElementById('scheduleModal').style.display = 'block'; }
    function closeScheduleModal() { document.getElementById('scheduleModal').style.display = 'none'; }
    function closeEditModal() { document.getElementById('editScheduleModal').style.display = 'none'; }

    // Fungsi Utama Loading Guru (Bisa dipakai Create & Edit)
    function loadTeachers(subjectId, targetSelectId, selectedIds = []) {
        const teacherSelect = document.getElementById(targetSelectId);
        teacherSelect.innerHTML = '<option value="">⏳ Memuat...</option>';
        
        if(!subjectId) return;

        fetch("{{ url('admin/get-teachers') }}/" + subjectId)
            .then(res => res.json())
            .then(data => {
                teacherSelect.innerHTML = '';
                data.forEach(g => {
                    // Cek apakah ID ini masuk dalam list yang dipilih sebelumnya (untuk Edit)
                    const isSelected = selectedIds.map(String).includes(String(g.id)) ? 'selected' : '';
                    teacherSelect.innerHTML += `<option value="${g.id}" ${isSelected}>${g.name}</option>`;
                });
            });
    }

    function openEditModal(schedule) {
        const modal = document.getElementById('editScheduleModal');
        const form = document.getElementById('editScheduleForm');
        
        form.action = "{{ url('admin/schedules') }}/" + schedule.id;
        
        document.getElementById('edit_subject_id').value = schedule.subject_id;
        document.getElementById('edit_classroom_id').value = schedule.classroom_id;
        document.getElementById('edit_tanggal_ujian').value = schedule.tanggal_ujian;
        document.getElementById('edit_jam_mulai').value = schedule.jam_mulai;
        document.getElementById('edit_jam_selesai').value = schedule.jam_selesai;
        document.getElementById('edit_durasi').value = schedule.durasi;

        // Load guru khusus untuk mapel ini dan tandai yang sudah terpilih
        loadTeachers(schedule.subject_id, 'edit_teacher_ids', schedule.teacher_ids);

        modal.style.display = 'block';
    }
</script>
@endsection