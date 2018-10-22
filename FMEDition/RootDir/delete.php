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
    $lang = array_merge( load_language('global'), load_language('delete') );
if (!mkglobal("id"))
    stderr($lang['delete_failed'], $lang['delete_missing_data']);
    $id = 0 + $id;
if (!is_valid_id($id))
    stderr($lang['delete_failed'], $lang['delete_missing_data']);
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT name,owner,seeders FROM torrents WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
if (!$row)
    stderr("{$lang['delete_failed']}", "{$lang['delete_not_exist']}");
if ( $CURUSER["id"] != $row["owner"] && !$CURUSER['ismod'] )
    stderr("{$lang['delete_failed']}", "{$lang['delete_not_owner']}\n");
    $rt = 0 + $_POST["reasontype"];
if (!is_int($rt) || $rt < 1 || $rt > 5)
    stderr($lang['delete_failed'], $lang['delete_invalid']);
    $reason = $_POST["reason"];
if ($rt == 1)
    $reasonstr = "{$lang['delete_dead']}";
    elseif ($rt == 2)
    $reasonstr = "{$lang['delete_dupe']}" . ($reason[0] ? (": " . trim($reason[0])) : "!");
    elseif ($rt == 3)
    $reasonstr = "{$lang['delete_nuked']}" . ($reason[1] ? (": " . trim($reason[1])) : "!");
    elseif ($rt == 4){
if (!$reason[2])
    stderr("{$lang['delete_failed']}", "{$lang['delete_violated']}");
    $reasonstr = $FMED['site_name']."{$lang['delete_rules']}" . trim($reason[2]);
    }else{
if (!$reason[3])
    stderr("{$lang['delete_failed']}", "{$lang['delete_reason']}");
    $reasonstr = trim($reason[3]);
}
deletetorrent($id);
    write_log("{$lang['delete_torrent']} $id ({$row['name']}){$lang['delete_deleted_by']}{$CURUSER['username']} ($reasonstr)\n");
if (isset($_POST["returnto"]))
    $ret = "<a href='" . htmlspecialchars($_POST["returnto"]) . "'>{$lang['delete_go_back']}</a>";
    else
    $ret = "<a href='{$FMED['baseurl']}/index.php'>{$lang['delete_back_index']}</a>";
    $HTMLOUT = '';
    $HTMLOUT .= "<h2>{$lang['delete_deleted']}</h2><p><$ret</p>";
    print stdhead("{$lang['delete_deleted']}") . $HTMLOUT . stdfoot();

function deletetorrent($id) {
global $FMED;
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM torrents WHERE id = $id");
    foreach(explode(".","peers.files.comments.ratings") as $x)
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM $x WHERE torrent = $id");
    unlink("{$FMED['torrent_dir']}/$id.torrent");
}
?> 