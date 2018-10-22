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
require_once "include/user_functions.php";
dbconn();
loggedinorreturn();

if ( get_user_class() < UC_SYSOP)
    header( "Location: {$FMED['baseurl']}/index.php" );
    $lang = array_merge( load_language('global') );
    $signup = isset($_GET['signup']) ? intval($_GET['signup']) : 0;
    $htmlout = '';
    $htmlout .= "<table width='100%' class='main' border='1' cellspacing='0' cellpadding='5'>\n";
if ($signup != ""){
if ($signup == 1){
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE openclose SET value = '1' WHERE name = 'signup'");
    write_log("Registrations open by $CURUSER[username]");
}
if ($signup == 2){
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE openclose SET value = '0' WHERE name = 'signup'");
    write_log("Registration closed by $CURUSER[username]");
    }
}
    $signup = mysqli_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT value FROM openclose WHERE name = 'signup'"),  0);
if ($signup == 0){
    $htmlout .= "<td align='center'>Registration is already closed<br/><a href=?signup=1>Click here to open registration!</a></td>\n";
    }else{
    $htmlout .= "<td align='center'>Registration is already open<br/><a href=?signup=2>Click here to close Registrations!</a></td>\n";
}
    $htmlout .= "</table>\n";
print stdhead("Close registrations") . $htmlout . stdfoot();
?>