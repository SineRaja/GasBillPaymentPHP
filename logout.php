<?php 
    session_start();
    // remove all session variables
    session_destroy();
    // redirect to home page
    header("Location: index.php");
