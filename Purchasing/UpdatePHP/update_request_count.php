<?php
    include '../../connection.php';
    $activeRequestsSql = "SELECT COUNT(request_ID)
                          FROM order_request
                          WHERE active = 1 AND order_ID IS NULL;";
    $activeRequestsResult = mysqli_query($link, $activeRequestsSql);
    $activeRequests = mysqli_fetch_array($activeRequestsResult);
    if(!$activeRequestsResult){
      echo mysqli_error($link);
    }
    if($activeRequests[0] > 0){
    echo "<button type='button' class='btn btn-primary col-md-12' onclick='location.href=\"processOrder.php\"'>
              Process order <span class='badge'>".$activeRequests[0]."</span>
            </button>";
    } else{
      echo "<button type='button' class='btn btn-primary col-md-12' onclick='location.href=\"processOrder.php\"'>
                Process order
            </button>";
    }
 ?>
