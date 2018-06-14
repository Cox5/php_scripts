<h3>Timesheet upload form</h3>
<?php

// Note: before uploading csv file, make sure that upload folder is created in advance in the same folder where the .php file is at

// Step 1. Connection with database

$dbhost = 'localhost';
$username = 'root';
$password = '';
$dbName = 'timesheet_danny';

// Create connection
$conn = mysqli_connect($dbhost, $username, $password, $dbName);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
 else {
	 echo "Connection to database successfull!<br />\n";
 }

// Step 2. Simple upload form with HTML
?>

<table width="600">
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
    <tr>
    <br>
        <td width="20%">Select file</td>
        <td width="80%"><input type="file" name="file" id="file" /></td>
    </tr>
    <tr>
        <td>Upload</td>
        <td><input type="submit" name="submit" /></td>
    </tr>
</form>
</table>
                    
<?php  

echo "<br />\n\n";

if ( isset($_POST["submit"]) ) {

   if ( isset($_FILES["file"])) {

            //if there was an error uploading the file
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

        }
        else {
            //Print file details , comment if not necessary 
             echo "Upload: " . $_FILES["file"]["name"] . "<br />";
             echo "Type: " . $_FILES["file"]["type"] . "<br />";
             echo "Size: " . round(($_FILES["file"]["size"] / 1024),3) . " Kb<br />";
             echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

                 //if file already exists
             if (file_exists("upload/" . $_FILES["file"]["name"])) {
            echo $_FILES["file"]["name"] . " already exists. ";
             }
             else {
                    //Store file in directory "upload" with the name of "uploaded_file.csv"
            $storagename = "uploaded_file.csv";
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);
            echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br />";
            }
        }
     } else {
             echo "No file selected <br />";
     }
}

//  Step 3. Parsing CSV file and reading through records and INSERT into database

// Rounds up to nearest 6 minutes ( eg. 31m becomes 36m)
function roundUpToAny($n,$x=6) {
    return round(($n+$x/2)/$x)*$x;
}

$row = 1;
$crn = NULL;
$rate = 1;

if(($handle = fopen("upload/" . $storagename , "r")) !== FALSE) {
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
        $num = count($data);
        $row++;
        
        if ($row > 2) {         // first row shows only column names so it can be skipped, at least for this csv file
            // 0 -> activity ; 1-> project; 2-> workers; 3-> duration; 4-> time  
            echo "<p> $num fields in line $row: <br /><p>\n";

            $hms = explode(":", $data[3]);          // divide duration in two parts (hours and minutes) with : delimiter
            $minutes = $hms[0] * 60 + $hms[1];      // convert all to minutes
            if ($minutes <= 15) {
                $duration = 0.25;
            }
            else {
                if($minutes % 6 != 0) {
                    $roundToSix = roundUptoAny($minutes,6);
                    $duration = round(($roundToSix / 60), 1);
                } else {
                    $duration = round(($minutes / 60), 1);
                }
            }

            $time = explode("–", $data[4]);         // divide time in two sections and we only need the first section to convert to YYYY-MM-DD           
            $dateFormat = date('Y-m-d', strtotime($time[0]));
            
            $activity = $data[0];                   // place activity into its variable for easier INSERT later on..
            
            if($data[1] == "Capital Guardians") {
                $crn = 113696;
            }
            else if($data[1] == "BF: PSI") {
                $crn = 113027;
            }
            else if($data[1] == "Deliver IT") {
                $crn = 113084;
            }
            else if($data[1] == "National Visas") {
                $crn = 113159;
            }
            else if($data[1] == "Techinfo") {
                $crn = 109769;
            }
            else if($data[1] == "BF: Rhinomed") {
                $crn = 113027;
            }
            else {
                // do something
            }
            
 /*                       
            echo $data[0] . "<br />\n";
            echo $data[1] . "<br />\n";
            echo $data[2] . "<br />\n";
            echo $data[3] . "<br />\n";
            echo $duration . "<br />\n";            // duration || data[3]
            echo $dateFormat . "<br />\n";          // time || data[4]
  */          

            $sql = 'INSERT INTO timesheet (crn, date, description, rate, hours) VALUES (  "'.
                $crn.'" ,  "'.
                $dateFormat['date'].'" , "'.
                $activity['description'].'" , "'.
                $rate.'" , "'.
                $duration['hours'].'" );"';
            
            if (mysqli_query($conn, $sql)) {
                echo "Records inserted successfully!";
            } else {
                echo "Error: " . $sql . "<br> " . mysqli_error($conn);
            }

        } 
    }
    fclose($handle);
}

?>