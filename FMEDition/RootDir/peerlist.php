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
require_once "include/bt_client_functions.php";
require_once "include/html_functions.php";

dbconn(false);
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('peerlist') );
    $id = (int)$_GET['id'];
if (!isset($id) || !is_valid_id($id))
    stderr($lang['edit_user_error'], $lang['peerslist_invalid_id']);
    $HTMLOUT = '';
function dltable($name, $arr, $torrent){
global $CURUSER, $lang;
    $htmlout = '';
if (!count($arr))
    return $htmlout = "<div align='left'><b>{$lang['peerslist_no']} $name {$lang['peerslist_data_available']}</b></div>\n";
    $htmlout = "\n";
    $htmlout .= "<table width='98%' class='main' border='1' cellspacing='0' cellpadding='5'>\n";
    $htmlout .= "<tr><td colspan='11'>" . count($arr) . " $name</td>
    </tr>
    " ."
    <tr>
    <td>{$lang['peerslist_user_ip']}</td>
    " ."
    <td align='center'>{$lang['peerslist_connectable']}</td>
    "."
    <td align='right'>{$lang['peerslist_uploaded']}</td>
    "."
    <td align='right'>{$lang['peerslist_rate']}</td>
    "."
    <td align='right'>{$lang['peerslist_downloaded']}</td>
    " ."
    <td align='right'>{$lang['peerslist_rate']}</td>
    " ."
    <td align='right'>{$lang['peerslist_ratio']}</td>
    " ."
    <td align='right'>{$lang['peerslist_complete']}</td>
    " ."
    <td align='right'>{$lang['peerslist_connected']}</td>
    " ."
    <td align='right'>{$lang['peerslist_idle']}</td>
    " ."
    <td align='left'>{$lang['peerslist_client']}</td></tr>\n";

    $now = time();
    foreach ($arr as $e) {
if ($e["privacy"] == "strong") continue;
    $htmlout .= "<tr>\n";
if ($e["username"])
    $htmlout .= "<td><a href='userdetails.php?id=$e[userid]'><b><font color='#" . get_user_class_color($e['class']) . "'> " . htmlspecialchars($e['username']) . "</font></b></a>".get_user_icons($e)."</td>\n";
    else
    $htmlout .= "<td>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
    $secs = max(1, ($now - $e["st"]) - ($now - $e["la"]));
    $htmlout .= "<td align='center'>" . ($e['connectable'] == "yes" ? "{$lang['peerslist_yes']}" : "<font color='red'>{$lang['peerslist_no']}</font>") . "</td>\n";
    $htmlout .= "<td align='right'>" . mksize($e["uploaded"]) . "</td>\n";
    $htmlout .= "<td align='right'><span style=\"white-space: nowrap;\">" . mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs) . "/s</span></td>\n";
    $htmlout .= "<td align='right'>" . mksize($e["downloaded"]) . "</td>\n";
if ($e["seeder"] == "no")
    $htmlout .= "<td align='right'><span style=\"white-space: nowrap;\">" . mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs) . "/s</span></td>\n";
    else
    $htmlout .= "<td align='right'><span style=\"white-space: nowrap;\">" . mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e['st'])) .    "/s</span></td>\n";
if ($e["downloaded"]){
    $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
    $htmlout .= "<td align=\"right\"><font color='" . get_ratio_color($ratio) . "'>" . number_format($ratio, 3) . "</font></td>\n";
    }else
if ($e["uploaded"])
    $htmlout .= "<td align='right'>{$lang['peerslist_inf']}</td>\n";
    else
    $htmlout .= "<td align='right'>---</td>\n";
    $htmlout .= "<td align='right'>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
    $htmlout .= "<td align='right'>" . mkprettytime($now - $e["st"]) . "</td>\n";
    $htmlout .= "<td align='right'>" . mkprettytime($now - $e["la"]) . "</td>\n";
    $htmlout .= "<td align='left'>" . htmlspecialchars(getagent($e["agent"], $e['peer_id'])) . "</td>\n";
    $htmlout .= "</tr>\n";
}
    $htmlout .= "</table>\n";
    return $htmlout;
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM torrents WHERE id = $id") or sqlerr();
if(mysqli_num_rows($res) == 0)
    stderr("{$lang['peerslist_error']}", "{$lang['peerslist_nothing']}");
    $row = mysqli_fetch_assoc($res);
    $downloaders = array();
    $seeders = array();
    $subres = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT u.username, u.class, u.donor, u.warned, u.enabled, u.privacy, p.seeder, p.finishedat, p.downloadoffset, p.uploadoffset, p.ip, p.port, p.uploaded, p.downloaded, p.to_go, p.started AS st, p.connectable, p.agent, p.last_action AS la, p.userid, p.peer_id FROM peers p LEFT JOIN users u ON p.userid = u.id WHERE p.torrent = $id") or sqlerr();
if(mysqli_num_rows($subres) == 0)
    stderr("{$lang['peerslist_warning']}", "{$lang['peerslist_no_data']}");
    while ($subrow = mysqli_fetch_assoc($subres)) {
if ($subrow["seeder"] == "yes")
    $seeders[] = $subrow;
    else
    $downloaders[] = $subrow;
}
function leech_sort($a,$b) {
if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);
    $x = $a["to_go"];
    $y = $b["to_go"];
if ($x == $y)
    return 0;
if ($x < $y)
    return -1;
    return 1;
}
function seed_sort($a,$b) {
    $x = $a["uploaded"];
    $y = $b["uploaded"];
if ($x == $y)
    return 0;
if ($x < $y)
    return 1;
    return -1;
}
    usort($seeders, "seed_sort");
    usort($downloaders, "leech_sort");
    $HTMLOUT .= "<h1>Peerlist for <a href='{$FMED['baseurl']}/details.php?id=$id'>".htmlentities($row['name'])."</a></h1>";
    $HTMLOUT .= dltable("{$lang['peerslist_seeders']}<a name='seeders'></a>", $seeders, $row);
    $HTMLOUT .= '<br />' . dltable("{$lang['peerslist_leechers']}<a name='leechers'></a>", $downloaders, $row);
    print stdhead("{$lang['peerslist_stdhead']}") . $HTMLOUT . stdfoot();
?> 