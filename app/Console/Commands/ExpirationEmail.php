<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SMSController;
use App\Models\Driver;
class ExpirationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiration:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $smsController = new SMSController();
        $drivers = Driver::where('status', 'active')->get();
        $smsController->sendExpirationEmail(15);
        foreach($drivers as $val)
        {
            try {
                $smsController->sendExpirationEmail($val->id);
            }catch(Exception $ex)
            {
                continue;
            }
            
        }

        return 0;
    }
}
