<?php
include '../../connection.php';
$order_name    = mysqli_real_escape_string($link, $_POST['order_name']);
$quote_number  = mysqli_real_escape_string($link, $_POST['quote_number']);
$supplier_name = mysqli_real_escape_string($link, $_POST['supplier_name']);
$first_date    = mysqli_real_escape_string($link, $_POST['first_date']);
$last_date     = mysqli_real_escape_string($link, $_POST['last_date']);


$order_name .= '%';
$quote_number .= '%';
$supplier_name .= '%';

?>
<div id='output'>
  <table class='table table-responsive table-striped table-condensed' id='mytable'>
    <thead>
      <tr>
        <th>Quote</th>
        <th>Supplier</th>
        <th>Quote issued</th>
        <th>Description</th>
        <th>Purchase number</th>
        <th><button class='btn btn-primary btn-xs' onclick='createRequestFromQuotes()'>Create request</button></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT quote_ID, supplier_ID, quote_date, description, order_ID, image, request_ID, quote_number
              FROM quote
              WHERE (order_ID = ANY(SELECT order_ID
                                   FROM purchase_order
                                   WHERE order_name LIKE '$order_name')
              OR order_ID IS NULL)
              AND supplier_ID = ANY(SELECT supplier_ID
                                    FROM supplier
                                    WHERE supplier_name LIKE '$supplier_name')
              AND quote_number LIKE '$quote_number' ";
      if(!empty($first_date)){
      	$sql .= "AND quote_date >= '$first_date' ";
      }
      if(!empty($last_date)){
      	$sql .= "AND quote_date <= '$last_date' ";
      }
      $sql .= "ORDER BY quote_ID DESC;";
      $result = mysqli_query($link, $sql);

      while($row = mysqli_fetch_array($result)){

        // Find the supplier names of each quote
        $supplierSql = "SELECT supplier_name
                        FROM supplier
                        WHERE supplier_ID = '$row[1]';";
        $supplierResult = mysqli_query($link, $supplierSql);
        $supplierRow = mysqli_fetch_array($supplierResult);

        // Find the order name of each quote
        $orderNameSql = "SELECT order_name
                         FROM purchase_order
                         WHERE order_ID = '$row[4]';";
        $orderNameResult = mysqli_query($link, $orderNameSql);
        $orderName = mysqli_fetch_array($orderNameResult);
        echo"
          <tr>
            <td><a href='#' data-toggle='modal' data-target='#".$row[0]."'>".$row[7]."</a></td>
            <td>".$supplierRow[0]."</td>
            <td>".$row[2]."</td>
            <td>".$row[3]."</td>
            <td><a href='#' onclick='setSessionIDSearch(".$row[4].")' data-toggle='modal' data-target='#".$row[4]."'>".$orderName[0]."</td>
            <td><center><input type='checkbox' name='".$row[0]."' value='chooseQuote'></center></td>
          </tr>";
      }
      ?>
    </tbody>
  </table>
  <?php
  // Modal for the quote ID
  $result = mysqli_query($link, $sql);
  while($row = mysqli_fetch_array($result)){
    // Find supplier name
    $supplierSql = "SELECT supplier_name
                    FROM supplier
                    WHERE supplier_ID = '$row[1]';";
    $supplierResult = mysqli_query($link, $supplierSql);
    $supplierRow = mysqli_fetch_array($supplierResult);

    // Find purchase order name
    $orderNameSql = "SELECT order_name
                     FROM purchase_order
                     WHERE order_ID = '$row[4]';";
    $orderNameResult = mysqli_query($link, $orderNameSql);
    $orderName = mysqli_fetch_array($orderNameResult);
    echo"
    <div class='modal fade' id='".$row[0]."' tabindex='-1' role='dialog' aria-labelledby='".$row[0]."' aria-hidden='true'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h4>Quote: ".$row[0]."</h4>
          </div>
          <div class='modal-body'>
            <p><strong>Quote number: </strong>".$row[7]."</p>
            <p><strong>Supplier: </strong>".$supplierRow[0]."</p>
            <p><strong>Purchase order: </strong>".$orderName[0]."</p>
            <p><strong>Description: </strong>".$row[3]."</p>
            <input type='image' src='../Scan/getQuoteImage.php?id=".$row[0]."' style='margin-top:5px;' width='100' height='90' onerror=\"this.src='../images/noimage.jpg'\" onclick=\"window.open('../Printouts/quotePrintout.php?id=".$row[0]."')\">
          </div>
          <div class='modal-footer' style='margin-top:10px'>
            <button type='button' style='float:right;' class='btn' data-dismiss='modal'>Close</button>
          </div>
        </div>
      </div>
    </div>";
  }
  // Modal for the purchase order
  $result = mysqli_query($link, $sql);
  while($row = mysqli_fetch_array($result)){
    $orderItemSql = "SELECT quantity, part_number, description, unit_price
                     FROM order_item
                     WHERE order_ID = '$row[4]';";
    $orderItemResult = mysqli_query($link, $orderItemSql);
    echo"
    <div class='modal fade' id='".$row[4]."' tabindex='-1' role='dialog' aria-labelledby='".$row[4]."' aria-hidden='true'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h4>Purchase order: ".$row[4]."</h4>
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
            </table>";
            echo"
          </div>
          <div class='modal-footer' style='margin-top:10px'>
            <div class='btn-group' style='float:left;'>
                <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                  Edit <span class='caret'></span>
                </button>
                <ul class='dropdown-menu' role='menu'>
                  <li><a href='../Views/purchaseOrderReceived.php'>Edit received info</a></li>
                  <li><a href='../Views/addOrderItem.php'>Edit PO</a></li>
                </ul>
            </div>
            <button type='button' onclick='printoutInfo(".$row[4].")' class='btn btn-primary' style='float:left; margin-left:5px'>Printout</button>
            <a href='../Views/viewAllImages.php' class='btn btn-primary' style='float:left'>View Scan</a>
            <button type='button' style='float:right;' class='btn' data-dismiss='modal'>Close</button>
          </div>
        </div>
      </div>
    </div>";
  }
  ?>
</div>