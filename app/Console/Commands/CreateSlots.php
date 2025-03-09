<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Slot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slot:create 
                            {start_time : the start time in H:i format like 08:00}
                            {end_time : the end time in H:i format like 08:00} 
                            {start_date : the start date for schedules (y-m-d}
                            {end_date : the end date for schedules (y-m-d}
                            {interval=60 : the interval in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create time slots based on start time, end time and interval and date range';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime=$this->argument('start_time');
        $endTime=$this->argument('end_time');
        $interval=$this->argument('interval');
        $startDate=$this->argument('start_date');
        $endDate=$this->argument('end_date');

        $this->createTimeSlots($startTime,$endTime,$interval,$startDate,$endDate);
    }

    public function createTimeSlots(string $startTime,string $endTime,int $interval,string $startDate, string $endDate)
    {
        try{
            $start=Carbon::createFromFormat('H:i',$startTime);
            $end=Carbon::createFromFormat('H:i',$endTime);
            $startDate=Carbon::createFromFormat('Y-m-d',$startDate);
            $endDate=Carbon::createFromFormat('Y-m-d',$endDate);
        } catch (\Exception $e){
            $this->error('Invalid time or date format.');
            return;
        }

        if ($start>=$end){
            $this->error('Start time should be less than end time.');
            return ;
        }

        if ($startDate>=$endDate){
            $this->error('Start date should be less than end date');
        }

        while ($start<$end){
            $next=$start->copy()->addMinutes($interval);

            $slot=Slot::query()->create([
                'start'=>$start->format('H:i'),
                'end'=>$next->format('H:i')
            ]);

            $admins=User::query()->where('role','admin')->get();

            foreach ($admins as $admin){
                $currentDate=$startDate->copy();
                while ($currentDate<=$endDate){
                    Schedule::query()->create([
                        'admin_id'=>$admin->id,
                        'date'=>$currentDate->format('Y-m-d'),
                        'slot_id'=>$slot->id,
                        'isBooked'=>false,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                    $currentDate->addDay();
                }
            }

            $start=$next;
        }

        $this->info('Time slots and schedules created successfully.');
    }
}
