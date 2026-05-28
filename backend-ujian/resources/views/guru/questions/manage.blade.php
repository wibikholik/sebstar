@extends('layouts.app')

@section('title', 'Kelola Soal')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('guru.schedules.index') }}" style="text-decoration: none; color: #666; font-weight: 600;">← Kembali ke Daftar Jadwal</a>
</div>

{{-- Alert Notifikasi --}}
@if(session('success'))
    <div style="background: #d4edda; border-left: 5px solid #28a745; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
        ✓ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; border-left: 5px solid #dc3545; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
        ⚠ {{ session('error') }}
    </div>
@endif

<div class="content-box" style="background: #fff; padding: 25px; border-radius: 15px; border-left: 5px solid #c91313; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <span style="font-size: 12px; background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 20px; font-weight: 700; text-transform: uppercase;">
                {{ $schedule->examType->name ?? 'Ujian' }}
            </span>
            <h3 style="margin: 10px 0 0; font-size: 24px; color: #0f172a;">{{ $schedule->subject->nama_mapel }}</h3>
            <p style="margin: 5px 0 0; color: #64748b;">Kelas: <strong>{{ $schedule->classroom->nama_kelas }}</strong></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="toggleModal('modalCopySoal')" style="background: #1e293b; color: #fff; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;">
                📋 Salin Soal
            </button>
            <button onclick="toggleModal('modalAddSoal')" style="background: #c91313; color: #fff; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;">
                + Tambah Soal
            </button>
        </div>
    </div>
</div>

<div class="content-box" style="background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <h4 style="margin-bottom: 20px; color: #1e293b;">Daftar Soal Sesi Ini ({{ count($questions) }})</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 13px;">
                    <th style="padding: 12px; width: 50px;">NO</th>
                    <th>PERTANYAAN</th>
                    <th style="width: 100px;">TIPE</th>
                    <th style="width: 120px;">PEMBUAT</th>
                    <th style="text-align: center; width: 150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $index => $q)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600; color: #1e293b;">{{ Str::limit(strip_tags($q->question_text), 100) }}</div>
                        @if($q->question_image) 
                            <small style="color: #3b82f6; font-weight: 700;">🖼️ Ada Gambar</small> 
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        <span style="text-transform: uppercase; font-size: 10px; font-weight: 800; background: #f1f5f9; padding: 4px 8px; border-radius: 5px;">
                            {{ $q->type }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <span style="font-size: 11px; color: #475569;">
                            {{ $q->user_id == auth()->id() ? 'Saya' : 'Admin/Lain' }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 15px;">
                            <button type="button" onclick='openEditModal({!! $q->toJson() !!})' style="color: #3b82f6; border: none; background: none; font-weight: 700; cursor: pointer; font-size: 14px;">Edit</button>
                            <form action="{{ route('guru.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; font-weight: 700; cursor: pointer; font-size: 14px;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 60px;">
                        <div style="color: #94a3b8; font-size: 14px;">Belum ada soal untuk jadwal ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Copy Soal --}}
<div id="modalCopySoal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); overflow-y: auto; padding: 20px;">
    <div style="background: #fff; margin: 5% auto; padding: 30px; width: 100%; max-width: 500px; border-radius: 15px; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <button onclick="toggleModal('modalCopySoal')" style="position: absolute; right: 15px; top: 15px; background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 20px; color: #0f172a;">Salin Soal</h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">Pilih jadwal yang sudah memiliki soal untuk disalin ke jadwal ini.</p>
        
        <form action="{{ route('guru.questions.copy', $schedule->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px; color: #475569;">Jadwal Sumber</label>
                <select name="from_schedule_id" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1; outline: none;" required>
                    <option value="">-- Pilih Jadwal Sumber --</option>
                    @foreach($otherSchedules as $other)
                        <option value="{{ $other->id }}">
                            {{ $other->subject->nama_mapel }} - {{ $other->classroom->nama_kelas }} ({{ date('d M Y', strtotime($other->tanggal_ujian)) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="width: 100%; padding: 14px; background: #1e293b; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer;">
                Mulai Salin Soal
            </button>
        </form>
    </div>
</div>

{{-- Modal Add --}}
<div id="modalAddSoal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); overflow-y: auto; padding: 20px;">
    <div style="background: #fff; margin: 2% auto; padding: 30px; width: 100%; max-width: 800px; border-radius: 15px; position: relative;">
        <button onclick="toggleModal('modalAddSoal')" style="position: absolute; right: 15px; top: 15px; background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 25px; color: #0f172a;">Buat Soal Baru</h3>
        @include('guru.questions.create')
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditSoal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); overflow-y: auto; padding: 20px;">
    <div style="background: #fff; margin: 2% auto; padding: 30px; width: 100%; max-width: 800px; border-radius: 15px; position: relative;">
        <button onclick="toggleModal('modalEditSoal')" style="position: absolute; right: 15px; top: 15px; background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 25px; color: #0f172a;">Perbarui Soal</h3>
        @include('guru.questions.edit')
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
        document.body.style.overflow = modal.style.display === 'block' ? 'hidden' : 'auto';
    }

    function openEditModal(data) {
        const form = document.getElementById('formEditSoal');
        // Update action URL ke route guru.questions.update
        form.action = `/guru/questions/${data.id}`;

        document.getElementById('edit_type').value = data.type;
        document.getElementById('edit_question_text').value = data.question_text;

        if (data.type === 'pg') {
            document.getElementById('editPgContainer').style.display = 'block';
            document.getElementById('editEssayContainer').style.display = 'none';
            document.getElementById('edit_option_a').value = data.option_a;
            document.getElementById('edit_option_b').value = data.option_b;
            document.getElementById('edit_option_c').value = data.option_c;
            document.getElementById('edit_option_d').value = data.option_d;
            document.getElementById('edit_option_e').value = data.option_e;
            document.getElementById('edit_correct_answer_pg').value = data.correct_answer;
        } else {
            document.getElementById('editPgContainer').style.display = 'none';
            document.getElementById('editEssayContainer').style.display = 'block';
            document.getElementById('edit_correct_answer_essay').value = data.correct_answer;
        }
        toggleModal('modalEditSoal');
    }

    function handleTypeChange(prefix) {
        const type = document.getElementById(prefix + '_type').value;
        document.getElementById(prefix + 'PgContainer').style.display = type === 'pg' ? 'block' : 'none';
        document.getElementById(prefix + 'EssayContainer').style.display = type === 'essay' ? 'block' : 'none';
    }
</script>
@endsection