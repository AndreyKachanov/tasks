<?php

require_once "config.php";
session_start();
$rout = new M_Rout($_GET['q']);
$rout->Request();