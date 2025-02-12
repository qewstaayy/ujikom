<?php

session_start();
session_destroy();
header("Location: ujikom/login.php");
exit();

?>