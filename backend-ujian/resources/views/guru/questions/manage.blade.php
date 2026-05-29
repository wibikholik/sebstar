@extends('layouts.app')

@section('title', 'Kelola Soal')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('guru.schedules.index') }}" style="text-decoration: none; color: #64748b; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;" onmouseover="this.style.color='#cd0000'" onmouseout="this.style.color='#64748b'">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
    </a>
</div>

{{-- Alert Notifikasi --}}
@if(session('success'))
    <div style="background: #ecfdf5; border-left: 5px solid #10b981; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <i class="fas fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fff5f5; border-left: 5px solid #cd0000; color: #950000; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <i class="fas fa-exclamation-circle" style="margin-right: 5px;"></i> {{ session('error') }}
    </div>
@endif

{{-- Header Informasi Jadwal --}}
<div class="content-box" style="background: #fff; padding: 25px; border-radius: 16px; border-left: 5px solid #cd0000; margin-bottom: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05); border-left: 5px solid #cd0000;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <span style="font-size: 11px; background: #fff5f5; color: #cd0000; padding: 5px 12px; border-radius: 20px; font-weight: 800; text-transform: uppercase; border: 1px solid rgba(205,0,0,0.15); letter-spacing: 0.5px;">
                {{ $schedule->examType->name ?? 'Ujian' }}
            </span>
            <h3 style="margin: 12px 0 6px; font-size: 24px; color: #1e1e2f; font-weight: 800;">{{ $schedule->subject->nama_mapel }}</h3>
            <p style="margin: 0; color: #64748b; font-size: 14px; font-weight: 600;">Kelas: <strong style="color: #1e1e2f; font-weight: 700;">{{ $schedule->classroom->nama_kelas }}</strong></p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button onclick="toggleModal('modalCopySoal')" class="btn-secondary-premium">
                <i class="fas fa-copy"></i> Salin Soal
            </button>
            <button onclick="toggleModal('modalAddSoal')" class="btn-primary-premium">
                <i class="fas fa-plus-circle"></i> Tambah Soal
            </button>
        </div>
    </div>
</div>

{{-- Main Content Table --}}
<div class="content-box" style="background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05);">
    <h4 style="margin: 0 0 20px 0; color: #1e1e2f; font-weight: 800; font-size: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-list-ul" style="color: #cd0000;"></i> Daftar Soal Sesi Ini <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 20px; font-size: 12px;">{{ count($questions) }}</span>
    </h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #edf0f5; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                    <th style="padding: 12px; width: 50px; text-align: center;">NO</th>
                    <th style="padding: 12px;">PERTANYAAN</th>
                    <th style="padding: 12px; width: 100px;">TIPE</th>
                    <th style="padding: 12px; width: 120px;">PEMBUAT</th>
                    <th style="padding: 12px; text-align: center; width: 150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $index => $q)
                <tr style="border-bottom: 1px solid #edf0f5; transition: 0.2s;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 15px; color: #64748b; font-weight: 700; text-align: center; font-size: 13px;">{{ $index + 1 }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600; color: #1e1e2f; font-size: 14px; line-height: 1.5;">{{ Str::limit(strip_tags($q->question_text), 100) }}</div>
                        @if($q->question_image) 
                            <small style="color: #cd0000; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; margin-top: 5px; background: #fff5f5; padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                <i class="fas fa-image"></i> Ada Gambar
                            </small> 
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        <span style="text-transform: uppercase; font-size: 10px; font-weight: 800; background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            {{ $q->type }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <span style="font-size: 12px; color: #475569; font-weight: 600;">
                            {{ $q->user_id == auth()->id() ? 'Saya' : 'Admin/Lain' }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 15px;">
                            <button type="button" onclick='openEditModal({!! $q->toJson() !!})' style="color: #3b82f6; border: none; background: none; font-weight: 700; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; padding: 0;">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('guru.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #cd0000; font-weight: 700; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; padding: 0;">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 60px;">
                        <div style="color: #94a3b8; font-size: 14px; font-weight: 600;">
                            <i class="fas fa-folder-open" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                            Belum ada soal untuk jadwal ini.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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

    /* Tombol Utama Premium (Merah Gradasi) */
    .btn-primary-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 30px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.2) !important;
        transition: 0.2s ease !important;
    }

    .btn-primary-premium:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 8px 20px rgba(205, 0, 0, 0.3) !important;
        filter: brightness(1.1);
    }

    /* Tombol Sekunder Premium (Slate Gelap) */
    .btn-secondary-premium {
        background: #1e293b !important;
        color: #fff !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 30px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: 0.2s ease !important;
    }

    .btn-secondary-premium:hover {
        background: #0f172a !important;
        transform: translateY(-1px) !important;
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