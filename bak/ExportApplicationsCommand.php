<?php

// cmd
use Illuminate\Console\Command;
// input
use Symfony\Component\Console\Input\InputOption;
// arg
use Symfony\Component\Console\Input\InputArgument;

// export cmd, extend
class ExportApplicationsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */

    // name of cmd
    protected $name = 'xyz:export-applications';

    /**
     * The console command description.
     *
     * @var string
     */

    // desc
    protected $description = 'Retrieve and convert applications within a time period into a csv and upload it to cloud storage.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // 3 times a day
        // Interval application export, this type of export runs 3 times a day with a different format than the other exports
        if ($this->option('interval')) {
            $this->exportIntervalApplications();

            return;
        } 

        // 2 times?
        // Follow up application export, similar to daily application export but different date range
        if ($this->option('followup')) {
            $this->exportFollowUpApplications();

            return;
        } 

        // 1 time?
        // Daily application export
        $this->exportApplications();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        // input_path, input arg, output_path
        return [
            [
                'applications_filepath',
                InputArgument::REQUIRED,
                'Path to store applications CSV locally',
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
            // 2 times?
            [
                'followup',
                'followup',
                InputOption::VALUE_NONE,
                'Set this option to export follow up application',
            ],

            // 3 times?
            [
                'interval',
                'interval',
                InputOption::VALUE_NONE,
                'Set this option to export interval application ',
            ],
        ];
    }

    /**
     * Export applications record to csv file and upload the csv file to Cloud Storage
     *
     * @return void
     */
    private function exportApplications()
    {
        // yesterday, with 1 sec, to today
        $reportStartDate = '-1 day 08:00:01';
        // today
        $reportEndDate = 'now 08:00:00';

        // applications in 1 day
        // this is model
        // Query the applications, let's assume that Application extends Laravel's Eloquent Model class
        $applications = Application::whereBetween('created_at', [
            date('Y-m-d H:i:s', strtotime($reportStartDate)),
            date('Y-m-d H:i:s', strtotime($reportEndDate))
        ])->get();

        // csv header
        // Insert first row with the header
        $csvArray = [array_keys($this->getApplicationsLineTemplate())];

        // loop app
        foreach ($applications as $application) {
            // Insert subsequent row with applications data
            $csvArray[] = array_values($this->convertApplicationToCsvArray($application));
        }

        // Convert csvArray into string
        $csvString = $this->convertToCsv($csvArray);

        // Get the path where the csv should be written into
        // local filename
        $filename = $this->argument('applications_filepath');

        // Write the csv
        file_put_contents($filename, $csvString);

        // cloud folder
        $folder = 'Applications - Follow Up/';

        // Upload csv to cloud storage
        $this->uploadToCloudStorage($filename, $folder);

        unlink($filename);
    }

    /**
     * Export follow up applications record to csv file and upload the csv file to Cloud Storage
     *
     * @return void
     */
    // similar
    private function exportApplicationsForFollowup()
    {
        // last 8 weeks
        $reportStartDate = '-10 weeks 08:00:01';
        $reportEndDate = '-2 weeks 08:00:00';

        // Query the applications for follow up, let's assume that Application extends Laravel's Eloquent Model class
        $applications = Application::whereBetween('created_at', [
            date('Y-m-d H:i:s', strtotime($reportStartDate)),
            date('Y-m-d H:i:s', strtotime($reportEndDate))
        ])->get();

        // csv header
        // Insert first row with the header
        $csvArray = [array_keys($this->getApplicationsLineTemplate())];

        // loop
        foreach ($applications as $application) {
            // Insert subsequent row with applications data
            // get values
            $csvArray[] = array_values($this->convertApplicationToCsvArray($application));
        }

        // Convert csvArray into string
        $csvString = $this->convertToCsv($csvArray);

        // Get the path where the csv should be written into
        $filename = $this->$this->argument('applications_filepath');

        // Write the csv
        file_put_contents($filename, $csvString);

        $folder = 'Applications - Follow Up/';

        // Upload csv to cloud storage
        $this->uploadToCloudStorage($filename, $folder);

        unlink($filename);
    }

    /**
     * Export applications at an interval
     *
     * @return void
     */
    private function exportIntervalApplications()
    {
        // Query the applications, let's assume that Application extends Laravel's Eloquent Model class
        $applications = Application::whereBetween('created_at', $this->getIntervalDateRange())
            ->get();

        // Insert first row with the header
        $csvArray = [array_keys($this->getIntervalApplicationsLineTemplate())];

        foreach ($applications as $application) {
            // Insert subsequent row with applications data
            $csvArray[] = array_values($this->convertIntervalApplicationToCsvArray($application));
        }

        // svn string all
        // Convert csvArray into string
        $csvString = $this->convertToCsv($csvArray);

        // get file names
        // Get the path where the csv should be written into
        $filename = $this->$this->argument('applications_filepath');

        // csv_str to app_filepath
        // Write the csv
        file_put_contents($filename, $csvString);

        // apps - inverval
        $folder = 'Applications - Interval/';

        // to cloud
        // Upload csv to cloud storage
        $this->uploadToCloudStorage($filename, $folder);

        // delete locally
        unlink($filename);
    }

    /**
     * Get the template for Applications file
     *
     * @return array
     */
    // data is person model
    private function getApplicationsLineTemplate()
    {
        $template = [];

        $template['id'] = '';
        $template['title'] = '';
        $template['firstname'] = '';
        $template['middlename'] = '';
        $template['lastname'] = '';
        $template['email'] = '';
        $template['phone'] = '';

        return $template;
    }

    /**
     * Process each application and map the fields to template
     *
     * @param $application
     * @return array
     */
    // with key, destill data, return
    private function convertApplicationToCsvArray($application)
    {
        $record = $this->getApplicationsLineTemplate();

        $record['id'] = $application->id;
        $record['title'] = $application->title;
        $record['firstname'] = Formatter::name($application->firstname);
        $record['middlename'] = Formatter::name($application->middlename);
        $record['lastname'] = Formatter::name($application->lastname);
        $record['email'] = $application->email;
        $record['phone'] = Formatter::phone($application->phone);

        return $record;
    }

    /**
     * Get the template for interval applications file
     *
     * @return array
     */
    private function getIntervalApplicationsLineTemplate()
    {
        $template = [];

        $template['Member ID'] = '';
        $template['Family Name'] = '';
        $template['Given Names'] = '';
        $template['Campaign'] = '';

        return $template;
    }

    /**
     * Process each application record to the correct template
     *
     * @param $application
     * @return array
     */
    // mem id, name, campaign
    private function convertIntervalApplicationToCsvArray($application)
    {
        $record = $this->getIntervalApplicationsLineTemplate();

        $record['Member ID'] = $application->id;
        $record['Family Name'] = Formatter::name($application->lastname);
        $record['Given Names'] = Formatter::name($application->firstname);
        $record['Campaign'] = $application->campaign;

        return $record;
    }

    /**
     * Convert the given array into csv string
     *
     * @param array $csvArray
     * @return string
     */
    // convert to csv
    private function convertToCsv(array $csvArray)
    {
        $lines = [];

        // one line values
        foreach ($csvArray as $values) {
            $line = [];

            // one line
            foreach ($values as $value) {
                $line[] = '"' . trim($value) . '"';
            }

            $lines[] = implode(',', $line);
        }

        return implode("\n", $lines);
    }

    /**
     * Upload the given file to cloud storage
     *
     * @param $filename
     * @param $folder
     */
    private function uploadToCloudStorage($filename, $folder)
    {
        // Pseudocode, you don't need to implement this
        print_r('Upload the given file to the cloud in the specified folder');
    }

    /**
     * Get the date range based on current time
     *
     * @return array 
     */
    private function getIntervalDateRange()
    {
        // Interval export date range is determined dynamically based on the time when the export runs
        // 1. If export runs between 08:00 to 13:00; interval export will retrieve records from 16:00:01 (day before) to 08:00:00 (today)
        // 2. If export runs between 13:00 to 16:00; interval export will retrieve records from 08:00:01 to 13:00:00
        // 3. If export runs between 16:00 to 08:00; interval export will retrieve records from 13:00:01 to 16:00:00
        $currentTime = date('H:i:s');

        if ($currentTime >= '08:00:00' && $currentTime <= '13:00:00') {
            // First time slot
            return [
                date('Y-m-d H:i:s', strtotime('-1 day 16:00:01')),
                date('Y-m-d H:i:s', strtotime('now 08:00:00')),
            ];
        } elseif ($currentTime >= '13:00:01' && $currentTime <= '16:00:00') {
            // Second time slot
            return [
                date('Y-m-d H:i:s', strtotime('now 08:00:01')),
                date('Y-m-d H:i:s', strtotime('now 13:00:00'))
            ];
        } else {
            // Third time slot
            return [
                date('Y-m-d H:i:s', strtotime('now 13:00:01')),
                date('Y-m-d H:i:s', strtotime('now 16:00:00'))
            ];
        }
    }
}