<?php
  include '../../connection.php';
  session_start();
  mysql_set_charset('utf8');

  $order_ID = $_SESSION["order_ID"];
  $id = $_GET['id'];

  // If there is no id sent in to this view then we find all the images linked to that PO
  // Otherwise we find the image linked to that PO that has the specific scan ID
  if(empty($id)){
    $sql = "SELECT scan_image
            FROM purchase_scan
            WHERE order_ID = '$order_ID'
            ORDER BY scan_ID DESC;";
  }else{
    $sql = "SELECT scan_image
            FROM purchase_scan
            WHERE order_ID = '$order_ID'
            AND scan_ID = '$id'
            ORDER BY scan_ID DESC;";
  }

  $result = mysqli_query($link, $sql);

  if(!$result){
      echo("Something went wrong : ".mysqli_error($link));
  }
  if(mysqli_num_rows($result) > 0){
    header('Content-Type: text/html; charset=utf-8');
      while($row = mysqli_fetch_array($result)){
        echo $row[0];
      }
  }else{
      echo "Image does not exist";
  }
  mysql_close($link);
?>
