<?php
require_once("config.php");
redirect_to_login($_SESSION["appid"], $_SESSION["appkey"], $_SESSION["callback"]);
?>