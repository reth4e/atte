<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rest;


class RestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'attendance_id' => 2,
            'started_at' => '12:00:00',
            'finished_at' => '13:00:00',
        ];
        Rest::create($param);

        $param = [
            'attendance_id' => 3,
            'started_at' => '12:00:00',
            'finished_at' => '13:00:00',
        ];
        Rest::create($param);
        
        $param = [
            'attendance_id' => 3,
            'started_at' => '18:00:00',
            'finished_at' => '19:00:00',
        ];
        Rest::create($param);
    }
}
