﻿<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="author" content="IT PLOCH">
		<title>apitester</title>
	</head>
	<body>
		<?php
			// start generation time measurment
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$start = $time;

			// general settings		
			$debug_mode = true;						// debug mode active (true) or inactive (false)
			$query = 'https://api2.wfirma.pl/goods/find';			// query address to generate XML file with products from wFirma.pl warehouse
			$requestFile = 'request.xml';					// optional file with additional XML request
			$userName = 'user@domain';					// Your wFirma.pl username 
			$password = 'pass';						// Your wFirma.pl password
		
			// read additional parameters for XML request from file if exists
			if ((strlen($requestFile) > 0) && file_exists($requestFile) ) {
				$intFp = fOpen ( $requestFile, 'r' );
				$xmlRequest = fRead( $intFp, fileSize( $requestFile ) );
				fClose( $intFp );
			}
		
			// get XML file with data from wFirma.pl
			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $query);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
				curl_setopt($ch, CURLOPT_USERPWD, $userName . ':' . $password);
			$result = curl_exec($ch);
			curl_close($ch);
		
			// convert recieved XML file from string to SimpleXML Object
			$xml = simplexml_load_string($result);
		
			$status = $xml->status->code;
	
			// debug mode window
			if ($debug_mode == true) {
				echo '<table style="width: 100%; background-color: #CCFF99"><tr><td><code><b>DEBUG MODE ACTIVE</b><br>';
					echo 'custom data read from file "'.$requestFile.'" (data size: <b>';
					echo strlen($xmlRequest).' bytes</b>):';
						echo '<textarea disabled style="width: 99%; height: 100px">';
							print_r($xmlRequest);
						echo '</textarea>';
					echo 'XML data file recieved from wFirma.pl (data size: <b>';	
					echo strlen($result).' bytes</b>):';
						echo '<textarea disabled style="width: 99%; height: 100px">';
							print_r($result);
						echo '</textarea>';	
					echo 'XML file converted to XML Object:';		
						echo '<textarea disabled style="width: 99%; height: 100px">';
							print_r($xml);
						echo '</textarea>';
					echo 'XML communication status';		
						echo '<textarea disabled style="width: 99%; height: 100px">';
							print_r($xml->status->code);
						echo '</textarea>';
				echo '</code></td></tr></table>';
			}
		
			// just simple data presentation of products from wFirma.pl warehouse including product name, quantity, unit and netto price
			if ($status == 'OK') {
				echo '<table><tr><td>name</td><td>quantity></td><td>unit</td><td>netto price</td>';
				foreach($xml->goods->good as $good) {
					echo '<tr><td>'.$good->name.'</td><td>'.$good->count.'</td><td>'.$good->unit.'</td><td>'.$good->netto.'</td></tr>';
				}
				echo '</table>';
			}
			else {
				echo 'query error';
			}

			// display generation time
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$finish = $time;
			$total_time = round(($finish - $start), 4);
			echo '<br><b>Page generated in '.$total_time.' seconds.';
		?>
	</body>
</html>
