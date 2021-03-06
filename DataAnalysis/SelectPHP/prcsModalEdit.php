
<?
include '../../connection.php';
session_start();

$prcsID = mysqli_real_escape_string($link, $_POST["prcsID"]);
$sampleID = $_SESSION["sampleID"];
$sql = "SELECT prcs_coating as coating, prcs_eq_ID as equipment, prcs_position as position, prcs_rotation as rotation,
		prcs_comment as comment, prcs_date as date, prcs_run_number as run, prcs_run_ID as runID
FROM process
WHERE prcs_ID = '$prcsID';";
$row = mysqli_fetch_array(mysqli_query($link, $sql));

$prcsEquipementSql = "SELECT prcs_eq_ID as eqID, prcs_eq_name as eqName, prcs_eq_acronym
FROM prcs_equipment
WHERE prcs_eq_active = TRUE;";
$prcsEquipementResult = mysqli_query($link, $prcsEquipementSql);

$sampleNameSql = "SELECT sample_name
FROM sample
WHERE sample_ID = '$sampleID';";
$sampleName = mysqli_fetch_row(mysqli_query($link, $sampleNameSql))[0];

$allCoatingsSql = "SELECT DISTINCT(prcs_coating) from process;";
$allCoatingsResult = mysqli_query($link, $allCoatingsSql);

$allPositionsSql = "SELECT DISTINCT(prcs_position) from process;";
$allPositionsResult = mysqli_query($link, $allPositionsSql);


echo"
<div class='modal-dialog'>
  <div class='modal-content '>
    <form role='form'>
      <div class='modal-header'>
        <div class='col-md-12'>
          <button type='button' id='close_modal' class='btn close glyphicon glyphicon-remove' data-dismiss='modal'></button>
        </div>
        <h3 class='center_heading'>".$sampleName."</h3>
      </div>
      <div class='modal-body'>
        <div id='error_message_edit'></div>
          <div class='form-group'>
            <label>Date:</label>
            <input type='date' id='prcs_date' class='form-control' value='".$row['date']."' style='width:100%;' onchange='generateRunID(this.form)'>
          </div>
          <div class='form-group'>
            <label>Equipment:</label>
            <select id='prcs_eq' class='form-control' value='".$row['equipment']."' onchange='generateRunID(this.form)'>";
              while($eqRow = mysqli_fetch_row($prcsEquipementResult)){
                if($row['equipment'] === $eqRow[0]){
                  echo "<option selected value='".$eqRow[0]."'>".$eqRow[2]."</option>";
                }
                else{
                  echo "<option value='".$eqRow[0]."'>".$eqRow[2]."</option>";
                }
              }
             echo"
            </select>
          </div>
          <div class='form-group'>
            <label>Run#:</label>
            <input type='number' id='prcs_run' class='form-control' value='".$row['run']."' style='width:100%' onchange='generateRunID(this.form)'>     
          </div>
          <div class='form-group'>
            <label>Run ID:</label><p style='display:block;' id='prcs_run_ID'>".$row['runID']."</p>          
          </div>
          <div class='form-group'>
          	<label>Coating:</label>
            <input type='text' list='coatings' id='prcs_coating' name='prcs_coating' class='form-control' value='".$row['coating']."'>
            <datalist id='coatings'>";
              while($coatingRow = mysqli_fetch_array($allCoatingsResult)){
                echo "<option value='".$coatingRow[0]."'></option>";
              }
          echo"
            </datalist>
          </div>
          <div class='form-group'>
          	<label>Position:</label>
            <input type='text' list='positions' id='prcs_position' name='prcs_position' class='form-control' value='".$row['position']."'>
            <datalist id='positions'>";
              while($positionRow = mysqli_fetch_array($allPositionsResult)){
                echo "<option value='".$positionRow[0]."'></option>";
              }
          echo"
            </datalist>
          </div>
          <div class='form-group'>
          	<label>Rotation:</label>
          	<input type='number' id='prcs_rotation' class='form-control' value='".$row['rotation']."' style='width:100%'>
          </div>";

        echo"
        <div class='form-group'>
          <label>Comment</label>
          <textarea id='prcs_comment' class='form-control'>".$row['comment']."</textarea> 
        </div>
        <div class='modal-footer'>
          <button type='button' class='btn btn-danger glyphicon glyphicon-trash' onclick='deletePrcs(".$prcsID.", this)'></button>
          <button type='button' class='btn btn-success' onclick='editPrcs(".$prcsID.",this.form)'>Save</button> 
        </div>
      </form>
    </div>
  </div>
</div>
</div>";

?>
<script>
  document.getElementById('close_modal').onclick = function(){
    modal.style.display = 'none';
  }
  function generateRunID(form){
    var eqID = $(form).find('#prcs_eq').val();
    var acr = $(form).find('#prcs_eq option[value="'+eqID+'"]').text()
    var date = $(form).find('#prcs_date').val().replace(/-/g,"").substring(2,8);
    var run = $(form).find('#prcs_run').val();
    var runPadded = ('00' + run).substring(run.length);
    var runID = acr+date+runPadded;
    $(form).find('#prcs_run_ID').html(runID);
  }

</script>