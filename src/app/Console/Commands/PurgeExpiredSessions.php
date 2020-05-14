<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Session;
use Carbon\Carbon;

class PurgeExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge all expired sessions';

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
     * @return mixed
     */
    public function handle()
    {
        Session::purgeExpired();

        $time = Carbon::now();

        $this->info($time. ': Expired sessions purged');
    }
}
