<!DOCTYPE html>

<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<title>Payoneer ATM calculator</title>
	<link rel="icon" href="favicon.ico">
	
		
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

		@media only screen and (min-width: 600px) {
			#desc {
				width: 40%;
				margin: 0 auto;
			}
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

		.footer {
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100%;
			text-align: center;
			font-size: 12px;
		}
		
	</style>
    
    <h2>Payoneer ATM kalkulator</h2>
	<P id="desc">Jednostavan kalkulator koji računa koliko novca je moguće podići na bankomatu nakon Payoneer provizije</P></br>
    
    <form  method="get">
	<table>
	<tr>
    <td>Unesi iznos u dolarima: </td><td><input type="number" step="any" name="usdvalue" min="3.15" required autofocus></td>
	</tr>
	<tr>
    <td colspan="2" align="center"><input type="submit"></td>
	</tr>
	</table>
    </form>

	<div class="footer">
		<p>[<a href="https://countdown-timer.000webhostapp.com/rate/">Hourly rate calculator</a>]</p>
		<p>Copyright&copy Milan Masnikosa</p>
	</div>

<?php
    
    if(empty($_GET)) {
        $usdValue = 0;
		$newAtmAmout = 0;
    } else {
        $usdValue = $_GET['usdvalue'];
    }

    $temp = 'https://spreadsheets.google.com/feeds/download/spreadsheets/Export?key=1PuP8VpBBiw8_gquC9dUwFkE1Vu0Z-Hbpi10IDVk869I&exportFormat=csv'; // download link to csv

    $contents = file_get_contents('https://spreadsheets.google.com/feeds/download/spreadsheets/Export?key=1PuP8VpBBiw8_gquC9dUwFkE1Vu0Z-Hbpi10IDVk869I&exportFormat=csv');
    
    // $payoneer = $contents - 3.50;       // kurs payoneera koji je za 3.5 do 4 dinara nizi od redovnog
    // $fee = 3.15 * $payoneer;            // 3.15 dolara provizije za bankomate od payoneera
    // $preAtm = $payoneer * $usdValue;    // vrednost za bankomat pre skidanja provizije
    // $atm = $preAtm - $fee;              // konacna vrednost za bankomat
	
	// new calculation
	$newAtmAmout = (($usdValue - 3.15) * 0.975) * $contents;
    


?>
    <p> 
        Zvanični USD kurs:  <?php echo "<b>".$contents."</b>" ; ?> <br>
        
        <br>
        <?php
            if (!empty($_GET)) {
				echo "Za $" . "<b>" . $usdValue . "</b>" . " mozes dobiti " . "<b>" . round($newAtmAmout,2) . "</b>" . " RSD";
            }
        ?>
    </p>
    
</body>
</html>