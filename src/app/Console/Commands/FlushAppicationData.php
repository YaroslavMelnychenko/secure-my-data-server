<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FlushAppicationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'application:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all application data';

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
        Storage::disk(config('filesystems.cloud'))->deleteDirectory('secured');
        $this->info('Cloud data purged');

        Storage::disk('local')->deleteDirectory('session/keys');
        $this->info('Local data purged');

        \Artisan::call('migrate:fresh --force');
        $this->info('Database freshed');

        \Artisan::call('passport:install');
        $this->info('Passport installed');
    }
}
