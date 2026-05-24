@extends('layouts.app')

@section('title', 'Kelola Soal')

@section('content')
{{-- Navigasi Kembali --}}
<div class="navigation-wrapper-premium">
    <a href="{{ route('guru.schedules.index') }}" class="btn-back-premium">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
    </a>
</div>

{{-- Alert Notifikasi Premium --}}
@if(session('success'))
    <div class="alert-premium alert-success-premium animate-fade-in">
        <div class="alert-icon-premium"><i class="fas fa-check-circle"></i></div>
        <div class="alert-text-premium">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert-premium alert-danger-premium animate-fade-in">
        <div class="alert-icon-premium"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="alert-text-premium">{{ session('error') }}</div>
    </div>
@endif

{{-- Ringkasan Sesi Jadwal (Hero Box) --}}
<div class="content-box-premium hero-session-premium">
    <div class="hero-layout-premium">
        <div class="hero-details-premium">
            <span class="exam-tag-premium">
                <i class="fas fa-graduation-cap"></i> {{ $schedule->examType->name ?? 'Ujian' }}
            </span>
            <h3 class="subject-title-premium">{{ $schedule->subject->nama_mapel ?? $schedule->subject->name }}</h3>
            <p class="classroom-subtitle-premium">
                Kelas: <strong>{{ $schedule->classroom->nama_kelas ?? $schedule->classroom->name }}</strong>
            </p>
        </div>
        <div class="hero-actions-premium">
            <button onclick="toggleModal('modalCopySoal')" class="btn-action-dark-premium">
                <i class="fas fa-copy"></i> Salin Soal
            </button>
            <button onclick="toggleModal('modalAddSoal')" class="btn-action-danger-premium">
                <i class="fas fa-plus"></i> Tambah Soal
            </button>
        </div>
    </div>
</div>

{{-- Blok Tabel Daftar Soal --}}
<div class="content-box-premium">
    <h4 class="table-title-premium">
        <i class="fas fa-list-ol"></i> Daftar Soal Sesi Ini ({{ count($questions) }})
    </h4>
    <div class="table-responsive-premium">
        <table class="table-premium">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">NO</th>
                    <th>PERTANYAAN</th>
                    <th style="width: 120px;">TIPE</th>
                    <th style="width: 140px;">PEMBUAT</th>
                    <th class="text-center" style="width: 160px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $index => $q)
                <tr>
                    <td class="text-center font-bold-premium color-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="question-preview-text">{{ Str::limit(strip_tags($q->question_text), 100) }}</div>
                        @if($q->question_image) 
                            <span class="image-indicator-premium">
                                <i class="fas fa-image"></i> Ada Gambar
                            </span> 
                        @endif
                    </td>
                    <td>
                        <span class="badge-type-premium type-{{ strtolower($q->type) }}">
                            {{ $q->type == 'pg' ? 'Pilihan Ganda' : 'Essay / Uraian' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-creator-premium {{ $q->user_id == auth()->id() ? 'creator-me' : 'creator-other' }}">
                            <i class="fas {{ $q->user_id == auth()->id() ? 'fa-user' : 'fa-user-shield' }}"></i>
                            {{ $q->user_id == auth()->id() ? 'Saya' : 'Admin/Lain' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons-group-premium">
                            <button type="button" onclick='openEditModal({!! $q->toJson() !!})' class="btn-table-edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('guru.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn-table-delete">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="table-empty-state">
                        <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                        <div class="empty-state-text">Belum ada daftar soal untuk jadwal pelaksanaan ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Copy Soal --}}
<div id="modalCopySoal" class="modal-backdrop-premium" style="display: none;">
    <div class="modal-box-premium max-w-md">
        <span onclick="toggleModal('modalCopySoal')" class="modal-close-premium">&times;</span>
        <h3 class="modal-title-premium"><i class="fas fa-clone"></i> Salin Struktur Soal</h3>
        <p class="modal-description-premium">Pilih paket jadwal ujian yang sudah memiliki bank soal untuk diduplikasi langsung menuju sesi ini.</p>
        
        <form action="{{ route('guru.questions.copy', $schedule->id) }}" method="POST" class="modal-form-premium">
            @csrf
            <div class="form-group-premium">
                <label class="form-label-premium">Jadwal Referensi Sumber</label>
                <select name="from_schedule_id" class="form-select-premium" required>
                    <option value="">-- Pilih Jadwal Sumber Data --</option>
                    @foreach($otherSchedules as $other)
                        <option value="{{ $other->id }}">
                            {{ $other->subject->nama_mapel ?? $other->subject->name }} - {{ $other->classroom->nama_kelas ?? $other->classroom->name }} ({{ date('d M Y', strtotime($other->tanggal_ujian)) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-submit-copy-premium">
                <i class="fas fa-exchange-alt"></i> Mulai Duplikasi Soal
            </button>
        </form>
    </div>
</div>

{{-- Modal Add Soal --}}
<div id="modalAddSoal" class="modal-backdrop-premium" style="display: none;">
    <div class="modal-box-premium max-w-xl animate-slide-up">
        <span onclick="toggleModal('modalAddSoal')" class="modal-close-premium">&times;</span>
        <h3 class="modal-title-premium"><i class="fas fa-plus-circle"></i> Buat Butir Soal Baru</h3>
        <hr class="modal-divider-premium">
        @include('guru.questions.create')
    </div>
</div>

{{-- Modal Edit Soal --}}
<div id="modalEditSoal" class="modal-backdrop-premium" style="display: none;">
    <div class="modal-box-premium max-w-xl animate-slide-up">
        <span onclick="toggleModal('modalEditSoal')" class="modal-close-premium">&times;</span>
        <h3 class="modal-title-premium"><i class="fas fa-edit"></i> Perbarui Konfigurasi Soal</h3>
        <hr class="modal-divider-premium">
        @include('guru.questions.edit')
    </div>
</div>

<script>
    /**
     * Berfungsi membuka atau menutup modal serta mengunci scroll latar belakang
     */
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            const isHidden = modal.style.display === 'none';
            modal.style.display = isHidden ? 'block' : 'none';
            document.body.style.overflow = isHidden ? 'hidden' : 'auto';
        }
    }

    /**
     * Memasukkan data butir soal terpilih ke dalam struktur form edit secara dinamis
     */
    function openEditModal(data) {
        const form = document.getElementById('formEditSoal');
        if (!form) return;

        // Mengarahkan route URL update data soal secara dinamis
        form.action = `/guru/questions/${data.id}`;

        document.getElementById('edit_type').value = data.type;
        document.getElementById('edit_question_text').value = data.question_text;

        if (data.type === 'pg') {
            document.getElementById('edit_option_a').value = data.option_a || '';
            document.getElementById('edit_option_b').value = data.option_b || '';
            document.getElementById('edit_option_c').value = data.option_