<?php
// include "../dbcon/db.php";
// ==========PASSWORD HASH=========
function passwordHash($password){
	return hash('sha256',$password);
}
function dateDiffInDays($date1, $date2) 
{
  $diff = strtotime($date2) - strtotime($date1);
  return abs(round($diff / 86400));
}

function tariffPrice($conn,$type){
	$qry=mysqli_query($conn,"SELECT * FROM taiff WHERE taiff_type='$type'");
    $arr=mysqli_fetch_assoc($qry);
    return $arr['rate'];
}

?>