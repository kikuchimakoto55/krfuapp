<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TAuthoritykindsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('t_authoritykinds')->insert([
            [
                'authoritykinds_id' => 1,
                'authoritykindsname' => '管理者',
                'registration_date' => Carbon::now(),
                'update_date' => Carbon::now(),
                'del_flg' => 0,
            ],
            [
                'authoritykinds_id' => 2,
                'authoritykindsname' => '運営権限',
                'registration_date' => Carbon::now(),
                'update_date' => Carbon::now(),
                'del_flg' => 0,
            ],
            [
                'authoritykinds_id' => 3,
                'authoritykindsname' => '一般権限',
                'registration_date' => Carbon::now(),
                'update_date' => Carbon::now(),
                'del_flg' => 0,
            ],
            [
                'authoritykinds_id' => 4,
                'authoritykindsname' => '使用者権限',
                'registration_date' => Carbon::now(),
                'update_date' => Carbon::now(),
                'del_flg' => 0,
            ],
        ]);
    }
}
