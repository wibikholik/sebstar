@extends('layouts.app')
@section('title', 'Koreksi Jawaban')

@section('content')
<form action="{{ route('guru.koreksi.update', $student->id) }}" method="POST" class="form-koreksi-premium">
    @csrf
    @method('PUT')
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

    {{-- Header Profil Siswa --}}
    <div class="profile-header-premium">
        <div class="profile-avatar-placeholder">
            <i class="fas fa-user-gradient"></i>
        </div>
        <div class="profile-meta-premium">
            <h3 class="student-name-premium">{{ $student->name }}</h3>
            <p class="subject-subtext-premium">
                <i class="fas fa-book-open"></i> Mata Pelajaran: <strong>{{ $schedule->subject->nama_mapel ?? $schedule->subject->name }}</strong>
            </p>
        </div>
    </div>

    {{-- Loop Butir Jawaban Essay --}}
    @foreach($essayAnswers as $index => $item)
    <div class="essay-card-premium">
        {{-- Nomor Soal --}}
        <div class="card-badge-header">
            <span class="question-number-tag">SOAL NOMOR {{ $index + 1 }}</span>
        </div>
        
        <div class="card-body-premium">
            {{-- Blok Pertanyaan --}}
            <div class="section-block-premium">
                <label class="block-label-premium"><i class="fas fa-question-circle"></i> Pertanyaan:</label>
                <div class="question-display-box">
                    {!! $item->question->question_text !!}
                </div>
            </div>

            {{-- Blok Jawaban Siswa --}}
            <div class="section-block-premium">
                <label class="block-label-premium color-red-accent"><i class="fas fa-pen-alt"></i> Jawaban Siswa:</label>
                <div class="student-answer-box">
                    {{ $item->answer ?? '(Siswa tidak mengisi jawaban)' }}
                </div>
            </div>

            {{-- Panel Penilaian (Skor & Feedback) --}}
            <div class="assessment-panel-grid">
                <div class="score-input-wrapper">
                    <label class="panel-label-premium">Skor (0-100)</label>
                    <input type="number" 
                           name="scores[{{ $item->id }}]" 
                           value="{{ $item->score }}" 
                           step="0.01" 
                           required 
                           min="0" 
                           max="100"
                           placeholder="0"
                           class="input-score-premium">
                </div>
                <div class="feedback-input-wrapper">
                    <label class="panel-label-premium">Catatan / Feedback Guru</label>
                    <textarea name="notes[{{ $item->id }}]" 
                              placeholder="Tulis ringkasan masukan atau evaluasi pengerjaan siswa di sini..." 
                              class="textarea-feedback-premium">{{ $item->teacher_note }}</textarea>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Floating/Sticky Footer Action Bar --}}
    <div class="sticky-footer-action-bar">
        <a href="{{ route('guru.koreksi.index', ['schedule_id' => $schedule->id]) }}" class="btn-cancel-premium">
            <i class="fas fa-times-circle"></i> Batalkan Penilaian
        </a>
        <button type="submit" class="btn-save-all-premium">
            <i class="fas fa-cloud-upload-alt"></i> Simpan Seluruh Penilaian
        </button>
    </div>
</form>

<style>
    /* Header Profil Siswa */
    .profile-header-premium {
        background: #ffffff !important;
        padding: 24px !important;
        border-radius: 16px !important;
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 12px rgba(30, 30, 47, 0.04) !important;
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
    }
    .profile-avatar-placeholder {
        width: 48px;
        height: 48px;
        background: #fff5f5;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cd0000;
        font-size: 20px;
    }
    .student-name-premium {
        margin: 0 !important;
        color: #1e1e2f !important;
        font-weight: 800 !important;
        font-size: 22px !important;
    }
    .subject-subtext-premium {
        margin: 4px 0 0 0 !important;
        color: #64748b !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
    }
    .subject-subtext-premium strong {
        color: #1e1e2f !important;
    }

    /* Kartu Per Butir Soal Essay */
    .essay-card-premium {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid #edf2f7 !important;
        overflow: hidden !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 12px rgba(30, 30, 47, 0.03) !important;
    }
    .card-badge-header {
        background: #f8fafc !important;
        padding: 12px 24px !important;
        border-bottom: 1px solid #edf2f7 !important;
    }
    .question-number-tag {
        font-weight: 800 !important;
        color: #94a3b8 !important;
        font-size: 11px !important;
        letter-spacing: 1px !important;
    }
    .card-body-premium {
        padding: 24px !important;
    }

    /* Sub Komponen Blok */
    .section-block-premium {
        margin-bottom: 20px !important;
    }
    .block-label-premium {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        color: #64748b !important;
        font-weight: 800 !important;
        font-size: 11px !important;
        margin-bottom: 8px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
    }
    .block-label-premium.color-red-accent {
        color: #cd0000 !important;
    }

    /* Tampilan Kotak Pertanyaan & Jawaban */
    .question-display-box {
        font-size: 14.5px !important;
        color: #334155 !important;
        background: #f8fafc !important;
        padding: 16px 20px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        line-height: 1.6 !important;
        font-weight: 600;
    }
    .student-answer-box {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        border: 1px solid #fee2e2 !important;
        padding: 18px 20px !important;
        border-radius: 12px !important;
        background: #fffcfc !important;
        min-height: 60px !important;
        line-height: 1.6 !important;
    }

    /* Panel Penilaian Grid */
    .assessment-panel-grid {
        display: grid !important;
        grid-template-columns: 160px 1fr !important;
        gap: 20px !important;
        background: #f8fafc !important;
        padding: 20px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
    }
    .panel-label-premium {
        display: block !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        color: #475569 !important;
        margin-bottom: 8px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
    }

    /* Input Komponen Nilai & Feedback */
    .input-score-premium,
    .textarea-feedback-premium {
        width: 100% !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        outline: none !important;
        background: #ffffff !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important;
        color: #1e1e2f !important;
    }
    .input-score-premium {
        padding: 11px !important;
        font-weight: 800 !important;
        font-size: 20px !important;
        text-align: center !important;
    }
    .textarea-feedback-premium {
        padding: 12px 14px !important;
        font-size: 13px !important;
        font-weight: 600;
        min-height: 54px !important;
        resize: vertical !important;
        font-family: inherit !important;
    }
    .input-score-premium:focus,
    .textarea-feedback-premium:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    /* Sticky Footer Bar Manajemen */
    .sticky-footer-action-bar {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-top: 36px !important;
        padding: 16px 28px !important;
        background: #1e293b !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3) !important;
        position: sticky !important;
        bottom: 20px !important;
        z-index: 99 !important;
    }
    .btn-cancel-premium {
        color: #94a3b8 !important;
        text-decoration: none !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: color 0.2s ease !important;
    }
    .btn-cancel-premium:hover {
        color: #ffffff !important;
    }
    .btn-save-all-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 14px 32px !important;
        border-radius: 12px !important;
        cursor: pointer !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        box-shadow: 0 4px 12px rgba(205, 0, 0, 0.3) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.2s ease !important;
    }
    .btn-save-all-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(205, 0, 0, 0.4) !important;
        filter: brightness(1.1) !important;
    }

    /* Responsivitas Layar Kecil (Tablet/Mobile) */
    @media (max-width: 640px) {
        .assessment-panel-grid {
            grid-template-columns: 1fr !important;
            gap: 14px !important;
        }
        .input-score-premium {
            text-align: left !important;
        }
        .sticky-footer-action-bar {
            flex-direction: column-reverse !important;
            gap: 14px !important;
            padding: 20px !important;
        }
        .btn-save-all-premium {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>