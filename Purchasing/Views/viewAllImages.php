<?php
include '../../connection.php';
session_start();
//find the current user
$user = $_SESSION["username"];
//find his level of security
$secsql = "SELECT security_level
           FROM employee
           WHERE employee_name = '$user'";
$secResult = mysqli_query($link, $secsql);

while($row = mysqli_fetch_array($secResult)){
  $user_sec_lvl = $row[0];
}
$user_sec_lvl = str_split($user_sec_lvl);
$user_sec_lvl = $user_sec_lvl[1];
// if the user security level is not high enough we kill the page and give him a link to the log in page
if($user_sec_lvl < 2){
  echo "<a href='../../Login/login.php'>Login Page</a></br>";
  die("You don't have the privileges to view this site.");
}
mysql_set_charset('utf8');
header('Content-Type: text/html; charset=utf-8');
$order_ID = $_SESSION["order_ID"];

$sql = "SELECT scan_ID, scan_image
        FROM purchase_scan
        WHERE order_ID = '$order_ID';";
$result = mysqli_query($link, $sql);

$orderSql = "SELECT quantity, part_number, description, final_inspection
             FROM order_item
             WHERE order_ID = '$order_ID';";
$orderResult = mysqli_query($link, $orderSql);

$quoteSql = "SELECT quote_ID, content, quote_number, supplier_ID, quote_date
             FROM quote
             WHERE order_ID = '$order_ID';";
$quoteResult = mysqli_query($link, $quoteSql);

?>
<head>
  <title>Fraunhofer CCD</title>
</head>
<body>
  <?php include '../header.php'; ?>
  <div class='container'>
    <div class='row well'>
      <div class='col-md-4'>
        <h3>All Scans</h3>
        <table class='table table-responsive'>
          <tbody>
            <?php
            if(mysqli_num_rows($result) == 0){
              echo"<tr><td><img src='../images/noimage.jpg' width='100' height='100'></td></tr>";
            }
            while($row = mysqli_fetch_array($result)){
              echo"<tr>
                    <td><input type='image' src='../Scan/getImage.php?id=".$row[0]."' width='100' height='100' onerror=\"this.src='../images/noimage.jpg'\" onclick=\"window.open('../Printouts/scanprintout.php?id=".$row[0]."')\"></td>
                    <td><button class='btn btn-danger' style='margin-top:35px;' onclick='deletePurchaseScan(".$row[0].")'>Delete</button></td>
                  </tr>";
            }
            ?>
          </tbody>
        </table>
        <?php
        if(mysqli_num_rows($quoteResult) != 0){
          echo"<h3>All Quotes</h3>
               <table class='table table-responsive'>
                <tbody>";
              while($quoteRow = mysqli_fetch_array($quoteResult)){
                $supplierNameSql = "SELECT supplier_name
                                    FROM supplier
                                    WHERE supplier_ID = '$quoteRow[3]';";
                $supplierNameResult = mysqli_query($link, $supplierNameSql);
                $supplierNameRow = mysqli_fetch_array($supplierNameResult);
                echo"<tr>
                      <td><input type='image' src='../Scan/getQuoteImage.php?id=".$quoteRow[0]."' width='100' height='100' onerror=\"this.src='../images/noimage.jpg'\" onclick=\"window.open('../Printouts/quotePrintout.php?id=".$quoteRow[0]."')\">
                      <button class='btn btn-danger' style='margin-top:35px;' onclick='deleteQuote(".$quoteRow[0].")'>Delete</button></td>
                      <td><p><strong>Quote number: </strong><a href='../SelectPHP/download.php?id=".$quoteRow[0]."'>".$quoteRow[2]."</a><br></p>
                      <p><strong>Supplier: </strong>".$supplierNameRow[0]."</p>
                      <p><strong>Date issued: </strong>".$quoteRow[4]."</p></td>
                    </tr>";
              }
              echo"
            </tbody>
          </table>";
        }
        ?>
      </div>
      <div class='col-md-7 col-md-offset-1'>
        <h3>Purchase Order: <?php echo $order_ID; ?></h3>
        <table class='table table-responsive'>
          <thead>
            <tr>
              <th>Quantity</th>
              <th>Part number</th>
              <th>Description</th>
              <th>Final inspection</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while($orderRow = mysqli_fetch_array($orderResult)){
              echo"<tr>
                    <td>".$orderRow[0]."</td>
                    <td>".$orderRow[1]."</td>
                    <td>".$orderRow[2]."</td>
                    <td>".$orderRow[3]."</td>
                  </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class='col-md-12'>
        <div class='col-md-6'>
          <form action="../InsertPHP/addImage.php" method="post" enctype="multipart/form-data" onsubmit="return checkSize(1000000)">
            <div class='col-md-6'>
              <label>Select image to upload:</label>
              <!-- hidden type which is used to redirect to the correct view -->
              <input type='hidden' value='allScans' id='redirect' name='redirect'>
              <input type="file" name="fileToUpload" id="fileToUpload" accept="image/jpeg">
            </div>
            <div class='col-md-6'>
              <input type="submit" class='btn btn-primary' value="Upload Image" name="submit">
            </div>
          </form>
        </div>
        <a href='purchaseOrderReceived.php' class='btn btn-primary' style='float:right; margin-top:15px;'>Back to purchase order</a>
      </div>
    </div>
  </div>
</body>
