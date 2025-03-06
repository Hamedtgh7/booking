<?php

namespace App\Console\Commands;

use App\Models\Slot;
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
                            {start_time : the start time in H:i format}
                            {end_time : the end time in H:i format} 
                            {interval=60 : the interval in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create time slots based on start time, end time and interval';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime=$this->argument('start_time');
        $endTime=$this->argument('end_time');
        $interval=$this->argument('interval');

        $this->createTimeSlots($startTime,$endTime,$interval);
    }

    public function createTimeSlots(string $startTime,string $endTime,int $interval)
    {
        try{
            $start=Carbon::createFromFormat('H:i',$startTime);
            $end=Carbon::createFromFormat('H:i',$endTime);
        } catch (\Exception $e){
            $this->error('Invalid time format.');
            return;
        }

        if ($start>=$end){
            $this->error('Start time should be less than end time.');
            return ;
        }

        while ($start<$end){
            $next=$start->copy()->addMinutes($interval);

            Slot::query()->create([
                'start'=>$start->format('H:i'),
                'end'=>$next->format('H:i')
            ]);

            $start=$next;
        }

        $this->info('Time slots created successfully.');
    }
}
