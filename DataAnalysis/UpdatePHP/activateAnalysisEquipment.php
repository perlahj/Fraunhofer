<?php
include '../../connection.php';

$eqID = mysqli_real_escape_string($link, $_POST["eqID"]);


$sql = "UPDATE anlys_equipment
		SET anlys_eq_active = TRUE
		WHERE anlys_eq_ID = '$eqID';";
$result = mysqli_query($link, $sql);

if(!result){
	die("Could not activate analysis equipment: ".mysqli_error($link));
}

mysqli_close($link);

?>