<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(AdminSeeder::class);

        $startDate=Carbon::today()->format('Y-m-d');
        $endDate=Carbon::today()->addMonth(3)->format('Y-m-d');

        Artisan::call('slot:create',[
            'start_time'=>'08:00',
            'end_time'=>'20:00',
            'start_date'=>$startDate,
            'end_date'=>$endDate,
            'interval'=>60,
        ]);
    }
}
