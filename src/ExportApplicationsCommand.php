<?php

// cmd
use Illuminate\Console\Command;
// input
use Symfony\Component\Console\Input\InputOption;
// arg
use Symfony\Component\Console\Input\InputArgument;


class ExportApplicationsCommand extends Command
{
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
        $this->line('Welcome to the user generator.');
    }


}