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
require_once "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";
dbconn(false);
loggedinorreturn();
$lang = array_merge(load_language('global'));
    $HTMLOUT = "";
if (isset($_GET['echeck']) && $_GET['echeck'] == 1) {
    $HTMLOUT.= begin_main_frame();
    $HTMLOUT.= "<div align='center'>
    <br />
    <table width='80%' border='0' align='center'>
    <tr><td align='center' valign='middle' class='donation'><h1>Pending Payment!</h1></td></tr>
    <tr><td align='center' valign='middle' class='one'><br />
    <b>Thank you for your support {$CURUSER["username"]}!</b><br /><br />Your e-check is <font color='red'>pending</font>.
    Upon confirmation of your payment you will recieve your bonus and VIP status. <br /><br />cheers,<br />{$FMED['site_name']} Staff</td></tr></table></div><br /><br /><br />";
    $HTMLOUT.= end_main_frame();
    echo stdhead('Donate').$HTMLOUT.stdfoot();
    die();
}
    $HTMLOUT.= begin_main_frame();
    $HTMLOUT.= "<div align='center'><br /><table width='80%' border='0' align='center'>
    <tr><td align='center' valign='middle' class='donation'><h1>Success!</h1></td></tr>
    <tr><td align='center' valign='middle' class='one'><br /><b>Thank you for your support {$CURUSER["username"]}!</b><br /><br />
    It's people like you that make it better for the whole community :)<br /><br />cheers,<br />{$FMED['site_name']} Staff</td></tr></table></div><br /><br /><br />";
    $HTMLOUT.= end_main_frame();
echo stdhead('Donate').$HTMLOUT.stdfoot();
die();
?>
