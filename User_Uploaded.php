<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8"> 
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</head>
<body>
	<div class="container" style="margin-bottom: 10px; margin-left:-10px">
		<div class="row">             
			<h4>Images you Uploaded:</h4>
		</div>
	</div>

	<div class = "container">
		<div class = "row">
			<div style ="width:400px; height:400px;overflow:auto; font-size:115%" class = "col-sm-2" id = "uploadImage">


<!-- Connect to the database, get the photo_id and the date according to the person_id, print the result in table format in the page. -->
<?php
	// There are two requests, one is the ordinary request when user enter the user ID and request all of the 
	// images, the other one is to request the images after filter. So distinct each request.

	$q = $_GET['q'];
	$i = $_GET['i'];

	if(!empty($_GET['q'])){
		$servername = "mysql.dur.ac.uk";
		$username = "ljtp63";
		$password = "sutt49on";
		$dbname = "Xljtp63_MammalWeb1";

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);

		$query = "SELECT photo_id, taken FROM Photo where person_id = $q";
		$result = mysqli_query($conn, $query) or die("Failed Query");

		if(mysqli_num_rows($result) == 0){
			echo "Cannot find related information in the Database";
		} else{
			echo "<table class = 'table table-striped'>";
			echo "<tr><th>PhotoID</th><th>Uploaded Date</th><th>View</th></tr>";
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr><td>{$row['photo_id']}</td><td>{$row['taken']}</td>";
				echo "<td><button class='editbtn' onclick = 'showpictureUpdate(this)'>View</button></td>";
			}
			echo "</table>";
		}
	} elseif(!empty($_GET['i'])){
		
		// Most of the codes in this clause is to construct the SQL according to the filter criterias.

		$error = False;
		$q = $i;
		$pieces = explode(",", $q);

		$person_id = $pieces[11];
		$site_id = $pieces[10];


		$months = array('1' => "01", '01' => "01",'2' => "02", '02' => "02",'3' => "03", '03' => "03",'4' => "04",'04' => "04",
		'5' => "05",'05' => "05", '6' => "06",'06' => "06", '7' => "07",'07' => "07", '8' => "08",'08' => "08", '9' => "09",'09' => "09",
		'10' => "10", '11' => "11", '12' => "12");

		$monthFrom = $pieces[1];
		$monthFrom = $months[$monthFrom];
		$monthTo = $pieces[4];
		$monthTo = $months[$monthTo];
		
		$dayFrom = $pieces[0];
		if (strlen($dayFrom) == 1 and $dayFrom != "-") {
			$dayFrom = "0".$dayFrom;
			//echo "yes";
		}
		$dayTo = $pieces[3];
		if (strlen($dayTo) == 1 and $dayTo != "-") {
			$dayTo = "0".$dayTo;
		}
		
		$yearFrom = $pieces[2];
		$yearTo = $pieces[5];
		
		$dateFrom = False;
		$dateTo = False;
		
		if ($dayFrom != "-") {
			$dateFrom = $yearFrom."-".$monthFrom."-".$dayFrom;
			$dateFrom = $dateFrom." 00:00:00";
		}
		if ($dayTo != "-") {
			$dateTo = $yearTo."-".$monthTo."-".$dayTo;
			$dateTo = $dateTo." 00:00:00";
		}


		$hours = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18,
		19, 20, 21, 22, 23);
		
		$minutes = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 
		21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 
		41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59);
		
		$fromHour = $pieces[6];
		$fromMin = $pieces[7];
		$toHour = $pieces[8];
		$toMin = $pieces[9];
		
		
		$times = array($fromHour, $fromMin, $toHour, $toMin);
		
		$timeFrom = False;
		$timeTo = False;
		
		#-----Check inputs valid-----#
		if ($fromMin != "-" and $fromHour == "-") {
			$error = True;
		}
		if ($toMin != "-" and $toHour == "-") {
			$error = True;
		}
		foreach ($times as $time) {
			if ($time != "-" and !is_numeric($time)) {
				$error = True;
			}
		}
		if (!in_array($fromHour, $hours) and $fromHour != "-") {
	    	$error = True;
		}
		if (!in_array($toHour, $hours) and $toHour != "-") {
	    	$error = True;
		}
		if (!in_array($fromMin, $minutes) and $fromMin != "-") {
	    	$error = True;
		}
		if (!in_array($toMin, $minutes) and $toMin != "-") {
	    	$error = True;
		}
		if (is_numeric($fromHour) and $fromHour != "-" and strlen($fromHour) == 1) {
			$fromHour = "0".$fromHour;
		}
		if (is_numeric($fromMin) and $fromMin != "-" and strlen($fromMin) == 1) {
			$fromMin = "0".$fromMin;
		}
		if (is_numeric($toHour) and $toHour != "-" and strlen($toHour) == 1) {
			$toHour = "0".$toHour;
		}
		if (is_numeric($toMin) and $toMin != "-" and strlen($toMin) == 1) {
			$toMin = "0".$toMin;
		}
		#---------------------------#
		
		if ($fromHour != "-" and $fromMin == "-") {
			$timeFrom = $fromHour.":00:00";
		} else if ($fromHour != "-" and $fromMin != "-") {
			$timeFrom = $fromHour.":".$fromMin.":00";
		}
		if ($toHour != "-" and $toMin == "-") {
			$timeTo = $toHour.":00:00";
		} else if ($toHour != "-" and $toMin != "-") {
			$timeTo = $toHour.":".$toMin.":00";
		}

		$site = false;
		if($site_id != "-"){
			$site = true;

		}

		$servername = "mysql.dur.ac.uk";
		$username = "ljtp63";
		$password = "sutt49on";
		$dbname = "Xljtp63_MammalWeb1";

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);

		
		$query = "SELECT photo_id, taken FROM Photo where person_id = $person_id";

		if ($dateFrom and $dateTo) {
			$query = $query." AND taken BETWEEN '".$dateFrom."' AND '".$dateTo."'";
		} else if ($dateFrom and !$dateTo) {
			$query = $query." AND taken > '".$dateFrom."'";
		} else if (!$dateFrom and $dateTo) {
			$query = $query." AND taken < '".$dateTo."'";
		}
		
		if ($timeFrom and $timeTo) {
			$query = $query." AND TIME(taken) BETWEEN '".$timeFrom."' AND '".$timeTo."'";
		} else if ($timeFrom and !$timeTo){
			$query = $query." AND TIME(taken) > '".$timeFrom."'";
		} else if (!$timeFrom and $timeTo) {
			$query = $query." AND TIME(taken) < '".$timeTo."'";
		}

		if($site){
			$query = $query." AND site_id = $site_id";
		}

		$result = mysqli_query($conn, $query) or die("Failed Query");

		if(mysqli_num_rows($result) == 0){
			echo "Cannot find related information in the Database";
		} else{
			echo "<table class = 'table table-striped'>";
			echo "<tr><th>PhotoID</th><th>Uploaded Date</th><th>View</th></tr>";
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr><td>{$row['photo_id']}</td><td>{$row['taken']}</td>";
				echo "<td><button class='editbtn' onclick = 'showpictureUpdate(this)'>View</button></td>";
			}
			echo "</table>";
		}

	}

?>

<!-- This javascript will get the image according to the photo_id and show the image in the page. -->
<script>

function showpictureUpdate(e){

	var hi = e.parentNode.parentNode.cells[0].innerHTML;
	$q = hi;
	$("#image2").load('showPicture.php?q='+$q);
}
</script>

			</div>
			<div class = "col-sm-6" id = "image2" style= "font-size:115%">Image Here</div>
		</div>
	</div>
</body>
</html>