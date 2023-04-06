<?php 
$conn = mysqli_connect("localhost", "root", "", "igse");
if (!$conn) {
    die("Connection failed:" . mysqli_connect_error());
}
?>