@extends('layouts.app')
@section('title', 'Manajemen Jadwal Ujian')

<style>
    /* Background dengan Gradasi Merah-Putih Tegas + Efek Polkadot Grid Modern */
    body {
        background-color: #f4f5f9 !important;
        background-image: 
            radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
            linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%) !important;
        background-size: 24px 24px, 100% 100% !important;
        background-attachment: fixed !important;
    }

    /* Pembungkus Konten Box Putih Premium */
    .content-box-premium {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    /* Tombol Action Gradasi Merah Dashboard */
    .btn-action-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 30px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .btn-action-premium:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 22px rgba(205, 0, 0, 0.4) !important;
        filter: brightness(1.1) !important;
    }

    /* Input & Select Filter Style */
    .filter-input-premium {
        width: 100%; 
        padding: 11px 16px; 
        border: 1px solid #cbd5e1; 
        border-radius: 10px; 
        font-size: 13px; 
        font-weight: 600; 
        color: #1e1e2f; 
        outline: none; 
        background: white; 
        box-sizing: border-box;
        transition: all 0.2s ease;
    }
    
    .filter-input-premium:focus {
        border-color: #cd0000;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1);
    }
</style>

@section('content')
<div class="content-box-premium">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div>
                <h3 style="margin: 0; color: #1e1e2f; font-weight: 700; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-calendar-alt" style="color: #cd0000;"></i> Jadwal Pelaksanaan Ujian
                </h3>
                <p style="margin: 4px 0 0 0; color: #6a6a7a; font-size: 13px; font-weight: 600;">Manajemen durasi pengerjaan, mata pelajaran, dan plotting pengawas.</p>
            </div>
        </div>
        <button onclick="openScheduleModal()" class="btn-action-premium">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </button>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div style="background: rgba(46, 204, 113, 0.1); border-left: 4px solid #2ecc71; color: #27ae60; padding: 14px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 600; font-size: 13px;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Filter & Search --}}
    <div style="background: #fafafa; padding: 18px; border-radius: 12px; border: 1px solid #edf0f5; margin-bottom: 25px;">
        <form action="{{ route('admin.schedules.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Mapel atau Token..." class="filter-input-premium">
            </div>
            <div>
                <select name="exam_type_id" class="filter-input-premium" style="min-width: 180px; cursor: pointer;">
                    <option value="">Semua Jenis Ujian</option>
                    @foreach($examTypes as $et)
                        <option value="{{ $et->id }}" {{ request('exam_type_id') == $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #1e293b; color: white; border: none; padding: 11px 22px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 13px;">Cari</button>
                @if(request()->anyFilled(['search', 'exam_type_id', 'status']))
                    <a href="{{ route('admin.schedules.index') }}" style="background: #e2e8f0; color: #475569; text-decoration: none; padding: 11px 22px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-block; line-height: 1.2;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table Area --}}
    <div style="overflow-x: auto; background: white; border-radius: 12px; border: 1px solid #edf0f5;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #fafafa; border-bottom: 2px solid #edf0f5;">
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">UJIAN & KELAS</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">PENGAMPU & PENGAWAS</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">WAKTU & DURASI</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">TOKEN</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">STATUS</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody style="color: #1e1e2f;">
                @forelse($schedules as $s)
                <tr style="border-bottom: 1px solid #edf0f5; transition: background 0.2s;">
                    <td style="padding: 18px 20px; vertical-align: top;">
                        <span style="font-size: 10px; background: rgba(230, 57, 70, 0.1); color: #cd0000; padding: 4px 10px; border-radius: 20px; font-weight: 700; display: inline-block; margin-bottom: 6px;">
                            {{ $s->examType->name ?? 'N/A' }}
                        </span>
                        <div style="font-weight: 700; font-size: 15px;">{{ $s->subject->nama_mapel ?? 'Mapel Terhapus' }}</div>
                        <div style="font-size: 12px; color: #6a6a7a; margin-top: 5px; font-weight: 600;">
                            <span style="background: #fafafa; padding: 3px 8px; border-radius: 6px; border: 1px solid #edf0f5;">Kelas {{ $s->classroom->nama_kelas ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td style="padding: 18px 20px; vertical-align: top;">
                        <div style="margin-bottom: 8px;">
                            <small style="color: #a0a0b0; display: block; font-size: 10px; font-weight: 700;">PENGAMPU:</small>
                            @php 
                                $teacherIds = is_array($s->teacher_ids) ? $s->teacher_ids : json_decode($s->teacher_ids, true);
                                $teachers_list = \App\Models\User::whereIn('id', $teacherIds ?? [])->get(); 
                            @endphp
                            @forelse($teachers_list as $t)
                                <span style="font-size: 13px; font-weight: 600; color: #1e1e2f;">{{ $t->name }}</span>{{ !$loop->last ? ', ' : '' }}
                            @empty
                                <span style="font-size: 12px; color: #cbd5e1; font-style: italic;">Belum diplot</span>
                            @endforelse
                        </div>
                        <div>
                            <small style="color: #a0a0b0; display: block; font-size: 10px; font-weight: 700;">PENGAWAS:</small>
                            <span style="font-size: 13px; font-weight: 700; color: #cd0000;">{{ $s->proctor->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td style="padding: 18px 20px; vertical-align: top;">
                        <div style="font-size: 13px; font-weight: 700; color: #1e1e2f;">📅 {{ \Carbon\Carbon::parse($s->tanggal_ujian)->translatedFormat('d F Y') }}</div>
                        <div style="font-size: 11px; color: #cd0000; background: rgba(230, 57, 70, 0.08); padding: 4px 8px; border-radius: 6px; display: inline-block; margin-top: 6px; font-weight: 700; border: 1px solid rgba(230, 57, 70, 0.15);">
                            ⏱️ {{ $s->durasi }} Menit
                        </div>
                    </td>
                    <td style="padding: 18px 20px; text-align: center; vertical-align: middle;">
                        <span style="font-family: monospace; font-size: 14px; font-weight: 700; background: #fffbeb; color: #b45309; padding: 6px 12px; border-radius: 8px; border: 1px dashed #f59e0b; letter-spacing: 0.5px;">{{ $s->token }}</span>
                    </td>
                    <td style="padding: 18px 20px; text-align: center; vertical-align: middle;">
                        <form action="{{ route('admin.schedules.status', $s->id) }}" method="POST">
                            @csrf
                            <select name="status" onchange="this.form.submit()" style="padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid transparent; cursor: pointer; {{ $s->status == 'aktif' ? 'background: rgba(46, 204, 113, 0.15); color: #27ae60;' : 'background: rgba(231, 76, 60, 0.1); color: #c0392b;' }}">
                                <option value="aktif" {{ $s->status == 'aktif' ? 'selected' : '' }}>AKTIF</option>
                                <option value="nonaktif" {{ $s->status == 'nonaktif' ? 'selected' : '' }}>OFF</option>
                            </select>
                        </form>
                    </td>
                    <td style="padding: 18px 20px; text-align: center; vertical-align: middle;">
                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                            <button type="button" onclick='openEditModal(@json($s))' style="background: #fafafa; border: 1px solid #cbd5e1; padding: 7px 10px; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 700; color: #475569;" title="Edit Jadwal">✏️</button>
                            <a href="{{ route('admin.questions.index', $s->id) }}" style="text-decoration: none; background: #1e293b; color: white; padding: 7px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;">📝 Soal</a>
                            <form action="{{ route('admin.schedules.destroy', $s->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus jadwal ini? Seluruh soal dan nilai siswa akan hilang!')" style="background: rgba(231, 76, 60, 0.08); border: 1px solid rgba(231, 76, 60, 0.2); padding: 7px 10px; border-radius: 8px; cursor: pointer; font-size: 12px;">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px 20px; color: #6a6a7a; font-weight: 600; font-size: 14px;">
                        <div style="font-size: 24px; margin-bottom: 10px;">📋</div>
                        Belum ada jadwal pelaksanaan ujian yang terdaftar.
                    </td>
                </tr>
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
        let currentTeachers = schedule.teacher_ids;
        if(typeof currentTeachers === 'string') currentTeachers = JSON.parse(currentTeachers);
        loadTeachers(schedule.subject_id, 'edit_teacher_ids', currentTeachers);
        modal.style.display = 'block';
    }
</script>
@endsection