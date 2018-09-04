<?php

namespace App;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\ApplicationExporter;
use App\Util;

class ExportApplicationsCommand extends Command
{
    const TIME_MODE = 'time';
    const INTERVAL_MODE = 'interval';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xyz:export-applications';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Retrieve and convert applications within a time period into a csv and upload it to cloud storage.";
 
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $mode = $this->option('mode');
        $inputPath = $this->option('inputPath'); // for local file
        $outputFolder = $this->option('outputFolder'); // for cloud folder
        $applicationExporter = new \ApplicationExporter();

        if($mode === self::TIME_MODE) {
            $startStr = $this->option('start');
            $endStr = $this->option('end');
        } else if($mode === self::INTERVAL_MODE) {
            $util = new Util();
            $arr = $util->getIntervalDateRange();
            $startStr = $arr[0];
            $endStr = $arr[1];
        } else {
            // mode not supported
        }

        $applicationExporter->export($startStr, $endStr, $inputPath, $outputFolder);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['mode', 'm', InputOption::VALUE_OPTIONAL, 'mode: time or interval'],
            ['inputPath', 'i', InputOption::VALUE_OPTIONAL, 'input file path'],
            ['outputFolder', 'o', InputOption::VALUE_OPTIONAL, 'output folder'],
            ['start', 's', InputOption::VALUE_OPTIONAL, 'Start time in string'],
            ['end', 'e', InputOption::VALUE_OPTIONAL, 'end time in string'],
        ];
    }

}