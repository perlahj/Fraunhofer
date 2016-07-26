<?php
include '../../connection.php';

$sampleName = mysqli_escape_string($link, $_POST["sampleName"]);
$beginDate = mysqli_real_escape_string($link, $_POST["beginDate"]);
$endDate = mysqli_real_escape_string($link, $_POST["endDate"]);
$minThickness = mysqli_real_escape_string($link, $_POST["minThickness"]);
$maxThickness = mysqli_real_escape_string($link, $_POST["endThickness"]);

$sampleName = "%".$sampleName."%";


$sql = "SELECT s.sample_ID, s.sample_name
FROM sample s
WHERE s.sample_name LIKE '$sampleName'";

// The name of the sample contains its date E.g. CCD-160725-01.
if(!empty($beginDate)){
        $sql .= "AND MID(s.sample_name, 5,6) >= '$beginDate'";
}
if(!empty($endDate)){
        $sql .= "AND MID(s.sample_name, 5,6) <= '$endDate'";
}
if(!empty($minThickness)){
  $sql .= "AND sample_ID IN(
SELECT  s.sample_ID
FROM sample s, anlys_result r
WHERE s.sample_ID = r.sample_ID AND r.anlys_res_result >= '$minThickness' AND anlys_eq_prop_ID IN (SELECT anlys_eq_prop_ID
              FROM anlys_eq_prop
              WHERE anlys_prop_ID = (SELECT anlys_prop_ID
                                    FROM anlys_property
                                    WHERE anlys_prop_name LIKE 'Thickness')))";
}
if(!empty($maxThickness)){
  $sql .= "AND sample_ID IN(
SELECT  s.sample_ID
FROM sample s, anlys_result r
WHERE s.sample_ID = r.sample_ID AND r.anlys_res_result <= '$maxThickness' AND anlys_eq_prop_ID IN (SELECT anlys_eq_prop_ID
              FROM anlys_eq_prop
              WHERE anlys_prop_ID = (SELECT anlys_prop_ID
                                    FROM anlys_property
                                    WHERE anlys_prop_name LIKE 'Thickness')))";
}

$sql .= "ORDER BY s.sample_ID DESC;";
$result = mysqli_query($link, $sql);



?>
<table class='table table-responsive table-condensed'>
    <thead>
    	<tr>
      		<th>Sample</th>
      		<th>Coating</th>
      		<th>Thickness</th>
      	</tr>
    </thead>
    <tbody>
    <?
    while($row = mysqli_fetch_array($result)){
    	echo"
    		<tr>
    			<td><a onclick='loadAndShowSampleModal(".$row[0].")'>".$row[1]."</a></td>
    			<td>Coating</td>";
      // Get the thickness of the sample. 
    	$thicknessSql = "SELECT TRUNCATE(AVG(r.anlys_res_result), 3)
              FROM anlys_result r
              WHERE r.sample_ID = '$row[0]' AND anlys_eq_prop_ID IN (SELECT anlys_eq_prop_ID
              FROM anlys_eq_prop
              WHERE anlys_prop_ID = (SELECT anlys_prop_ID
                                    FROM anlys_property
                                    WHERE anlys_prop_name LIKE 'Thickness'))
              GROUP BY anlys_eq_prop_ID
              ORDER BY anlys_res_ID DESC
              LIMIT 1;";
        $thicknessResult = mysqli_query($link, $thicknessSql);
        $thicknessRow = mysqli_fetch_row($thicknessResult);


       	echo"
    			<td>".$thicknessRow[0]."</td>
    		</tr>";
    }
    ?>

    </tbody>
</table>
<!-- Sample Modals -->
<div id="sample_modal" class="modal"></div>
<script>
  var modal = document.getElementById('sample_modal');
  function loadAndShowSampleModal(sampleID){
    loadSampleModal(sampleID);
    modal.style.display = "block";
  }
 </script>