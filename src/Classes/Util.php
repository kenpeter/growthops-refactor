<?php

namespace App;

class Util {
    
    public function getIntervalDateRange()
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