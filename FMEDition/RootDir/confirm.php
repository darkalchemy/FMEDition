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
require_once "include/user_functions.php";

    $lang = array_merge( load_language('global'), load_language('confirm') );
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $md5 = isset($_GET['secret']) ? $_GET['secret'] : '';
if (!is_valid_id($id))
    stderr("{$lang['confirm_user_error']}", "{$lang['confirm_invalid_id']}");
if (! preg_match( "/^(?:[\d\w]){32}$/", $md5 ) ){
    stderr("{$lang['confirm_user_error']}", "{$lang['confirm_invalid_key']}");
}
dbconn();
global $CURUSER;
if (!$CURUSER) {
    get_template();
}
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT passhash, editsecret, status FROM users WHERE id = $id");
    $row = @mysqli_fetch_assoc($res);
if (!$row)
    stderr("{$lang['confirm_user_error']}", "{$lang['confirm_invalid_id']}");
if ($row['status'] != 'pending'){
    header("Refresh: 0; url={$FMED['baseurl']}/ok.php?type=confirmed");
    exit();
}
    $sec = $row['editsecret'];
if ($md5 != $sec)
    stderr("{$lang['confirm_user_error']}", "{$lang['confirm_cannot_confirm']}");
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET status='confirmed', editsecret='' WHERE id=$id AND status='pending'");
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    stderr("{$lang['confirm_user_error']}", "{$lang['confirm_cannot_confirm']}");
    logincookie($id, $row['passhash']);
    header("Refresh: 0; url={$FMED['baseurl']}/ok.php?type=confirm");
?> 