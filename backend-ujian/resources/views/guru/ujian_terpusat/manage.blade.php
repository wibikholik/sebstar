@extends('layouts.app')

@section('title', 'Kelola Soal')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('guru.ujian-terpusat.index') }}" style="text-decoration: none; color: #666; font-weight: 600;">← Kembali ke Daftar Jadwal</a>
</div>

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
        {{ session('error') }}
    </div>
@endif

<div class="content-box" style="background: #fff; padding: 25px; border-radius: 15px; border-left: 5px solid #c91313; margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0; font-size: 22px;">{{ $schedule->subject->name }}</h3>
            <p style="margin: 5px 0 0; color: #666;">Kelas: <strong>{{ $schedule->classroom->name }}</strong></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="toggleModal('modalCopySoal')" style="background: #1e293b; color: #fff; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; cursor: pointer;">
                📋 Salin Soal
            </button>
            <button onclick="toggleModal('modalAddSoal')" style="background: #c91313; color: #fff; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 700; cursor: pointer;">
                + Tambah Soal
            </button>
        </div>
    </div>
</div>

<div class="content-box" style="background: #fff; padding: 25px; border-radius: 15px;">
    <h4 style="margin-bottom: 20px;">Daftar Soal Sesi Ini</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px; width: 50px;">No</th>
                    <th>Pertanyaan</th>
                    <th style="width: 100px;">Tipe</th>
                    <th style="width: 120px;">Pembuat</th>
                    <th style="text-align: center; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $index => $q)
                <tr style="border-bottom: 1px solid #f4f4f4;">
                    <td style="padding: 15px;">{{ $index + 1 }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600;">{{ Str::limit($q->question_text, 80) }}</div>
                        @if($q->question_image) <small style="color: #1565c0;">(Ada Gambar)</small> @endif
                    </td>
                    <td style="padding: 15px;"><span style="text-transform: uppercase; font-size: 11px; font-weight: 800;">{{ $q->type }}</span></td>
                    <td style="padding: 15px;">
                        <span style="font-size: 12px; background: #eee; padding: 2px 8px; border-radius: 5px;">
                            {{ $q->user->role ?? 'Admin' }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 15px;">
                            <button type="button" onclick='openEditModal({!! $q->toJson() !!})' style="color: #1565c0; border: none; background: none; font-weight: 700; cursor: pointer; font-family: inherit;">Edit</button>
                            <form action="{{ route('guru.ujian-terpusat.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #c91313; font-weight: 700; cursor: pointer; font-family: inherit;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: #999;">Belum ada soal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Copy Soal --}}
<div id="modalCopySoal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); overflow-y: auto; padding: 20px;">
    <div style="background: #fff; margin: 5% auto; padding: 25px; width: 100%; max-width: 500px; border-radius: 15px; position: relative;">
        <button onclick="toggleModal('modalCopySoal')" style="position: absolute; right: 15px; top: 15px; background: #eee; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 20px;">Salin Soal dari Jadwal Lain</h3>
        <form action="{{ route('guru.ujian-terpusat.copy', $schedule->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Pilih Jadwal Sumber</label>
                <select name="from_schedule_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required>
                    <option value="">-- Pilih Jadwal Sumber --</option>
                    @php
                        $teacherId = Auth::id();
                        $otherSchedules = \App\Models\Schedule::with(['classroom'])
                            ->where('id', '!=', $schedule->id)
                            ->where('subject_id', $schedule->subject_id)
                            ->where(function($query) use ($teacherId) {
                                $query->whereJsonContains('teacher_ids', (string)$teacherId)
                                      ->orWhere('teacher_ids', $teacherId);
                            })->get();
                    @endphp
                    @foreach($otherSchedules as $other)
                        <option value="{{ $other->id }}">Kelas {{ $other->classroom->name }} ({{ $other->tanggal_ujian }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: #1e293b; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Mulai Salin</button>
        </form>
    </div>
</div>

{{-- Modal Add --}}
<div id="modalAddSoal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); overflow-y: auto; padding: 20px;">
    <div style="background: #fff; margin: 2% auto; padding: 25px; width: 100%; max-width: 650px; border-radius: 15px; position: relative;">
        <button onclick="toggleModal('modalAddSoal')" style="position: absolute; right: 15px; top: 15px; background: #eee; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 20px;">Tambah Soal</h3>
        @include('guru.ujian_terpusat.create')
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditSoal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); overflow-y: auto; padding: 20px;">
    <div style="background: #fff; margin: 2% auto; padding: 25px; width: 100%; max-width: 650px; border-radius: 15px; position: relative;">
        <button onclick="toggleModal('modalEditSoal')" style="position: absolute; right: 15px; top: 15px; background: #eee; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">&times;</button>
        <h3 style="margin-bottom: 20px;">Edit Soal</h3>
        @include('guru.ujian_terpusat.edit')
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
        form.action = `/guru/ujian-terpusat/${data.id}`;

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