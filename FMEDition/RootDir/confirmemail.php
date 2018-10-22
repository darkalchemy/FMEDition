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
    $lang = array_merge( load_language('global'), load_language('confirmemail') );
if ( !isset($_GET['uid']) OR !isset($_GET['key']) OR !isset($_GET['email']) )
    stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_idiot']}");
if (! preg_match( "/^(?:[\d\w]){32}$/", $_GET['key'] ) ){
    stderr( "{$lang['confirmmail_user_error']}", "{$lang['confirmmail_no_key']}" );
}
if (! preg_match( "/^(?:\d){1,}$/", $_GET['uid'] ) ){
    stderr( "{$lang['confirmmail_user-error']}", "{$lang['confirmmail_no_id']}" );
}
    $id = intval($_GET['uid']);
    $md5 = $_GET['key'];
    $email = urldecode($_GET['email']);
if( !validemail($email) )
    stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_false_email']}");
dbconn();
global $CURUSER;
if (!$CURUSER) {
    get_template();
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT editsecret FROM users WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
if (!$row)
    stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");
    $sec = $row['editsecret'];
if (preg_match('/^ *$/s', $sec))
    stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");
if ($md5 != md5($sec . $email . $sec))
    stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET editsecret='', email=" . sqlesc($email) . " WHERE id=$id AND editsecret=" . sqlesc($row["editsecret"]));
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");
    header("Refresh: 0; url={$FMED['baseurl']}/usercp.php?emailch=1");
?> 