<?php
header("Access-control-Allow-Origin:*");
header("Content-Type:application/json");
include "../dbcon/db.php";
include "../include/func.php";
$type=(isset($_GET["type"])) ? $_GET["type"] : die();
$bedrooms=(isset($_GET["total_bedroom"])) ? $_GET["total_bedroom"] : die() ;
$response=[];
$qry1=mysqli_query($conn,"SELECT c.customer_id,c.bedroom_num,pt.name FROM `customer` c INNER JOIN p_type pt ON c.property_type=pt.id WHERE pt.name='$type' AND c.bedroom_num='$bedrooms'");
$total_cus=0;
if($qry1){
	if(mysqli_num_rows($qry1) > 0){
       while($arr1=mysqli_fetch_assoc($qry1)){
       	  $cusID=$arr1["customer_id"];
       	  $qry2=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$cusID' ORDER BY submission_date DESC LIMIT 2");
       	  if(mysqli_num_rows($qry2) > 0){
       	  	$total_day_reading=$total_night_reading=$total_gas_reading=$no=$total_consumption=$avg_consumption_per_customer=0;
       	  	$date2='';
       	  	$date1='';
       	  	$total_cus=0;
       	  	while($arr2=mysqli_fetch_assoc($qry2)){
       	  	   if($no == 0){
       	  	   	 $total_consumption=$arr2["elec_readings_day"]+$arr2["elet_reading_night"]+$arr2["gas_reading"];
       	  	   	 $date1=date('d-m-Y',strtotime($arr2['submission_date']));
       	  	   	 //if in case previous date not exist then we have to subtract 1 month from latest date..
       	  	   	 $date2=date('d-m-Y',strtotime('-1 months',strtotime($date2)));
       	  	   	 $no++;
       	  	   }else{
       	  	   	  $total_consumption-=$arr2["elec_readings_day"]+$arr2["elet_reading_night"]+$arr2["gas_reading"];
                  $date2=date('d-m-Y',strtotime($arr2['submission_date']));
       	  	   }
       	  	   $total_cus++;
               $avg_consumption_per_customer+=ceil($total_consumption/dateDiffInDays($date1,$date2));
       	  	}
       	  	$response=[
                "type"=>$type,
                "bedroom"=>$bedrooms,
                "average_electricity_gas_cost_per_day"=>ceil($avg_consumption_per_customer/$total_cus)
       	  	];
       	  }
       }
	}
}
echo json_encode($response);
