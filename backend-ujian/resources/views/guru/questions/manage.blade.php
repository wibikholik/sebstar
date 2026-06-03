@extends('layouts.app')

@section('title', 'Kelola Soal')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; border: 1px solid #f1f5f9;">
    
    {{-- Header Informasi Jadwal & Navigasi --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
        <div>
            <a href="{{ route('guru.schedules.index') }}" style="text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.color='#c91313'" onmouseout="this.style.color='#64748b'">← Kembali ke Daftar Jadwal</a>
            <h3 style="margin: 5px 0 0 0; color: #0f172a; font-weight: 700; font-size: 22px;">{{ $schedule->subject->nama_mapel }}</h3>
            <span style="font-size: 13px; color: #64748b; font-weight: 500;">
                <i class="fas fa-school" style="margin-right: 4px;"></i> {{ $schedule->classroom->nama_kelas ?? 'Tanpa Kelas' }} | 
                <i class="fas fa-layer-group" style="margin-right: 4px;"></i> {{ $schedule->examType->name ?? 'Tipe Belum Di-set' }}
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="toggleModal('modalCopySoal')" style="background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">📋 Salin Soal</button>
            <button type="button" onclick="toggleModal('modalAddSoal')" style="background: #c91313; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#a70f0f'" onmouseout="this.style.background='#c91313'">+ Tambah Soal</button>
        </div>
    </div>

    {{-- Panel Import Soal via Excel --}}
    <div style="background: #fafafa; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; flex-direction: column; gap: 2px;">
            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">Import Soal via Excel / CSV</span>
            <span style="font-size: 12px; color: #64748b;">Aturan template: Gunakan tipe <b>pg</b> (isi opsi a-e & correct_answer) atau <b>essay</b> (kosongkan opsi).</span>
        </div>
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('guru.questions.download_template') }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;">
                <i class="fas fa-file-download"></i> Unduh Template Soal
            </a>
            
            <form action="{{ route('guru.questions.import', $schedule->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 12px; margin: 0;">
                @csrf
                <input type="file" name="file_excel" required style="font-size: 13px; color: #64748b; cursor: pointer;">
                <button type="submit" style="background: #c91313; color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; box-shadow: 0 4px 10px rgba(201, 19, 19, 0.15);">
                    🚀 Proses Import Soal
                </button>
            </form>
        </div>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- List Bertumpuk Kumpulan Butir Soal --}}
    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse($questions as $index => $q)
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; background: #ffffff; transition: 0.3s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                <span style="font-weight: 700; color: #64748b; font-size: 12px; letter-spacing: 0.5px;">
                    NO. {{ $index + 1 }} ({{ strtoupper($q->type) }}) 
                    <span style="margin-left: 8px; font-weight: 500; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px;">
                        Pembuat: {{ $q->user_id == auth()->id() ? 'Saya' : 'Admin/Lain' }}
                    </span>
                </span>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick='openEditModal({!! $q->toJson() !!})' style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #475569; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">✏️ Edit</button>
                    
                    <form action="{{ route('guru.questions.destroy', $q->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')" style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; transition: 0.2s;" onmouseover="this.style.background='#fca5a5'" onmouseout="this.style.background='#fee2e2'">🗑️</button>
                    </form>
                </div>
            </div>

            {{-- Media Gambar Soal (Jika Ada) --}}
            @if(!empty($q->question_image) && trim($q->question_image) != '')
                <div style="margin-bottom: 15px; background: #f8fafc; padding: 10px; border-radius: 8px; display: inline-block; border: 1px solid #e2e8f0;">
                    <img src="{{ asset('storage/' . $q->question_image) }}" 
                         alt="Gambar Soal SEBSTAR" 
                         style="max-width: 100%; max-height: 250px; border-radius: 6px; display: block; object-fit: contain;">
                </div>
            @endif

            {{-- Isi Teks Pertanyaan --}}
            <p style="font-size: 16px; color: #1e293b; line-height: 1.6; margin-bottom: 15px; font-weight: 500;">{!! nl2br(e($q->question_text)) !!}</p>

            {{-- Opsi / Jawaban Singkat Sesuai Tipe Soal --}}
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

