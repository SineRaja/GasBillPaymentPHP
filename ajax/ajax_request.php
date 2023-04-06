<?php
session_start();
include "../dbcon/db.php";
include "../include/func.php";

if (isset($_POST["action"]) && $_POST["action"] == "submitRegisterForm") {
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $email = $_POST["email"];
  $password = passwordHash($_POST["reg_pass"]);
  $address = $_POST["address"];
  $property_type = $_POST["pro_type"];
  $no_of_bedrooms = $_POST["no_of_bedrooms"];
  $evc = $_POST["evc"];
  $qryEmail = mysqli_query($conn,"SELECT * FROM customer WHERE customer_id='$email'");
  if (mysqli_num_rows($qryEmail) > 0) {
    $response["message"] = "The provided email is already associated with an existing customer...!!!";
  }else{
    //    echo
    $qryEVC = mysqli_query($conn,"SELECT * FROM voucher WHERE EVC_code='$evc'");
    if (mysqli_num_rows($qryEVC) > 0) {
      $arr=mysqli_fetch_assoc($qryEVC);
      if ($arr["used"] == 0) {
        $amt=$arr['amount'];
         $qry = mysqli_query($conn,"INSERT INTO customer(customer_id, password_hash, address, property_type, bedroom_num,balance,type) VALUES ('$email', '$password', '$address', '$property_type', '$no_of_bedrooms','$amt','customer')");
         if($qry){
            $id=mysqli_insert_id($conn);
            $v_update=mysqli_query($conn,"UPDATE voucher SET used='1',amount='0' WHERE EVC_code='$evc'");
            if($v_update){
               $response["status"]=1;
               $response["message"]="Successfully Registered customer";
               $_SESSION['cid']=$id;
               $_SESSION['email']=$email;
               $_SESSION['type']='customer';
            }
         }else{
            $response["message"]="Oops there is a problem...please try again..";
         }
      } else {
        $response["message"] = "Another customer has already used the provided EVC..!!!";
      }
    }else{
      $response["message"] = "Invalid Energy voucher Card";
    }
  } 
  echo json_encode($response);
}
//LOGIN PART

if (isset($_POST["action"]) && $_POST["action"] == "loginForm") {
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $email = $_POST["email"];
  $ps=$_POST["pass"];
  $password =passwordHash($_POST["pass"]);
  $result = mysqli_query($conn, "SELECT id,customer_id,type FROM customer WHERE customer_id='$email'");
  if($result){
    $row_count = mysqli_num_rows($result);
    if ($row_count == 1) {
       $arr=mysqli_fetch_assoc($result);
       $id=$arr['id'];
       $type=$arr['type'];
       $pass=mysqli_query($conn,"SELECT password_hash FROM customer WHERE id='$id'");
       $arrPass=mysqli_fetch_assoc($pass);
       if($arrPass["password_hash"] == $password){
           $_SESSION["cid"] = $id;
           $_SESSION["email"] = $email;
           $_SESSION['type']=$type;
            if(!empty($_POST["rem"]))
            {
                 setcookie('cid',$email,time()+365*24*60*60,'/','','',true);
                 setcookie('ps',$ps,time()+365*24*60*60,'/','','',true);
            }
            else{
              if(isset($_COOKIE['cid'])){
                setcookie('cid','');
              }
              if(isset($_COOKIE['ps'])){
                setcookie('ps','');
              }
            }
           $response["status"]=1;
           $response["message"]="Successfully Login...";
       }else{
          $response["message"] = "Invalid Password..!!!";
       }    
    } else {
      $response["message"] = "Invalid User Email..!!!!";
    }
  }
  echo json_encode($response);
}


//METER READING PART

