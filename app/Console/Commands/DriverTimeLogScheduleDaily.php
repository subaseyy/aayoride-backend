<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Entities\DriverDetail;

class DriverTimeLogScheduleDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'driver-timelog:inserted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset driver details and insert records into driver_time_logs for the current day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $driverDetails = DriverDetail::get();

        DB::transaction(function () use ($driverDetails) {
            foreach ($driverDetails as $detail) {
                DB::table('driver_time_logs')->insert([
                    'driver_id' => $detail->user_id,
                    'online_time' => $detail->online_time,
                    'on_driving_time' => $detail->on_driving_time,
                    'idle_time' => $detail->idle_time,
                    'date' => date('Y-m-d')
                ]);

                // Reset the values in the model
                $detail->update([
                    'online_time' => 0,
                    'online' => null,
                    'offline' => null,
                    'accepted' => null,
                    'completed' => null,
                    'start_driving' => null,
                    'on_driving_time' => 0,
                    'idle_time' => 0,
                ]);
            }
        });
        $this->info('Driver details have been reset and records have been inserted into driver_time_logs.');
    }
}
