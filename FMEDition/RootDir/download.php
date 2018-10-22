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
dbconn();
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('download') );
    $id = isset($_GET['torrent']) ? intval($_GET['torrent']) : 0;
if ( !is_valid_id($id) )
    stderr("{$lang['download_user_error']}", "{$lang['download_no_id']}");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT name, vip, filename FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_assoc($res);
    $fn = "{$FMED['torrent_dir']}/$id.torrent";
if (!$row || !is_file($fn) || !is_readable($fn))
    httperr();
if ($row["vip"] == 'yes' && get_user_class() < UC_VIP)
    stderr("{$lang['vip']}", "{$lang['vip1']}");
if ($CURUSER["downloadpos"] == 'no'){
    stderr("{$lang['sorry']}", "{$lang['yourrights']}");
}
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET hits = hits + 1 WHERE id = $id");
require_once "include/benc.php";
if (!isset($CURUSER['passkey']) || strlen($CURUSER['passkey']) != 32){
    $CURUSER['passkey'] = md5($CURUSER['username'].time().$CURUSER['passhash']);
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET passkey='{$CURUSER['passkey']}' WHERE id={$CURUSER['id']}");
}
  $dict = bdec_file($fn, filesize($fn));
  $dict['value']['announce']['value'] = "{$FMED['announce_urls'][0]}?passkey={$CURUSER['passkey']}";
  $dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];
  $dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
  header('Content-Disposition: attachment; filename="[' . $FMED['site_name'] . ']' . $row['filename'] . '"');
  header("Content-Type: application/x-bittorrent");
  print(benc($dict));
?>