<?php
include '../../connection.php';
$noFinalInspection = mysqli_real_escape_string($link, $_POST['noFinalInspection']);
$part_number  = mysqli_real_escape_string($link, $_POST['part_number']);
$description  = mysqli_real_escape_string($link, $_POST['description']);
$department  = mysqli_real_escape_string($link, $_POST['department']);
$first_date  = mysqli_real_escape_string($link, $_POST['first_date']);
$last_date   = mysqli_real_escape_string($link, $_POST['last_date']);

// Make both part number and description start with '%' so that it looks for every character
// Makes it easier to filter
$part_number .= '%';
$description .= '%';

// added '%' in front of description so that I can search for substring in middle of description
// and not just in the beginning
$description = '%' . $description;

$totalFinalPrice = 0; // A variable that shows the complete price of all the PO's

// Query to find the department ID
$departmentSql = "SELECT department_ID
                  FROM department
                  WHERE department_name = '$department';";
$departmentResult = mysqli_query($link, $departmentSql);
$row = mysqli_fetch_array($departmentResult);
$department_ID = $row[0];

?>
<div id='output'>
  <table class='table table-responsive table-striped table-condensed'>
    <thead>
      <tr>
        <th>Part Number</th>
        <th>Description</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Final Price</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT oi.order_item_ID, oi.part_number, oi.description, oi.quantity, oi.unit_price, oi.department_ID, oi.order_ID
              FROM order_item oi
              WHERE oi.part_number LIKE '$part_number'
              AND oi.description LIKE '$description' ";
      if(!empty($department_ID)){
      	$sql .= "AND oi.department_ID = '$department_ID' ";
      }
      if(!empty($first_date)){
      	$sql .= "AND (SELECT po.order_date FROM purchase_order po WHERE po.order_ID = oi.order_ID) >= '$first_date' ";
      }
      if(!empty($last_date)){
      	$sql .= "AND (SELECT po.order_date FROM purchase_order po WHERE po.order_ID = oi.order_ID) <= '$last_date' ";
      }
      $sql .= "ORDER BY order_item_ID DESC;";
      $result = mysqli_query($link, $sql);
      while($row = mysqli_fetch_array($result)){
        $finalPrice = 0;
        // Query to find the final price of each order item
        $orderItemSql = "SELECT quantity, unit_price
                         FROM order_item
                         WHERE order_item_ID = '$row[0]';";
        $orderItemResult = mysqli_query($link, $orderItemSql);
        while($orderItemRow = mysqli_fetch_array($orderItemResult)){
          $finalPrice += $orderItemRow[0] * $orderItemRow[1];
          $totalFinalPrice += $finalPrice;
        }
        echo"
          <tr>
            <td><a href='#' onclick='setSessionIDSearch(".$row[6].")' data-toggle='modal' data-target='#".$row[0]."'>".$row[1]."</td>
            <td>".$row[2]."</td>
            <td>".$row[3]."</td>
            <td>$".number_format((float)$row[4], 2, '.', '')."</td>
            <td>$".number_format((float)$finalPrice, 2, '.', '')."</td>
          </tr>";
      }
      echo"
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <th>Total price:</th>
          <th><u style='border-bottom: 1px solid black'>$".number_format((float)$totalFinalPrice, 2, '.', '')."</u></th>
        </tr>";
      ?>
    </tbody>
  </table>
  <?php
  $result = mysqli_query($link, $sql);
  while($row = mysqli_fetch_array($result)){
    // Find the purchase order number
    $orderNumberSql = "SELECT order_name, approval_status
                       FROM purchase_order
                       WHERE order_ID = '$row[6]';";
    $orderNumberResult = mysqli_query($link, $orderNumberSql);
    $orderNumber = mysqli_fetch_array($orderNumberResult);

    // Information for each order item for that purchase order
    $orderItemSql = "SELECT quantity, part_number, description, unit_price
                     FROM order_item
                     WHERE order_ID = '$row[6]';";
    $orderItemResult = mysqli_query($link, $orderItemSql);
    echo"
    <div class='modal fade' id='".$row[0]."' tabindex='-1' role='dialog' aria-labelledby='".$row[1]."' aria-hidden='true'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h4>Purchase order: ".$orderNumber[0]."</h4>
          </div>
          <div class='modal-body'>
          <table class='table table-responsive'>
            <thead>
              <tr>
                <th>Pos. #</th>
                <th>Quantity</th>
                <th>Part #</th>
                <th>Description</th>
                <th>USD Unit</th>
                <th>USD Total</th>
              </tr>
            </thead>
            <tbody>";
              $counter = 1;
              $totalOrderPrice = 0;
              while($orderItemRow = mysqli_fetch_array($orderItemResult)){
                $total = $orderItemRow[0] * $orderItemRow[3];
                $totalOrderPrice = $totalOrderPrice + $total;
                echo"
                  <tr>
                    <td>".$counter."</td>
                    <td>".$orderItemRow[0]."</td>
                    <td>".$orderItemRow[1]."</td>
                    <td>".$orderItemRow[2]."</td>
                    <td>$".number_format((float)$orderItemRow[3], 2, '.', '')."</td>
                    <td>$".number_format((float)$total, 2, '.', '')."</td>";
                    $counter = $counter + 1;
              }
            echo"
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th>Total Order Price:</th>
                <th><u style='border-bottom: 1px solid black'>$".number_format((float)$totalOrderPrice, 2, '.', '')."</u></th>
              </tr>
            </tbody>
          </table>
          </div>
          <div class='modal-footer'>
            <div class='btn-group' style='float:left;'>
                <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                  Edit <span class='caret'></span>
                </button>
                <ul class='dropdown-menu' role='menu'>";
                if($orderNumber[1] != 'pending' && $orderNumber[1] != 'declined'){echo "<li><a href='../Views/purchaseOrderReceived.php'>Edit received info</a></li>";}
                  echo"<li><a href='../Views/addOrderItem.php'>Edit PO</a></li>
                </ul>
            </div>
            <a href='../Printouts/purchaseOrder.php' class='btn btn-primary' style='float:left; margin-left:5px;'";
            if($orderNumber[1] == 'pending' || $orderNumber[1] == 'declined'){echo " disabled";}
            echo">Printout</a>
            <a href='../Views/viewAllImages.php' class='btn btn-primary' style='float:left'";
            if($orderNumber[1] == 'pending' || $orderNumber[1] == 'declined'){echo " disabled";}
            echo">View Scan</a>
            <button type='button' style='float:right;' class='btn' data-dismiss='modal'>Close</button>
          </div>
        </div>
      </div>
    </div>";
  }
   ?>
