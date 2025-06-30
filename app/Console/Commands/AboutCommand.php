<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AboutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'about';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show application health status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Application is healthy!');
        return Command::SUCCESS;
    }
}
