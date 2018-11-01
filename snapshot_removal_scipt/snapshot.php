<?php

header("Content-type: text/plain");

// Define the filename here
define("FILENAME", "snapshot_new");

date_default_timezone_set("Australia/Melbourne");

// Define resulting arrays for each dataset group
$datasets_past_14 = array();
$datasets_9_13 = array();
$datasets_2_8 = array();
$datasets_current_past = array();
$datasets_for_keep = array();

// Skip the first row from snapshot file 
$flag = true;

$myfile = fopen(FILENAME, "r") or die("Unable to open file!");
while (!feof($myfile)) {

    // Get file contents 
    $fileContents = fgets($myfile);
    
    // skips the first row from file 
    if ($flag) { $flag = false; continue; }

    # Get dataset and timestamp from each row
    # line[0] = 'z/rosella/secure.clari.net.au@2018-10-14-10h46'
    $line = explode('  ', $fileContents);
    
    // Get dataset and timestamp
    $contents = explode('@', $fileContents);
    // Extract timestamp 
    if (ISSET($contents[1])) {
        $timestamp = explode('  ', $contents[1]);
    }

    // replace 'h' with '-' from time format
    $tmp = str_replace('h', '-', $timestamp);
    
    // assign dataset and timestamp to local variables
    $dataset = $contents[0];    // e.g. 'z/hawk/basejail'
    $timestampFull = $timestamp[0];  // 
    $timestampWithoutTime = substr($timestamp[0], 0, -6);

    // get year-week formats
    $dayFormat = date('Y-W-D', strtotime($timestampWithoutTime));
    $dateFormat = date('Y-W', strtotime($timestampWithoutTime));

    $weekNumber = date('W', strtotime($timestampWithoutTime));
    $currentWeekNumber = date('W');
    $differenceWeekNumber = $currentWeekNumber - $weekNumber;


    $dateTimeFormat = str_replace('h', '-', $timestampFull);

    if ($differenceWeekNumber >= 0 && $differenceWeekNumber <= 1) {
        $datasets_current_past[$dataset][$dayFormat][] = $line[0];
    }
    if ($differenceWeekNumber >= 2 && $differenceWeekNumber <= 8) {
        $datasets_2_8[$dataset][$dateFormat][] = $line[0];
    } 
    if ($differenceWeekNumber >= 9 && $differenceWeekNumber <= 13) {
        if (($weekNumber % 2) == 0) {
            // datasets for removal (even week numbers)
            $datasets_9_13[$dateFormat][] = $line[0];
        } else {
            $datasets_for_keep[$dataset][$dateFormat][] = $line[0];
        }
    }
    if ($differenceWeekNumber >= 14) {
        $datasets_past_14[$dateFormat][] = $line[0];
    } 

}

// Past 14 weeks output
foreach ($datasets_past_14 as $value) {
    foreach ($value as $key => $data) {
        printf("zfs destroy %s\n", $data);
    }
}

// Past 9 - 13 weeks output
foreach ($datasets_9_13 as $value){
    foreach ($value as $key => $data) {
        printf("zfs destroy %s\n", $data);
    }
}

// Past 2 to 8 weeks output
foreach ($datasets_2_8 as $key => $value) { // z/jails/dove.clari.net.au'
    foreach ($value as $k => $val) {    // '2018-42'
        $arrSize = count($val);
        if ($arrSize > 1) {  // for multiple elements in $val array, find the most recent one and keep it, remove the rest
            foreach ($val as $i => $v) {  // 'z/jails/dove.clari.net.au@2018-10-16-10h45'
                if ($i == $arrSize - 1) {   // if index number matches the size of the array, that's the most recent snapshot
                    $datasets_for_keep[$dataset][$dateFormat][] = $v;
                } else {
                    printf("zfs destroy %s\n", $v);
                }
            }
        } else {
            foreach ($val as $v) {
                // only one element in array, do not delete that snapshot
                $datasets_for_keep[$dataset][$dateFormat][] = $v;
            }
        }
    }
}

//Current week and past week output
foreach ($datasets_current_past as $key => $value) {
    foreach ($value as $k => $val) {
        $arraySize = count($val);
        if ($arraySize > 1 ) {
            foreach ($val as $i => $v) {
                if ($i == $arraySize - 1) {
                    // keep it
                    $datasets_for_keep[$dataset][$dateFormat][] = $v;
                } else {
                    printf("zfs destroy %s\n", $v);
                }
            }
        } else {
            foreach ($val as $v) {
                // only one element in array, do not delete that snapshot
                $datasets_for_keep[$dataset][$dateFormat][] = $v;
            }
        }
    }
}

// Print out snapshots that should not be removed
echo "KEEP LIST:\n";
foreach ($datasets_for_keep as $key => $value) {
    foreach ($value as $k => $val) {
        foreach ($val as $i => $v) {
            printf("%s\n", $v);
        }
    }
}

fclose($myfile);