if (isset($_POST["action"]) && $_POST["action"] == "MeterReadingForm") {
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $customer_id = $_POST["customer_id"];
  $submitted_date = $_POST["submit_date"];
  $meter_day = $_POST["m_day"];
  $meter_night = $_POST["m_night"];
  $gas_meter = $_POST["g_meter"];
  $total_due=$total_day_reading=$total_night_reading=$total_gas_reading=0;
  // $date1=$date2="00-00-0000";
  $total_bill=0;
  $qry=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id ='$customer_id' ORDER BY submission_date DESC LIMIT 1");
  if(mysqli_num_rows($qry) == 0){
     $previous_date=date('d-m-Y',strtotime('-1 months',strtotime($submitted_date)));
     $total_bill=$total_bill=(($meter_day) * tariffPrice($conn,'electricity_day') )+(($meter_night) * tariffPrice($conn,'electricity_night')) +( ($gas_meter) * tariffPrice($conn,'gas')) + (tariffPrice($conn,'sanding_charge') * dateDiffInDays($submitted_date,$previous_date));
  }else{
       $arrBills=mysqli_fetch_assoc($qry);
       $total_day_reading=$arrBills["elec_readings_day"];
       $total_night_reading=$arrBills["elet_reading_night"];
       $total_gas_reading=$arrBills["gas_reading"];
       $previous_date=date("d-m-Y",strtotime($arrBills["submission_date"]));
       $total_bill=(($meter_day-$total_day_reading) * tariffPrice($conn,'electricity_day') )+(($meter_night-$total_night_reading) * tariffPrice($conn,'electricity_night')) +( ($gas_meter-$total_gas_reading) * tariffPrice($conn,'gas')) + (tariffPrice($conn,'sanding_charge') * dateDiffInDays($submitted_date,$previous_date));
  }
  $sql = "INSERT INTO reading(customer_id,submission_date, elec_readings_day, elet_reading_night, gas_reading,total_bill,status) VALUES ('$customer_id','$submitted_date', '$meter_day', '$meter_night', '$gas_meter','$total_bill','Pending')";
  $result = mysqli_query($conn, $sql);
  if ($result){
    $response["status"] = 1;
    $response["message"] = "Successfully added Meter Reading..!!!";
  }else{
    $response["message"] = "Oops there is Problem while adding reading...!!!";
  }
  echo json_encode($response);
}
if(isset($_POST["payMonthly"])){
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $payID=$_POST["payID"];
  $bill=$_POST["bill"];
  $email=$_POST["email"];
  $balanceQry=mysqli_query($conn,"SELECT balance FROM customer WHERE customer_id='$email'");
  $balanceArr=mysqli_fetch_assoc($balanceQry);
  $balance=$balanceArr['balance'];
  $total=$balance-$bill;
  $qry=mysqli_query($conn,"UPDATE reading SET status='Paid' WHERE reading_id='$payID' AND customer_id='$email'");
  if($qry){
    $updateCus=mysqli_query($conn,"UPDATE customer SET balance='$total' WHERE customer_id='$email'");
    if($updateCus){
        $response["status"] = 1;
        $response["message"] = "Bill Paid Successfully..!!!";
    }else{
       $response["message"] = "Oops Something went wrong...!!!";
    }
  }
 echo json_encode($response);
}
if(isset($_POST["payFull"])){
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $countBill=$_POST["countBill"];
  $email=$_POST["email"];
  $balanceQry=mysqli_query($conn,"SELECT balance FROM customer WHERE customer_id='$email'");
  $balanceArr=mysqli_fetch_assoc($balanceQry);
  $balance=$balanceArr['balance'];
  $total=$balance-$countBill;
  $qry=mysqli_query($conn,"UPDATE reading SET status='Paid' WHERE  customer_id='$email'");
  if($qry){
    $updateCus=mysqli_query($conn,"UPDATE customer SET balance='$total' WHERE customer_id='$email'");
    if($updateCus){
        $response["status"] = 1;
        $response["message"] = "Bill Paid Successfully..!!!";
    }else{
       $response["message"] = "Oops Something went wrong...!!!";
    }
  }
 echo json_encode($response);
}

