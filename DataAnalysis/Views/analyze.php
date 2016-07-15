<?php
include '../../connection.php';
session_start();

$securityLevel = $_SESSION["securityLevelDA"];
$sampleID = $_SESSION["sampleID"];
$sampleSetID = $_SESSION["sampleSetID"];
$propID = $_SESSION["propID"];
$eqID = $_SESSION["eqID"];

$recentSampleSetsSql = "SELECT sample_set_ID, sample_set_name
FROM sample_set
ORDER BY sample_set_ID DESC LIMIT 10;";
$recentSampleSetsResult = mysqli_query($link, $recentSampleSetsSql);

$propertiesSql = "SELECT anlys_prop_ID, anlys_prop_name
FROM anlys_property;";
$propertiesResult = mysqli_query($link, $propertiesSql);

?>

<head>
  <title>Fraunhofer CCD</title>
</head>
<body>
  <?php include '../header.php'; ?>
  <?php echo "<input type='hidden' id='employee_ID' value='".$employee_ID."'>"; ?>
  <div class='container'>
  </form>
  <div class='row well well-lg col-md-12'>
    <form role='form'>
      <div id='error_message'></div>
      <div id='sample_div' class='col-md-12'>
        <h4 class='custom_heading'>1. Choose a sample</h4>
        <div class='col-md-4 form-group'>
          <label>Sample set: </label>
          <select id='sample_set_ID' class='form-control' onchange='updateSamplesInSet()' style='width:auto;'>
            <option value='-1'>Choose a set</option>
            <?
            while($sampleSetRow = mysqli_fetch_array($recentSampleSetsResult)){
              echo "<option value='".$sampleSetRow[0]."'>".$sampleSetRow[1]."</option>";
            }
            ?>
          </select>
        </div>
        <div id='samples_in_set' class='col-md-4 form-group'></div>
        <div id='sample_info' class='col-md-4 form-group'>
        </div>
      </div>

      <div class='col-md-12'>
        <h4 id='prop_eq_div' class='custom_heading'>2. Choose a property and equipment</h4>
        <?php
      // For easy changing of layout of tables.
        $numTablesPerRow = 4;
        $colSize = 12/$numTablesPerRow;
        $tableCounter = 0;
        while($propertyRow = mysqli_fetch_array($propertiesResult)){
          if($tableCounter % $numTablesPerRow === 0){
            echo"
          </div>
          <div class='col-md-12'>";
          }
          echo"
          <table class='col-md-".$colSize."'>
            <thead>
              <tr>
                <th>".$propertyRow[1]."</th>
              </tr>
            </thead>
            <tbody>";
              $equipmentSql = "SELECT e.anlys_eq_ID, e.anlys_eq_name
              FROM anlys_equipment e, anlys_eq_prop a
              WHERE a.anlys_eq_ID = e.anlys_eq_ID AND a.anlys_prop_ID = '$propertyRow[0]';";
              $equipmentResult = mysqli_query($link, $equipmentSql);
              while($equipmentRow = mysqli_fetch_array($equipmentResult)){
                echo"
                <tr>
                  <td><a id='".$equipmentRow[0].$propertyRow[0]."' onclick='showAnlysResultForm(".$propertyRow[0].",".$equipmentRow[0].",".$sampleID.",this.form)'>".$equipmentRow[1]."</a></td>
                </tr>";

              }
              echo"
            </tbody>
          </table>";
          $tableCounter++;
        }
        ?>
      </div>
      <div class='col-md-12'>
        <h4 class='custom_heading'>3. Enter results</h4>
        <div id='res_div'></div>
      </div>
      <div class='col-md-12'>
        <h4 class='custom_heading'>4. Display averages?</h4>
        <div id='aveg_div'></div>
      </div>

    </div>
  </form>
</div>
<script>

  var bootstrapBlue = "#337AB7";
  var bootstrapDarkBlue = "#23527C";
  var bootstrapPurple = "#5E4485";

  $(document).ready(function(){

    updateSamplesInSet(<?php echo $sampleSetID; ?>);
    
    if(<?php echo $propID; ?> && <?php echo $eqID; ?>){
        showAnlysResultForm(<?php echo $propID; ?>,<?php echo $eqID; ?>);
      }

    // Color the equipment link that is chosen.
    $("#<?php echo $eqID.$propID; ?>").css("color", bootstrapPurple);
    $("#<?php echo $eqID.$propID; ?>").css("text-decoration", "underline");

    })

    // Color the equipment the user chose. 
    $("td a").click(function () { 
        $("td a").css("color", bootstrapBlue);
        $("td a").css("text-decoration", "none");
        $(this).css("color", bootstrapPurple);
        $(this).css("text-decoration", "underline");

      });

    // Make the combo box select the currently chosen sample set.
    $("#sample_set_ID").val(<?php echo $sampleSetID; ?>)
 
</script>
</body>