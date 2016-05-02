<?php
	$q = $_GET['q'];
	#--------Connection Code----------#

	$servername = "mysql.dur.ac.uk";
	$username = "ljtp63";
	$password = "sutt49on";
	$dbname = "Xljtp63_MammalWeb1";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	$query = "SELECT filename, site_id, person_id FROM Photo where photo_id = $q";
	$result = mysqli_query($conn, $query) or die("Failed Query");

	$row = mysqli_fetch_assoc($result);

	$file_name = $row['filename'];
	$person_id = $row['person_id'];
	$site_id = $row['site_id'];

	$url = "person_".$person_id."/site_".$site_id."/".$file_name;
	echo "<img width='450px' height='400px' src='http://www.mammalweb.org//biodivimages/".$url."'/>";

?>

<!-- Get the associated filename, site_id and person_id according to that particular photo_id and combine those attributes together
to construct an URL, use the URL to query the mammalweb to get the specific image. -->