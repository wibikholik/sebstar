@extends('layouts.app')
@section('title', 'Manajemen Jadwal Ujian')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 24px;">Jadwal Pelaksanaan Ujian</h3>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Manajemen durasi pengerjaan, mata pelajaran, dan plotting pengawas.</p>
        </div>
        <button onclick="openScheduleModal()" style="background: #c91313; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(201, 19, 19, 0.2);">
            + Tambah Jadwal
        </button>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div style="background: #dcfce7; border-left: 4px solid #15803d; color: #15803d; padding: 16px; border-radius: 8px; margin-bottom: 25px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Filter & Search --}}
    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
        <form action="{{ route('admin.schedules.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Mapel atau Token..." style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
            </div>
            <div>
                <select name="exam_type_id" style="padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: white;">
                    <option value="">Semua Jenis Ujian</option>
                    @foreach($examTypes as $et)
                        <option value="{{ $et->id }}" {{ request('exam_type_id') == $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Cari</button>
                @if(request()->anyFilled(['search', 'exam_type_id', 'status']))
                    <a href="{{ route('admin.schedules.index') }}" style="background: #e2e8f0; color: #475569; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">UJIAN & KELAS</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">PENGAMPU & PENGAWAS</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">WAKTU & DURASI</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">TOKEN</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">STATUS</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <span style="font-size: 10px; background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 20px; font-weight: 700; display: inline-block; margin-bottom: 5px;">
                            {{ $s->examType->name ?? 'N/A' }}
                        </span>
                        <div style="font-weight: 700; color: #0f172a; font-size: 16px;">{{ $s->subject->nama_mapel }}</div>
                        <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                            <span style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">Kelas {{ $s->classroom->nama_kelas }}</span>
                        </div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <div style="margin-bottom: 8px;">
                            <small style="color: #94a3b8; display: block; font-size: 11px;">PENGAMPU:</small>
                            @php 
                                $teacherIds = is_array($s->teacher_ids) ? $s->teacher_ids : json_decode($s->teacher_ids, true);
                                $teachers_list = \App\Models\User::whereIn('id', $teacherIds ?? [])->get(); 
                            @endphp
                            @foreach($teachers_list as $t)
                                <span style="font-size: 12px; font-weight: 500;">{{ $t->name }}</span>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                        <div>
                            <small style="color: #94a3b8; display: block; font-size: 11px;">PENGAWAS:</small>
                            <span style="font-size: 12px; font-weight: 700; color: #c91313;">{{ $s->proctor->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        {{-- Format Tanggal Indonesia --}}
                        <div style="font-size: 14px; font-weight: 600;">📅 {{ \Carbon\Carbon::parse($s->tanggal_ujian)->translatedFormat('d F Y') }}</div>
                        <div style="font-size: 13px; color: #c91313; background: #fff1f2; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-top: 5px; font-weight: 700;">
                            ⏱️ {{ $s->durasi }} Menit
                        </div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <span style="font-family: monospace; font-size: 15px; font-weight: 800; background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 8px; border: 1px dashed #f59e0b;">{{ $s->token }}</span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <form action="{{ route('admin.schedules.status', $s->id) }}" method="POST">
                            @csrf
                            <select name="status" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; border: none; cursor: pointer; {{ $s->status == 'aktif' ? 'background: #dcfce7; color: #15803d;' : 'background: #fee2e2; color: #991b1b;' }}">
                                <option value="aktif" {{ $s->status == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                                <option value="nonaktif" {{ $s->status == 'nonaktif' ? 'selected' : '' }}>OFF</option>
                            </select>
                        </form>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <button type="button" onclick='openEditModal(@json($s))' style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px; border-radius: 6px; cursor: pointer;">✏️</button>
                            <a href="{{ route('admin.questions.index', $s->id) }}" style="text-decoration: none; background: #1e293b; color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">📝 Soal</a>
                            <form action="{{ route('admin.schedules.destroy', $s->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus jadwal ini? Seluruh soal dan nilai siswa akan hilang!')" style="background: #fee2e2; border: 1px solid #fecaca; padding: 8px; border-radius: 6px; cursor: pointer;">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">Belum ada jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('admin.schedules.modal_create')
@include('admin.schedules.modal_edit')

<script>
    function openScheduleModal() { document.getElementById('scheduleModal').style.display = 'block'; }
    function closeScheduleModal() { document.getElementById('scheduleModal').style.display = 'none'; }
    function closeEditModal() { document.getElementById('editScheduleModal').style.display = 'none'; }

    // Fungsi Utama AJAX Load Guru
    function loadTeachers(subjectId, targetSelectId, selectedIds = []) {
        const teacherSelect = document.getElementById(targetSelectId);
        if(!teacherSelect) return;

        teacherSelect.innerHTML = '<option value="" disabled selected>⏳ Memuat guru...</option>';
        
        if(!subjectId) {
            teacherSelect.innerHTML = '<option value="" disabled>Pilih Mapel Dulu</option>';
            return;
        }

        fetch(`/admin/get-teachers/${subjectId}?t=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                teacherSelect.innerHTML = '';
                if(data.length === 0) {
                    teacherSelect.innerHTML = '<option value="" disabled>Tidak ada guru untuk mapel ini</option>';
                } else {
                    data.forEach(g => {
                        let opt = document.createElement('option');
                        opt.value = g.id;
                        opt.text = g.name;
                        // Cek jika ID guru ini ada dalam daftar yang dipilih (untuk Edit)
                        if (Array.isArray(selectedIds) && selectedIds.map(String).includes(String(g.id))) {
                            opt.selected = true;
                        }
                        teacherSelect.appendChild(opt);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                teacherSelect.innerHTML = '<option value="" disabled>Gagal memuat data</option>';
            });
    }

    function openEditModal(schedule) {
        const modal = document.getElementById('editScheduleModal');
        const form = document.getElementById('editScheduleForm');
        form.action = "/admin/schedules/" + schedule.id;
        
        document.getElementById('edit_exam_type_id').value = schedule.exam_type_id;
        document.getElementById('edit_subject_id').value = schedule.subject_id;
        document.getElementById('edit_classroom_id').value = schedule.classroom_id;
        document.getElementById('edit_proctor_id').value = schedule.proctor_id;
        document.getElementById('edit_tanggal_ujian').value = schedule.tanggal_ujian;
        document.getElementById('edit_durasi').value = schedule.durasi;

        // Load guru pengampu untuk mapel terkait dan tandai yang sudah dipilih
        let currentTeachers = schedule.teacher_ids;
        if(typeof currentTeachers === 'string') currentTeachers = JSON.parse(currentTeachers);
        
        loadTeachers(schedule.subject_id, 'edit_teacher_ids', currentTeachers);
        
        modal.style.display = 'block';
    }
</script>
@endsection