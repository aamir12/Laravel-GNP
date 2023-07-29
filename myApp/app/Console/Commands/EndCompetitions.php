<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\CompetitionScheduler;

class EndCompetitions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competitions:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will end all competitions with an end_date of the current time (this minute).';

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
        CompetitionScheduler::endCompetitions();
    }
}
