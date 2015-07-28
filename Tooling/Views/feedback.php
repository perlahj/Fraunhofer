<!DOCTYPE html>
<?php
include '../connection.php';
session_start();
?>  
<html>
<head>
  <title>Fraunhofer CCD</title>
  <link href='../css/bootstrap.min.css' rel='stylesheet'>
  <link href='../css/main.css' rel='stylesheet'>
  
  
</head>
<body>
  <div class='col-md-12'>
    <?php include '../header.php'; ?>
    <div class='container'>
      <h2>Place for feedback</h2>
      <h3>Name:</h3> <input type="text" id='name'name="name"><br>
      <h3>Feedback:</h3><textarea id='comment'name="comment"></textarea><br>
      <input type="submit" onclick='addFeedback()'>
      <div class='col-md-12'>
        <h2>Comments:</h2>
        <?php 
        $sql = "SELECT * 
                FROM Feedback 
                ORDER BY FID DESC";
        $result = mysqli_query($link, $sql);
        if(!$result){
            mysqli_error($link);
        }
        while($row = mysqli_fetch_array($result)){
            echo "<div class='row well well-lg'>".$row[0]."<div><strong>".$row[1]."</strong></div><div>". $row[2]."</div></div>";
        }
        ?>
      </div>
    </div>
    <script type="text/javascript">
    // refreshes the page automaticly after 2 minutes if the user is inactive
    // did this to see comments without refreshing
    var idleTime = 0;
    $(document).ready(function () {
        //Increment the idle time counter every minute.
        var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

        //Zero the idle timer on mouse movement.
        $(this).mousemove(function (e) {
            idleTime = 0;
        });
        $(this).keypress(function (e) {
            idleTime = 0;
        });
    });
    function timerIncrement() {
        idleTime = idleTime + 1;
        if(idleTime > 2) {
            window.location.reload();
        }
    }
  </script> 
</body>
</html>