@extends('layouts.app')
@section('title', 'Kelola Soal Admin')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; border: 1px solid #f1f5f9;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
        <div>
            <a href="{{ route('admin.schedules.index') }}" style="text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600;">← Kembali</a>
            <h3 style="margin: 5px 0 0 0; color: #0f172a; font-weight: 700;">{{ $schedule->subject->nama_mapel }}</h3>
            <span style="font-size: 13px; color: #64748b;">
                {{ $schedule->classroom->nama_kelas ?? 'Tanpa Kelas' }} | 
                {{ $schedule->examType->name ?? 'Tipe Belum Di-set' }}
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="openModal('copyModal')" style="background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">📋 Salin Soal</button>
            <button type="button" onclick="openModal('createModal')" style="background: #c91313; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">+ Tambah Soal</button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">{{ session('error') }}</div>
    @endif

    <!-- Daftar Soal -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse($questions as $q)
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <span style="font-weight: 700; color: #64748b; font-size: 12px;">NO. {{ $loop->iteration }} ({{ strtoupper($q->type) }})</span>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick='openEditModal(@json($q))' style="background: #f1f5f9; border: 1px solid #ddd; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">✏️ Edit</button>
                    <form action="{{ route('admin.questions.destroy', [$schedule->id, $q->id]) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus soal?')" style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">🗑️</button>
                    </form>
                </div>
            </div>

            @if($q->question_image)
                <img src="{{ asset('storage/' . $q->question_image) }}" style="max-width: 250px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #eee;">
            @endif

            <p style="font-size: 16px; color: #1e293b; line-height: 1.6; margin-bottom: 15px;">{!! nl2br(e($q->question_text)) !!}</p>

            @if($q->type == 'pg')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    @foreach(['a','b','c','d','e'] as $o)
                        <div style="padding: 12px; border-radius: 8px; border: 1px solid {{ strtoupper($q->correct_answer) == strtoupper($o) ? '#bbf7d0' : '#f1f5f9' }}; background: {{ strtoupper($q->correct_answer) == strtoupper($o) ? '#f0fdf4' : 'white' }}; font-size: 14px;">
                            <strong>{{ strtoupper($o) }}.</strong> {{ $q->{'option_'.$o} }}
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #c91313; font-size: 14px;">
                    <strong>Pedoman Jawaban:</strong><br>{{ $q->correct_answer }}
                </div>
            @endif
        </div>
        @empty
            <div style="text-align: center; padding: 50px; color: #64748b; border: 2px dashed #f1f5f9; border-radius: 12px;">Belum ada soal.</div>
        @endforelse
    </div>
</div>

