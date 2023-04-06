<?php
session_start(); 
if(!isset($_SESSION['cid']) || $_SESSION['type'] != 'admin'){
   header("location:index.php");
}
$id=base64_decode($_GET["id"]);
include "dbcon/db.php";
include "include/func.php";
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customer Stats</title>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <?php include "include/csslink.php"; ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light fixed-top"  style="background-color:#b8860b;">
	  <a class="navbar-brand" href="#"><h1 style="color: blue;">IGSE</h1></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="igse_dash.php"><i class="fa fa-home mr-1"></i>Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="tariff.php"><i class="fa fa-gbp mr-1"></i>tariff</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="igse_customer.php"><i class="fa fa-user-circle mr-1"></i>Customer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="fa fa-sign-out mr-1" aria-hidden="true"></i>logout</a>
          </li>
        </ul>
      </div>
    </nav>
 
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
var data = google.visualization.arrayToDataTable([
       ['Reading', 'consumption'],
       <?php
       $qryFind=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$id' ORDER BY submission_date DESC LIMIT 2");
       
       if($qryFind){
         $row=mysqli_num_rows($qryFind);
          $totalEle=0;
          $totalGas=0;
         if($row > 0){
            $no=0;
            $date1=date('d-m-y');
            $date2=date('d-m-y',strtotime('-1 Months',strtotime($date1)));

            while($arrFind=mysqli_fetch_assoc($qryFind)){
              if($no==0){
                $date1=date("d-m-Y",strtotime($arrFind['submission_date']));
                $totalEle=$arrFind['elec_readings_day']+$arrFind['elet_reading_night'];
                $totalGas=$arrFind['gas_reading'];
                $no++;
              }else{
                $date2=date("d-m-Y",strtotime($arrFind['submission_date']));
                $totalEle-=$arrFind['elec_readings_day']+$arrFind['elet_reading_night'];
                $totalGas-=$arrFind['gas_reading'];
              }  
            }
            $totalDays=dateDiffInDays($date1, $date2);
            $perGas=ceil(($totalGas/$totalDays));
            $perEle=ceil(($totalEle/$totalDays));
            echo "['".$perGas." KWh per Day Avg Gas Consumption',".$perGas."],";
            echo "['".$perEle." KWh per day Avg Electricity Consumption',".$perEle."]";
         }else{

         }
       }
       ?>
      ],false); 
        var options = {'title':'average gas and electricity consumption (in kWh) per Day',
                       'width':350,
                       'height':350};
        var chart = new google.visualization.PieChart(document.getElementById('chart_avg'));
        chart.draw(data, options);
      }
    </script>
    <div class="container-fluid pt-150">
      <div class="row mb-4">
        <div class="col col-12 col-md-5 col-lg-5">
          <div class="card alert-success p-3" style="background-color:#bdb76b;">
            <p>Customer ID: <?= $id; ?></p>
           <?php  $lp=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$id' AND status='Paid' ORDER BY reading_id DESC LIMIT 1");
               if(mysqli_num_rows($lp) == 1){
                $arrLP=mysqli_fetch_assoc($lp);
           ?> 
               <p>Last bill Paid <i class="fa fa-gbp mr-1"></i><?= $arrLP['total_bill']?></p>
             <?php }else{ ?>
               <p>Last bill Paid <i class="fa fa-gbp mr-1"></i>0.00</p>
             <?php } ?>
           </div>
          <div id="chart_avg"></div>
        </div>
        <div class="col col-12 col-md-7 col-lg-7">
          <div class="card p-2" style="overflow-x:scroll;">
            <p class="font-weight-bold  text-center"  style="background-color:#bc8f8f; color: #191970	;">Meter Reading</p>
            <table class="table table-striped" style="background-color:#bdb76b;">
              <thead >
                <tr>
                  <td class="font-weight-bold">Submission Date</td>
                  <td class="font-weight-bold">Electricity Day</td>
                  <td class="font-weight-bold">Electricity Night</td>
                  <td class="font-weight-bold">Gas Reading</td>
                  <td class="font-weight-bold">Bill</td>
                  <td class="font-weight-bold">status</td>
                </tr>
              </thead>
              <tbody>
<?php $read_qry=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$id' ORDER BY submission_date DESC");
      if(mysqli_num_rows($read_qry) > 0){
        while($read_arr=mysqli_fetch_assoc($read_qry)){
          $status=($read_arr["status"] == 'Pending') ? '<p class="p-1 bg-danger text-light text-center">Pending</p>' : '<p class="p-1 bg-success text-light text-center">Paid</p>';
?>
                <tr>
                  <td class="font-weight-bold text-primary"><?= date('d-m-Y',strtotime($read_arr["submission_date"])); ?></td>
                  <td><?= $read_arr["elec_readings_day"] ?> KWh</td>
                  <td><?= $read_arr["elet_reading_night"] ?> Kwh</td>
                  <td><?= $read_arr["gas_reading"] ?> Kwh</td>
                  <td class="text-success font-weight-bold"><i class="fa fa-gbp mr-1"></i><?= $read_arr["total_bill"] ?></td>
                  <td ><?= $status ?></td>
                </tr>
<?php
        }
      }else{
        ?>
            <tr>
              <td colspan="6">No Reading Found</td>
            </tr>
        <?php
      }
?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php include "include/script.php"; ?>
  </body>
</html>