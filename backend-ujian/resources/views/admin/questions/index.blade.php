@extends('layouts.app')
@section('title', 'Kelola Soal Admin')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; border: 1px solid #f1f5f9;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
        <div>
            <a href="{{ route('admin.schedules.index') }}" style="text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.color='#c91313'" onmouseout="this.style.color='#64748b'">← Kembali</a>
            <h3 style="margin: 5px 0 0 0; color: #0f172a; font-weight: 700; font-size: 22px;">{{ $schedule->subject->nama_mapel }}</h3>
            <span style="font-size: 13px; color: #64748b; font-weight: 500;">
                <i class="fas fa-school" style="margin-right: 4px;"></i> {{ $schedule->classroom->nama_kelas ?? 'Tanpa Kelas' }} | 
                <i class="fas fa-layer-group" style="margin-right: 4px;"></i> {{ $schedule->examType->name ?? 'Tipe Belum Di-set' }}
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="openModal('copyModal')" style="background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">📋 Salin Soal</button>
            <button type="button" onclick="openModal('createModal')" style="background: #c91313; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#a70f0f'" onmouseout="this.style.background='#c91313'">+ Tambah Soal</button>
        </div>
    </div>

    <div style="background: #fafafa; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; flex-direction: column; gap: 2px;">
            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">Import Soal via Excel / CSV</span>
            <span style="font-size: 12px; color: #64748b;">Aturan template: Gunakan tipe <b>pg</b> (isi opsi a-e & correct_answer) atau <b>essay</b> (kosongkan opsi).</span>
        </div>
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('admin.questions.download_template') }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;">
                <i class="fas fa-file-download"></i> Unduh Template Soal
            </a>
            
            <form action="{{ route('admin.questions.import', $schedule->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 12px; margin: 0;">
                @csrf
                <input type="file" name="file_excel" required style="font-size: 13px; color: #64748b; cursor: pointer;">
                <button type="submit" style="background: #c91313; color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; box-shadow: 0 4px 10px rgba(201, 19, 19, 0.15);">
                    🚀 Proses Import Soal
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse($questions as $q)
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; background: #ffffff; transition: 0.3s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                <span style="font-weight: 700; color: #64748b; font-size: 12px; letter-spacing: 0.5px;">NO. {{ $loop->iteration }} ({{ strtoupper($q->type) }})</span>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick='openEditModal(@json($q))' style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #475569; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">✏️ Edit</button>
                    <form action="{{ route('admin.questions.destroy', [$schedule->id, $q->id]) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')" style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; transition: 0.2s;" onmouseover="this.style.background='#fca5a5'" onmouseout="this.style.background='#fee2e2'">🗑️</button>
                    </form>
                </div>
            </div>

            @if(!empty($q->question_image) && trim($q->question_image) != '')
                <div style="margin-bottom: 15px; background: #f8fafc; padding: 10px; border-radius: 8px; display: inline-block; border: 1px solid #e2e8f0;">
                    <img src="{{ asset('storage/' . $q->question_image) }}" 
                         alt="Gambar Soal SEBSTAR" 
                         style="max-width: 100%; max-height: 250px; border-radius: 6px; display: block; object-fit: contain;">
                </div>
            @endif

            <p style="font-size: 16px; color: #1e293b; line-height: 1.6; margin-bottom: 15px; font-weight: 500;">{!! nl2br(e($q->question_text)) !!}</p>

            @if($q->type == 'pg')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    @foreach(['a','b','c','d','e'] as $o)
                        @if(!empty($q->{'option_'.$o}))
                            <div style="padding: 12px 16px; border-radius: 8px; border: 1px solid {{ strtoupper($q->correct_answer) == strtoupper($o) ? '#bbf7d0' : '#e2e8f0' }}; background: {{ strtoupper($q->correct_answer) == strtoupper($o) ? '#f0fdf4' : 'white' }}; font-size: 14px; color: #334155;">
                                <strong style="color: {{ strtoupper($q->correct_answer) == strtoupper($o) ? '#16a34a' : '#64748b' }};">{{ strtoupper($o) }}.</strong> {{ $q->{'option_'.$o} }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div style="padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #c91313; font-size: 14px; color: #334155;">
                    <strong style="color: #c91313;"><i class="fas fa-info-circle"></i> Pedoman Jawaban:</strong>
                    <div style="margin-top: 5px; line-height: 1.5;">{!! nl2br(e($q->correct_answer)) !!}</div>
                </div>
            @endif
        </div>
        @empty
            <div style="text-align: center; padding: 50px; color: #64748b; border: 2px dashed #e2e8f0; border-radius: 12px; background: #fafafa;">
                <i class="fas fa-folder-open" style="font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                Belum ada butir soal dalam jadwal ujian ini.
            </div>
        @endforelse
    </div>
</div>

<div id="copyModal" class="modal-bg" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 10% auto; padding: 30px; border-radius: 16px; width: 500px; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #1e293b; font-weight: 700;">📋 Salin Soal</h3>
        <form action="{{ route('admin.questions.copy', $schedule->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 14px; color: #475569;">Pilih Jadwal Sumber</label>
                <select name="from_schedule_id" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; outline: none; background: white;">
                    <option value="">-- Pilih Jadwal Sumber --</option>
                    @foreach(\App\Models\Schedule::with(['classroom', 'examType'])->where('id', '!=', $schedule->id)->where('subject_id', $schedule->subject_id)->get() as $other)
                        <option value="{{ $other->id }}">
                            Kelas: {{ $other->classroom->nama_kelas ?? '???' }} | Tipe: {{ $other->examType->name ?? 'Belum Di-set' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeModal('copyModal')" style="flex: 1; padding: 12px; border-radius: 8px; background: #f1f5f9; border: none; cursor: pointer; font-weight: 600; color: #475569;">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; background: #1e293b; color: white; font-weight: 700; cursor: pointer;">Mulai Salin</button>
            </div>
        </form>
    </div>
</div>

<div id="createModal" class="modal-bg" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 3% auto; padding: 30px; border-radius: 16px; width: 700px; max-height: 85vh; overflow-y: auto; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #c91313; font-weight: 700;">Tambah Soal Baru</h3>
        <form action="{{ route('admin.questions.store', $schedule->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Tipe Soal</label>
                <select name="type" id="add_type" onchange="handleTypeChange('add')" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: white;" required>
                    <option value="pg">Pilihan Ganda (PG)</option>
                    <option value="essay">Essay / Uraian</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Pertanyaan</label>
                <textarea name="question_text" style="width: 100%; min-height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; outline: none;" required></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Gambar (Opsional)</label>
                <input type="file" name="question_image" accept="image/*" style="width: 100%; font-size: 14px;">
            </div>
            
            <div id="addPgContainer">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Pilihan Opsi Jawaban</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    @foreach(['a','b','c','d','e'] as $opt)
                        <input type="text" name="option_{{ $opt }}" placeholder="Opsi {{ strtoupper($opt) }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                    @endforeach
                </div>
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Kunci Jawaban Benar</label>
                <select name="correct_answer_pg" style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #c91313; outline: none; background: white;">
                    @foreach(['A','B','C','D','E'] as $k) <option value="{{ $k }}">Kunci: {{ $k }}</option> @endforeach
                </select>
            </div>
            
            <div id="addEssayContainer" style="display: none; margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Pedoman Jawaban Essay</label>
                <textarea name="correct_answer_essay" style="width: 100%; min-height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; outline: none;" placeholder="Tuliskan kata kunci kunci jawaban essay atau rubrik penilaian..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" onclick="closeModal('createModal')" style="flex: 1; padding: 12px; border-radius: 8px; background: #f1f5f9; border: none; cursor: pointer; font-weight: 600; color: #475569;">Batal</button>
                <button type="submit" style="flex: 2; padding: 12px; border-radius: 8px; background: #c91313; color: white; font-weight: 700; cursor: pointer;">Simpan Soal</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal-bg" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);">
    <div style="background: white; margin: 3% auto; padding: 30px; border-radius: 16px; width: 700px; max-height: 85vh; overflow-y: auto; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #1e293b; font-weight: 700;">Edit Soal Ujian</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Tipe Soal</label>
                <select name="type" id="edit_type" onchange="handleTypeChange('edit')" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; background: white;" required>
                    <option value="pg">Pilihan Ganda (PG)</option>
                    <option value="essay">Essay / Uraian</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Pertanyaan</label>
                <textarea id="edit_question_text" name="question_text" style="width: 100%; min-height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; outline: none;" required></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Ganti Gambar (Opsional)</label>
                <input type="file" name="question_image" accept="image/*" style="width: 100%; font-size: 14px;">
            </div>
            
            <div id="editPgContainer">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Pilihan Opsi Jawaban</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    @foreach(['a','b','c','d','e'] as $opt)
                        <input type="text" id="edit_option_{{ $opt }}" name="option_{{ $opt }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                    @endforeach
                </div>
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Kunci Jawaban Benar</label>
                <select id="edit_correct_answer_pg" name="correct_answer_pg" style="width: 100%; padding: 10px; border-radius: 8px; border: 2px solid #cbd5e1; outline: none; background: white;">
                    @foreach(['A','B','C','D','E'] as $k) <option value="{{ $k }}">Kunci: {{ $k }}</option> @endforeach
                </select>
            </div>
            
            <div id="editEssayContainer" style="display: none; margin-bottom: 15px;">
                <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 14px; color: #475569;">Pedoman Jawaban Essay</label>
                <textarea id="edit_correct_answer_essay" name="correct_answer_essay" style="width: 100%; min-height: 100px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit; outline: none;"></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" onclick="closeModal('editModal')" style="flex: 1; padding: 12px; border-radius: 8px; background: #f1f5f9; border: none; cursor: pointer; font-weight: 600; color: #475569;">Batal</button>
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
            document.getElementById('edit_option_a').value = q.option_a || '';
            document.getElementById('edit_option_b').value = q.option_b || '';
            document.getElementById('edit_option_c').value = q.option_c || '';
            document.getElementById('edit_option_d').value = q.option_d || '';
            document.getElementById('edit_option_e').value = q.option_e || '';
            document.getElementById('edit_correct_answer_pg').value = q.correct_answer ? q.correct_answer.toUpperCase() : 'A';
        } else {
            document.getElementById('edit_correct_answer_essay').value = q.correct_answer || '';
        }
        openModal('editModal');
    }

    // Menutup modal otomatis jika area latar belakang di luar boks diklik
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-bg')) {
            event.target.style.display = "none";
            document.body.style.overflow = 'auto';
        }
    }
</script>
@endsection