<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TLicensekindsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = date("Y-m-d H:i:s");
        $data = [
            ['licensekinds_id' => 1,
            '' => $i,
            'licensekindsname' => "hogehoge",
            'registration_date' => $now,
            'update_date' => $now,
            'del_flg' => 0,
            ],
            ['licensekinds_id' => 2,
            '' => $i,
            'licensekindsname' => "hogehoge",
            'registration_date' => $now,
            'update_date' => $now,
            'del_flg' => 0,
            ],
            ['licensekinds_id' => 3,
            '' => $i,
            'licensekindsname' => "hogehoge",
            'registration_date' => $now,
            'update_date' => $now,
            'del_flg' => 0,
            ],
            ];
            
    }
}
