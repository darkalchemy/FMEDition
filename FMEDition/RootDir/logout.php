<?php
/*
+------------------------------------------------
|   BitTorrent Tracker PHP
|   =============================================
|   by xblade
|   (c) 2017 - 2018
|   =============================================
|   Licence Info: GPL
+------------------------------------------------
*/
require_once("include/bittorrent.php");
dbconn();
logoutcookie();
Header("Location: {$FMED['baseurl']}/");
?>