<?php
header("Access-control-Allow-Origin:*");
header("Content-Type:application/json");
include "../dbcon/db.php";
$response=[];
$qry=mysqli_query($conn,"SELECT COUNT(*) AS total_count,pt.name AS property_name FROM customer c LEFT JOIN p_type pt ON c.property_type=pt.id GROUP BY c.property_type");

if($qry){
 $rows=mysqli_num_rows($qry);
 if($rows > 0){
 	while($arr=mysqli_fetch_assoc($qry)){
 		$output=[
            $arr["property_name"]=>$arr["total_count"],
 		];
 		$response[]=$output;
 	}
 }else{
 	$response="No result found";
 }
 echo json_encode($response);
}

 ?>
