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
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('takerate') );
if (!isset($CURUSER))
    stderr("{$lang['rate_fail']}", "{$lang['rate_login']}");
if (!mkglobal("rating:id"))
    stderr("{$lang['rate_fail']}", "{$lang['rate_miss_form_data']}");
    $id = 0 + $id;
if (!$id)
    stderr("{$lang['rate_fail']}", "{$lang['rate_invalid_id']}");
    $rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
    stderr("{$lang['rate_fail']}", "{$lang['rate_invalid']}");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT owner FROM torrents WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
if (!$row)
    stderr("{$lang['rate_fail']}", "{$lang['rate_torrent_not_found']}");
    $time_now = time();
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, $time_now)");
if (!$res) {
if (mysqli_errno($GLOBALS["___mysqli_ston"]) == 1062)
    stderr("{$lang['rate_fail']}", "{$lang['rate_already_voted']}");
    else
    stderr("{$lang['rate_fail']}", mysqli_error($GLOBALS["___mysqli_ston"]));
}
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
header("Refresh: 0; url=details.php?id=$id&rated=1");
?> 