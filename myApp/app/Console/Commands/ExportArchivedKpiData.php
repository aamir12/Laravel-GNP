<?php

namespace App\Console\Commands;

use App\Classes\KpiExporter;
use Illuminate\Console\Command;

class ExportArchivedKpiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will export and remove all archived KPI data from the DB';

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
        KpiExporter::exportKpiArchive();
    }
}