<!-- MODAL COPY -->
<div id="copyModal" class="modal-bg" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 5% auto; padding: 30px; border-radius: 16px; width: 500px; position: relative;">
        <h3 style="margin-top: 0; color: #1e293b;">📋 Salin Soal</h3>
        <form action="{{ route('admin.questions.copy', $schedule->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">Pilih Jadwal Sumber</label>
                <select name="from_schedule_id" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                    <option value="">-- Pilih Jadwal Sumber --</option>
                    @foreach(\App\Models\Schedule::with(['classroom', 'examType'])->where('id', '!=', $schedule->id)->where('subject_id', $schedule->subject_id)->get() as $other)
                        <option value="{{ $other->id }}">
                            Kelas: {{ $other->classroom->nama_kelas ?? '???' }} | Tipe: {{ $other->examType->name ?? 'Belum Di-set' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeModal('copyModal')" style="flex: 1; padding: 12px; border-radius: 8px; background: #f1f5f9; border: none; cursor: pointer;">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; background: #1e293b; color: white; font-weight: 700; cursor: pointer;">Mulai Salin</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CREATE -->
<div id="createModal" class="modal-bg" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 30px; border-radius: 16px; width: 700px; max-height: 90vh; overflow-y: auto; position: relative;">
        <h3 style="margin-top: 0; color: #c91313;">Tambah Soal Baru</h3>
        <form action="{{ route('admin.questions.store', $schedule->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Tipe Soal</label>
                <select name="type" id="add_type" onchange="handleTypeChange('add')" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required>
                    <option value="pg">Pilihan Ganda (PG)</option>
                    <option value="essay">Essay / Uraian</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Pertanyaan</label>
                <textarea name="question_text" style="width: 100%; min-height: 80px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Gambar (Opsional)</label>
                <input type="file" name="question_image" accept="image/*" style="width: 100%;">
            </div>
            <div id="addPgContainer">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    @foreach(['a','b','c','d','e'] as $opt)
                        <input type="text" name="option_{{ $opt }}" placeholder="Opsi {{ strtoupper($opt) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    @endforeach
                </div>
                <select name="correct_answer_pg" style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #c91313;">
                    @foreach(['A','B','C','D','E'] as $k) <option value="{{ $k }}">Kunci: {{ $k }}</option> @endforeach
                </select>
            </div>
            <div id="addEssayContainer" style="display: none; margin-bottom: 15px;">
                <textarea name="correct_answer_essay" style="width: 100%; min-height: 80px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" placeholder="Pedoman jawaban essay..."></textarea>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="closeModal('createModal')" style="flex: 1; padding: 12px; border-radius: 8px; background: #f1f5f9; border: none; cursor: pointer;">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; background: #c91313; color: white; font-weight: 700; cursor: pointer;">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="editModal" class="modal-bg" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 2% auto; padding: 30px; border-radius: 16px; width: 700px; max-height: 90vh; overflow-y: auto; position: relative;">
        <h3 style="margin-top: 0; color: #1e293b;">Edit Soal</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Tipe Soal</label>
                <select name="type" id="edit_type" onchange="handleTypeChange('edit')" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required>
                    <option value="pg">Pilihan Ganda (PG)</option>
                    <option value="essay">Essay / Uraian</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Pertanyaan</label>
                <textarea id="edit_question_text" name="question_text" style="width: 100%; min-height: 80px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px;">Ganti Gambar (Opsional)</label>
                <input type="file" name="question_image" accept="image/*" style="width: 100%;">
            </div>
            <div id="editPgContainer">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    @foreach(['a','b','c','d','e'] as $opt)
                        <input type="text" id="edit_option_{{ $opt }}" name="option_{{ $opt }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                    @endforeach
                </div>
                <select id="edit_correct_answer_pg" name="correct_answer_pg" style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #c91313;">
                    @foreach(['A','B','C','D','E'] as $k) <option value="{{ $k }}">Kunci: {{ $k }}</option> @endforeach
                </select>
            </div>
            <div id="editEssayContainer" style="display: none; margin-bottom: 15px;">
                <textarea id="edit_correct_answer_essay" name="correct_answer_essay" style="width: 100%; min-height: 80px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="closeModal('editModal')" style="flex: 1; padding: 12px; border-radius: 8px; background: #f1f5f9; border: none; cursor: pointer;">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; background: #1e293b; color: white; font-weight: 700; cursor: pointer;">Update Soal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function handleTypeChange(mode) {
        const type = document.getElementById(mode + '_type').value;
        const pg = document.getElementById(mode + 'PgContainer');
        const essay = document.getElementById(mode + 'EssayContainer');
        if (type === 'pg') {
            pg.style.display = 'block';
            essay.style.display = 'none';
        } else {
            pg.style.display = 'none';
            essay.style.display = 'block';
        }
    }

    function openEditModal(q) {
        document.getElementById('editForm').action = `/admin/schedules/${q.schedule_id}/questions/${q.id}`;
        document.getElementById('edit_type').value = q.type;
        document.getElementById('edit_question_text').value = q.question_text;
        
        handleTypeChange('edit');

        if(q.type === 'pg') {
            document.getElementById('edit_option_a').value = q.option_a;
            document.getElementById('edit_option_b').value = q.option_b;
            document.getElementById('edit_option_c').value = q.option_c;
            document.getElementById('edit_option_d').value = q.option_d;
            document.getElementById('edit_option_e').value = q.option_e;
            document.getElementById('edit_correct_answer_pg').value = q.correct_answer;
        } else {
            document.getElementById('edit_correct_answer_essay').value = q.correct_answer;
        }
        openModal('editModal');
    }

    // Menutup modal jika klik di luar area modal
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-bg')) {
            event.target.style.display = "none";
            document.body.style.overflow = 'auto';
        }
    }
</script>
@endsection