if(isset($_POST["fetchBill"])){
  $response=[];
  $email=$_POST['email'];
  $qryTable=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$email' ORDER BY submission_date DESC");
  if($qryTable){
    $rowsTable=mysqli_num_rows($qryTable);
    if($rowsTable > 0){
      while($arrTable=mysqli_fetch_assoc($qryTable)){
         $output=[
            "reading_id"=>$arrTable['reading_id'],
            "submission_date"=>$arrTable['submission_date'],
            "elec_readings_day"=>$arrTable['elec_readings_day'],
            "elet_reading_night"=>$arrTable['elet_reading_night'],
            "gas_reading"=>$arrTable['gas_reading'],
            "total_bill"=>$arrTable['total_bill'],
            "status"=>$arrTable['status'],
         ];

         $response[]=$output;
      }
    }
  }
      echo json_encode($response);
}
if(isset($_POST["voucher_recharge"])){
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $vid=$_POST["vid"];
  $email=$_POST['email'];
  $qry=mysqli_query($conn,"SELECT * FROM voucher WHERE EVC_code='$vid'");
  if($qry){
    $rows=mysqli_num_rows($qry);
    if($rows > 0){
      $arr=mysqli_fetch_assoc($qry);
      if($arr['used']== 0){
         $amount=$arr['amount'];
         $uCusBlnc=mysqli_query($conn,"UPDATE customer SET balance='$amount' WHERE customer_id='$email'");
         if($uCusBlnc){
           $update=mysqli_query($conn,"UPDATE voucher SET used='1',amount='0' WHERE EVC_code='$vid'");
           if($update){
             $response["status"]=1;
             $response["message"]="Added EVC code...";
           }else{
             $response["message"]="There is a problem while added EVC code..";
           }
         }else{
           $response["message"]="Oops there is a problem..Please try again..";
         }
      }else{
        $response["message"]="This EVC code is already used...!!!";
      }
    }else{
      $response["message"]="Invalid EVC code..!!!";
    }
  }
  echo json_encode($response);
}
if(isset($_POST["action"]) && $_POST['action'] == 'setTariffForm'){
  $response = [
    "status" => 0,
    "message" => "Oops Something Went Wrong..."
  ];
  $meter_day = $_POST["m_day_unit"];
  $meter_night = $_POST["m_night_unit"];
  $gas_meter = $_POST["g_meter_unit"];
  $standing=$_POST["standing_price"];
  $u1=mysqli_query($conn,"UPDATE taiff SET rate='$meter_day' WHERE taiff_type='electricity_day'");
  if($u1){
     $u2=mysqli_query($conn,"UPDATE taiff SET rate='$meter_night' WHERE taiff_type='electricity_night'");
     if($u2){
        $u3=mysqli_query($conn,"UPDATE taiff SET rate='$gas_meter' WHERE taiff_type='gas'");
        if($u3){
          $u4=mysqli_query($conn,"UPDATE taiff SET rate='$standing' WHERE taiff_type='sanding_charge'");
          if($u4){
             $response["status"]=1;
             $response["message"]="SuccessFully Updated Price";
          }else{
             $response["message"]="Something Went wrong While updating Standing Price..";
          }
        }else{
          $response["message"]="Something Went wrong While setting Gas unit price per KWh.";
        }
     }else{
        $response["message"]="Something Went wrong While setting electricity night unit price per KWh.";
     }
  }else{
    $response["message"]="Something Went wrong While setting electricity Day unit price per KWh.";
  }
  echo json_encode($response);
}
if(isset($_POST["fetchBalance"])){
  $email=$_POST['email'];
  $qry=mysqli_query($conn,"SELECT * FROM customer WHERE customer_id='$email'");
  $arr=mysqli_fetch_assoc($qry);
  echo json_encode($arr['balance']);
}



