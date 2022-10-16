<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => 1,
            'date' => '2022-10-10',
            'started_at' => '08:00:00',
            'finished_at' => '20:00:00',
        ];
        Attendance::create($param);
        $param = [
            'user_id' => 2,
            'date' => '2022-10-10',
            'started_at' => '08:00:00',
            'finished_at' => '20:00:00',
        ];
        Attendance::create($param);
        $param = [
            'user_id' => 3,
            'date' => '2022-10-10',
            'started_at' => '08:00:00',
            'finished_at' => '20:00:00',
        ];
        Attendance::create($param);
    }
}
