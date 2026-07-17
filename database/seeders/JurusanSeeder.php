<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        Kelas::query()->delete();
        Jurusan::query()->delete();

        $rpl = Jurusan::create([
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Program keahlian pengembangan perangkat lunak, aplikasi web, dan mobile.',
            'is_active' => true,
        ]);

        $dkv = Jurusan::create([
            'kode' => 'DKV',
            'nama' => 'Desain Komunikasi Visual',
            'deskripsi' => 'Program keahlian desain grafis, multimedia, animasi, dan produksi konten visual.',
            'is_active' => true,
        ]);

        $akl = Jurusan::create([
            'kode' => 'AKL',
            'nama' => 'Akuntansi & Keuangan Lembaga',
            'deskripsi' => 'Program keahlian akuntansi, perpajakan, pengelolaan keuangan, dan administrasi perkantoran.',
            'is_active' => true,
        ]);

        $bd = Jurusan::create([
            'kode' => 'BD',
            'nama' => 'Bisnis Digital',
            'deskripsi' => 'Program keahlian bisnis berbasis digital, e-commerce, dan pemasaran digital.',
            'is_active' => true,
        ]);

        $all = [$rpl, $dkv, $akl, $bd];

        foreach ($all as $j) {
            foreach ([10, 11, 12] as $tingkat) {
                $j->kelas()->create([
                    'tingkat' => $tingkat,
                    'nama' => $j->kode . ' 1',
                    'is_active' => true,
                ]);

                if ($tingkat === 10) {
                    $j->kelas()->create([
                        'tingkat' => $tingkat,
                        'nama' => $j->kode . ' 2',
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Jurusan & Kelas berhasil di-seed!');
    }
}