{{-- Modal Copy Soal --}}
<div id="modalCopySoal" class="modal-custom" style="display: none;">
    <div class="modal-content-custom" style="max-width: 500px !important;">
        <div class="modal-header-premium">
            <h4 class="modal-title-text">
                <i class="fas fa-copy"></i> Salin Soal Sesi Lain
            </h4>
            <span onclick="toggleModal('modalCopySoal')" class="modal-close-btn">&times;</span>
        </div>
        
        <form action="{{ route('guru.questions.copy', $schedule->id) }}" method="POST" class="modal-form-body">
            @csrf
            <p style="font-size: 13px; color: #64748b; margin: 0 0 20px 0; font-weight: 600; line-height: 1.5;">
                Pilih bank jadwal yang sudah memiliki kumpulan bank soal untuk disalin secara massal ke jadwal aktif saat ini.
            </p>
            
            <div class="form-group-premium">
                <label class="form-label-premium">Jadwal Sumber Master</label>
                <select name="from_schedule_id" class="form-select-premium" required>
                    <option value="" hidden>-- Pilih Jadwal Sumber --</option>
                    @foreach($otherSchedules as $other)
                        <option value="{{ $other->id }}">
                            {{ $other->subject->nama_mapel }} - {{ $other->classroom->nama_kelas }} ({{ date('d M Y', strtotime($other->tanggal_ujian)) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="modal-actions-premium" style="margin-top: 25px;">
                <button type="button" onclick="toggleModal('modalCopySoal')" class="btn-cancel-premium">Batal</button>
                <button type="submit" class="btn-submit-premium">
                    <i class="fas fa-file-import"></i> Mulai Salin Soal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Add Soal --}}
<div id="modalAddSoal" class="modal-custom" style="display: none;">
    <div class="modal-content-custom" style="max-width: 800px !important;">
        <div class="modal-header-premium">
            <h4 class="modal-title-text">
                <i class="fas fa-plus-circle"></i> Buat Butir Soal Baru
            </h4>
            <span onclick="toggleModal('modalAddSoal')" class="modal-close-btn">&times;</span>
        </div>
        <div class="modal-form-body">
            @include('guru.questions.create')
        </div>
    </div>
</div>

{{-- Modal Edit Soal --}}
<div id="modalEditSoal" class="modal-custom" style="display: none;">
    <div class="modal-content-custom" style="max-width: 800px !important;">
        <div class="modal-header-premium">
            <h4 class="modal-title-text">
                <i class="fas fa-edit"></i> Perbarui Form Butir Soal
            </h4>
            <span onclick="toggleModal('modalEditSoal')" class="modal-close-btn">&times;</span>
        </div>
        <div class="modal-form-body">
            @include('guru.questions.edit')
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'none' || modal.style.display === '' ? 'block' : 'none';
        document.body.style.overflow = modal.style.display === 'block' ? 'hidden' : 'auto';
    }

    // Menutup modal jika area backdrop luar diklik
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-custom')) {
            event.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    function openEditModal(data) {
        const form = document.getElementById('formEditSoal');
        if(form) {
            form.action = `/guru/questions/${data.id}`;
        }

        document.getElementById('edit_type').value = data.type;
        document.getElementById('edit_question_text').value = data.question_text;

        if (data.type === 'pg') {
            document.getElementById('editPgContainer').style.display = 'block';
            document.getElementById('editEssayContainer').style.display = 'none';
            document.getElementById('edit_option_a').value = data.option_a || '';
            document.getElementById('edit_option_b').value = data.option_b || '';
            document.getElementById('edit_option_c').value = data.option_c || '';
            document.getElementById('edit_option_d').value = data.option_d || '';
            document.getElementById('edit_option_e').value = data.option_e || '';
            document.getElementById('edit_correct_answer_pg').value = data.correct_answer ? data.correct_answer.toUpperCase() : 'A';
        } else {
            document.getElementById('editPgContainer').style.display = 'none';
            document.getElementById('editEssayContainer').style.display = 'block';
            document.getElementById('edit_correct_answer_essay').value = data.correct_answer || '';
        }
        toggleModal('modalEditSoal');
    }

    function handleTypeChange(prefix) {
        const type = document.getElementById(prefix + '_type').value;
        document.getElementById(prefix + 'PgContainer').style.display = type === 'pg' ? 'block' : 'none';
        document.getElementById(prefix + 'EssayContainer').style.display = type === 'essay' ? 'block' : 'none';
    }
</script>

<style>
    /* Backdrop Modal Efek Blur */
    .modal-custom {
        position: fixed !important;
        z-index: 2000 !important;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(30, 30, 47, 0.4) !important;
        backdrop-filter: blur(4px) !important;
        overflow-y: auto;
        padding: 20px;
        box-sizing: border-box;
    }

    /* Wadah Konten Utama Modal */
    .modal-content-custom {
        background-color: #ffffff !important;
        margin: 4% auto !important;
        padding: 0 !important;
        border-radius: 16px !important;
        width: 100% !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        overflow: hidden !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        animation: modalSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) ease-out;
    }

    /* Header Bergradasi Merah */
    .modal-header-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        padding: 18px 24px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .modal-title-text {
        margin: 0 !important;
        font-weight: 700 !important;
        font-size: 15px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        color: #ffffff !important;
    }

    .modal-close-btn {
        cursor: pointer !important;
        font-size: 24px !important;
        line-height: 1 !important;
        font-weight: 300 !important;
        color: #ffffff !important;
        opacity: 0.8 !important;
        transition: all 0.2s !important;
    }

    .modal-close-btn:hover {
        opacity: 0.5 !important;
    }

    /* Form Body */
    .modal-form-body {
        padding: 24px !important;
    }

    .form-group-premium {
        margin-bottom: 16px !important;
    }

    .form-label-premium {
        display: block !important;
        margin-bottom: 6px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
    }

    /* Elemen Input & Select Custom */
    .form-select-premium,
    .form-input-premium {
        width: 100% !important;
        padding: 11px 16px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #1e1e2f !important;
        background: #ffffff !important;
        outline: none !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important;
    }

    .form-select-premium:focus,
    .form-input-premium:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    /* Tombol Aksi di Modal */
    .modal-actions-premium {
        display: flex !important;
        gap: 12px !important;
    }

    .btn-cancel-premium {
        flex: 1 !important;
        padding: 12px !important;
        border-radius: 30px !important;
        border: 1px solid #edf0f5 !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
    }

    .btn-cancel-premium:hover {
        background: #e2e8f0 !important;
    }

    .btn-submit-premium {
        flex: 2 !important;
        padding: 12px !important;
        border-radius: 30px !important;
        border: none !important;
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
    }

    .btn-submit-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 20px rgba(205, 0, 0, 0.35) !important;
        filter: brightness(1.1) !important;
    }

    /* Animasi Lembut Saat Muncul */
    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endsection