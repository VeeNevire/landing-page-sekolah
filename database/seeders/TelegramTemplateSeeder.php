<?php

namespace Database\Seeders;

use App\Models\TelegramBot;
use App\Models\TelegramTemplate;
use Illuminate\Database\Seeder;

class TelegramTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $bot = TelegramBot::where('is_active', true)->first();
        if (! $bot) {
            $this->command?->warn('Belum ada bot Telegram aktif. Template contoh tidak dibuat.');
            return;
        }

        $templates = [
            [
                'name' => 'Informasi Nilai Siswa',
                'report_type' => 'grade',
                'body' => "🔔 INFORMASI NILAI SISWA\n\nHalo Bapak/Ibu,\n\nNilai {{nama_siswa}} untuk mata pelajaran {{mata_pelajaran}} pada penilaian {{penilaian}} adalah {{nilai}} dari {{nilai_maksimal}}.\n\nStatus: {{status}}\nGuru: {{guru}}\nTanggal: {{tanggal}}\n\nLihat detail di InvestaSchool:\n{{link_detail}}\n\nSalam,\n{{nama_sekolah}}",
            ],
            [
                'name' => 'Pengumuman Rencana Ujian',
                'report_type' => 'exam_plan',
                'body' => "📚 RENCANA UJIAN\n\nHalo Bapak/Ibu,\n\nInformasi rencana ujian untuk {{nama_siswa}} telah diterbitkan.\n\nMata Pelajaran: {{mata_pelajaran}}\nTanggal: {{tanggal}}\n\nDetail lengkap:\n{{link_detail}}\n\n{{nama_sekolah}}",
            ],
            [
                'name' => 'Rekap Kehadiran Siswa',
                'report_type' => 'attendance',
                'body' => "📋 INFORMASI ABSENSI\n\nHalo Bapak/Ibu,\n\nRekap absensi {{nama_siswa}} telah diperbarui.\n\nTanggal: {{tanggal}}\nStatus: {{status}}\nCatatan: {{catatan}}\n\nLihat detail:\n{{link_detail}}\n\n{{nama_sekolah}}",
            ],
        ];

        foreach ($templates as $template) {
            TelegramTemplate::updateOrCreate(
                ['telegram_bot_id' => $bot->id, 'name' => $template['name']],
                $template + ['is_active' => true]
            );
        }

        $this->command?->info('3 template bot contoh berhasil dibuat untuk '.$bot->name.'.');
    }
}
