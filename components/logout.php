<?php

// clear session
session_start();
session_unset();
session_destroy();
header("Location:login.php");

?>