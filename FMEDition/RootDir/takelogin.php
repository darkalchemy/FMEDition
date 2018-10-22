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
require_once 'include/bittorrent.php';
require_once "include/password_functions.php";

if (!mkglobal('username:password:captcha'))
    die();
    session_start();
if(empty($captcha) || $_SESSION['captcha_id'] != strtoupper($captcha)){
    header('Location: login.php');
    exit();
}
dbconn();
global $CURUSER;
if(!$CURUSER){
get_template();
}
    $lang = array_merge( load_language('global'), load_language('takelogin') );
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
    $row = mysqli_fetch_assoc($res);
if (!$row)
    stderr($lang['tlogin_failed'], 'Username or password incorrect');
if ($row['passhash'] != make_passhash( $row['secret'], md5($password) ) )
    stderr($lang['tlogin_failed'], 'Username or password incorrect');
if ($row['enabled'] == 'no')
    stderr($lang['tlogin_failed'], $lang['tlogin_disabled']);
    logincookie($row['id'], $row['passhash']);
    header("Location: {$FMED['baseurl']}/index.php");
?> 