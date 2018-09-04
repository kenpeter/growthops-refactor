<?php

namespace App;

use App\Fomatter;

class ApplicationExpoter {


    public function export($startStr, $endStr, $inputPath, $outputFolder) {
        // variable interfacing
        $reportStartDate = $startStr;
        $reportEndDate = $endStr;

        // Model
        $applications = Application::whereBetween('created_at', [
            date('Y-m-d H:i:s', strtotime($reportStartDate)),
            date('Y-m-d H:i:s', strtotime($reportEndDate))
        ])->get();

        // csv header
        $csvArray = [array_keys($this->getApplicationsLineTemplate())];

        foreach ($applications as $application) {
            // csv data
            $csvArray[] = array_values($this->convertApplicationToCsvArray($application));
        }

        // Convert csvArray into string
        $csvString = $this->convertToCsv($csvArray);

        // Get the path where the csv should be written into
        $filename = $inputPath;

        // Write the csv
        file_put_contents($filename, $csvString);

        // Upload csv to cloud storage
        $this->uploadToCloudStorage($filename, $outputFolder);

        unlink($filename);
    }

    /**
     * Get the template for Applications file
     *
     * @return array
     */
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
     * Convert the given array into csv string
     *
     * @param array $csvArray
     * @return string
     */
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
}