<?php
session_start();
include '../../connection.php';

$sampleSetDate = mysqli_real_escape_string($link, $_POST['sampleSetDate']);

$sampleSetNumberSql = "SELECT count(sample_set_ID)
FROM sample_set
WHERE MID(sample_set_name, 5, 6) = '$sampleSetDate';";
$sampleSetNumber = mysqli_fetch_row(mysqli_query($link, $sampleSetNumberSql))[0] + 1;
$sampleSetNumber = str_pad($sampleSetNumber, 2, '0', STR_PAD_LEFT);

echo"
	<label>Set name: </label>
	<br>
	<p class='sample_set_name'>CCD-".$sampleSetDate."-".$sampleSetNumber."</p>
	<input type='hidden' id='sample_set_name' name='sample_set_name' value='CCD-".$sampleSetDate."-".$sampleSetNumber."'>
	<br>
	<label>Sample name: </label>
	<br>
	<p class='sample_set_name'>CCD-".$sampleSetDate."-".$sampleSetNumber."-01</p>
	<input type='hidden' id='sample_name' name='sample_name' value='CCD-".$sampleSetDate."-".$sampleSetNumber."-01'>";

mysqli_close($link);
?>