<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamMonitoringEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $scheduleId;
    public $studentId;
    public $actionType;
    public $message;

    /**
     * Membuat instance event baru.
     * * @param int $scheduleId ID Jadwal Ujian
     * @param int $studentId ID Siswa yang bersangkutan
     * @param string $actionType Jenis Aksi ('PELANGGARAN', 'RESET_AKSES', atau 'FORCE_SUBMIT')
     * @param string $message Pesan tambahan opsional
     */
    public function __construct($scheduleId, $studentId, $actionType, $message = '')
    {
        $this->scheduleId = $scheduleId;
        $this->studentId = $studentId;
        $this->actionType = $actionType;
        $this->message = $message;
    }

    /**
     * Menentukan nama channel (saluran frekuensi umum/Public) 
     * agar mudah didengarkan oleh React Native Expo & JavaScript Web tanpa ribet auth token channel.
     */
    public function broadcastOn(): array
    {
        return [
          new Channel('exam-monitoring.' . $this->scheduleId),
        ];
    }

    /**
     * Nama Event khusus yang akan dibaca oleh pemicu listener client (Web & Mobile)
     */
    public function broadcastAs(): string
    {
        return 'ExamAktivitas';
    }
}