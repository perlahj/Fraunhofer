<?php
include '../../connection.php';
session_start();
$sampleSetID = $_SESSION["sampleSetID"];
$sampleID = $_SESSION["sampleID"];
$sampleSetName = $_POST["sample_set_name"];
$sampleName =  $_POST["sample_name"];

$samplePicture = "sample_picture";

if($_FILES[$samplePicture]["name"]){

// Build the path
$target_dir = "../Uploads/".date("Y")."/".date("m")."/".$sampleSetName."/".$sampleName."/";
// If the folder does not exist create it.
if (!file_exists($targer_dir)) {
    mkdir($target_dir, 0777, true);
}
$temp = explode(".", $_FILES[$samplePicture]["name"]);
$newName = $sampleName."_profilePicture.".end($temp);

$target_file = $target_dir.$newName;

$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES[$samplePicture]["tmp_name"]);
    if($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        //echo "File is not an image.";
        $errorMessage .= "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
          		"This file is not an image.</div>";
        $uploadOk = 0;
    }
}
// Check if file already exists
// if (file_exists($target_file)) {
//     // echo "Sorry, file already exists.";
//     $errorMessage .= "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
//           		"Sorry, file already exists.</div>";
//     $uploadOk = 0;
// }
// Check file size
if ($_FILES[$samplePicture]["size"] > 500000) {
    //echo "Sorry, your file is too large.";
    $errorMessage .= "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
          		"Sorry, your file is too large. The max size is: </div>";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $errorMessage .= "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
          		"Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    // echo "Sorry, your file was not uploaded.";
    $errorMessage .= "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
          		"Sorry, your file was not uploaded.</div>";

// if everything is ok, try to upload file
} else {
	// This does replace an existing photo with same name.
    if (move_uploaded_file($_FILES[$samplePicture]["tmp_name"], $target_file)) {
        // echo "The file ". basename( $_FILES[$samplePicture]["name"]). " has been uploaded.";
    } else {
        // echo "Sorry, there was an error uploading your file.";
         $errorMessage .= "<div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+
          		"Sorry, there was an error uploading your file.</div>";
    }
}


$sql = "UPDATE sample
		SET sample_picture = '$target_file'
		WHERE sample_ID = '$sampleID';";
$result = mysqli_query($link, $sql);

if(!$result){
	die("Could not update sample picture: ".mysqli_error($link));
}
}

mysqli_close($link);

// There can be no echo before this call, otherwise the redirect will not work. 
header('Location: ../Views/addSample.php?id='.$sampleSetID);

?>