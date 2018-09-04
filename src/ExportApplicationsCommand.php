<?php

// cmd
use Illuminate\Console\Command;
// input
use Symfony\Component\Console\Input\InputOption;
// arg
use Symfony\Component\Console\Input\InputArgument;


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
        $mode = $this->argument('mode');
        if($mode === self::TIME_MODE) {
            $startStr = $this->option('start');
            $endStr = $this->option('end');


        } else if($mode === self::INTERVAL_MODE) {

        } else {
            // mode not supported
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'applications_filepath',
                InputArgument::REQUIRED,
                'Path to store applications CSV locally',
            ],

            [
                'mode',
                InputArgument::REQUIRED,
                'Mode can be interval, time and more can be added',
            ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['start', 's', InputOption::VALUE_REQUIRED, 'Start time in string'],
            ['end', 'e', InputOption::VALUE_REQUIRED, 'end time in string'],
        ];
    }

}