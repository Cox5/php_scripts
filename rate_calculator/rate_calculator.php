<!DOCTYPE html>

<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body>
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            text-align: center;
            font-family: "Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", sans-serif;
        }
        form {
            display: inline-block;
            text-align: center;
        }
        input[type=text], select {
            width=100%; 
            
        }
        
        input[type=submit] {
            text-align: center;
            color: white;
            border-radius: 2px;
            cursor: pointer;
            background-color: #4CAF50;
            margin: 4px 0;
            padding: 5px 5px;
            
        }
        
        input[type=submit]:hover {
            background-color: #45a049;
        }
        
        h2,p {
            text-align: center;
        }
        
    </style>
    
    <h2>Rate calculator</h2>
    
    <form  method="get">
    Unesi sate: <input type="text" name="hours">
	</br>
	Unesi minute: <input type="text" name="minutes">
	</br>
	Satnica u $: <input type="text" name="rate">
	</br>
    <input type="submit">
    </form>

<?php
    
    if(empty($_GET)) {
		$hours = 0;
		$minutes = 0;
		$rate = 0;
    } else {
        $hours = $_GET['hours'];
		$minutes = $_GET['minutes'];
		$rate = $_GET['rate'];
		
		$decimal_hours = round($hours + $minutes / 60, 2);
	
		$profit = $decimal_hours * $rate;
    }



?>
    
        <?php
            if (!empty($_GET)) {
				echo "<br>";
                echo "Zarada za " . "<b>" . $decimal_hours . "</b>" . " sati je  " . "<b>" . $profit . "</b>" . " USD";
            }
        ?>
    </p>
    
</body>
</html>