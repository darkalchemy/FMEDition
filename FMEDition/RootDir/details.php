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
require_once "include/bbcode_functions.php";
require_once "include/pager_functions.php";
require_once "include/torrenttable_functions.php";
require_once "include/html_functions.php";

function ratingpic($num) {
    global $FMED;
    $r = round($num * 2) / 2;
if ($r < 1 || $r > 5)
    return;
    return "<img src=\"{$FMED['pic_base_url']}{$r}.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
}
dbconn(false);
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('details') );
if (!isset($_GET['id']) || !is_valid_id($_GET['id']))
    stderr("{$lang['details_user_error']}", "{$lang['details_bad_id']}");
    $id = (int)$_GET["id"];
if (isset($_GET["hit"])){
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET views = views + 1 WHERE id = $id");
    header("Location: {$FMED['baseurl']}/details.php?id=$id");
    exit();
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.free, torrents.box, torrents.vip, torrents.poster, torrents.tube, torrents.filename, LENGTH(torrents.nfo) AS nfosz, torrents.last_action AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < {$FMED['minvotes']}, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.comments, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, categories.name AS cat_name, users.username FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id") or sqlerr();
    $row = mysqli_fetch_assoc($res);
    $owned = $moderator = 0;
if (get_user_class() >= UC_MODERATOR)
    $owned = $moderator = 1;
    elseif ($CURUSER["id"] == $row["owner"])
    $owned = 1;
//}
if (!$row || ($row["banned"] == "yes" && !$moderator))
    stderr("{$lang['details_error']}", "{$lang['details_torrent_id']}");
    $HTMLOUT = '';
if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
    $owned = 1;
    else
    $owned = 0;
    $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if (isset($_GET["uploaded"])) {
    $HTMLOUT .= "<h2>{$lang['details_success']}</h2>\n";
    $HTMLOUT .= "<p>{$lang['details_start_seeding']}</p>\n";
    }elseif (isset($_GET["edited"])) {
    $HTMLOUT .= "<h2>{$lang['details_success_edit']}</h2>\n";
if (isset($_GET["returnto"]))
    $HTMLOUT .= "<p><b>{$lang['details_go_back']}<a href='" . htmlspecialchars($_GET["returnto"]) . "'>{$lang['details_whence']}</a>.</b></p>\n";
    }elseif (isset($_GET["rated"]))
    $HTMLOUT .= "<h2>{$lang['details_rating_added']}</h2>\n";
    $s = htmlentities( $row["name"], ENT_QUOTES );
    $HTMLOUT .= "<table width='98%' border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";
    $url = "edit.php?id=" . $row["id"];
if (isset($_GET["returnto"])) {
    $addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
    $url .= $addthis;
    $keepget = $addthis;
}
    $editlink = "a href=\"$url\" class=\"sublink\"";
    $HTMLOUT .= "<br /><td class='rowhead'>Name</td><td align='left'>$s</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead' width='1%'>{$lang['details_download']}</td>
    <td width='99%' align='left'><a class='index' href='download.php?torrent=$id'>" . htmlspecialchars($row["filename"]) . "</a></td></tr>";
if($row['box'] == 'yes') {
    $HTMLOUT.= "<tr>
    <td class='rowhead'>{$lang['details_seedbox']}</td>
    <td align='left'>
    <img src='{$FMED['pic_base_url']}webseeder.png' border='0' alt='Seedbox' title='Seedbox' />
    &nbsp;&nbsp;
    {$lang['details_seedbox2']}</td></tr>\n";
}
if($row['free'] == 'yes') {
    $HTMLOUT.= "<tr>
    <td class='rowhead'>{$lang['details_freeleach']}</td>
    <td align='left'>
    <img src='{$FMED['pic_base_url']}freedownload.gif' border='0' alt='freedownload' title='freedownload' />
    &nbsp;&nbsp;
    {$lang['details_freeleach2']}</td></tr>\n";
}
if($row['vip'] == 'yes') {
    $HTMLOUT.= "<tr>
    <td class='rowhead'>{$lang['details_vip']}</td>
    <td align='left'>
    <img src='{$FMED['pic_base_url']}vip.gif' border='0' alt='freedownload' title='freedownload' />
    &nbsp;&nbsp;
    {$lang['details_vip2']}</td></tr>\n";
}
    $HTMLOUT .= tr("{$lang['details_info_hash']}", $row["info_hash"]);
if (!empty($row["descr"]))
    $HTMLOUT .= "<tr>
    <td style='vertical-align:top'>{$lang['details_description']}</td>
    <td><div style='background-color:#d9e2ff;width:100%;height:150px;overflow: auto'>". str_replace(array("\n", "  "), array("<br />\n", "&nbsp; "), format_comment( $row["descr"] ))."</div></td></tr>";
if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['details_nfo']}</td>
    <td align='left'><a href='viewnfo.php?id=$row[id]'><b>{$lang['details_view_nfo']}</b></a> (" .mksize($row["nfosz"]) . ")</td></tr>\n";
if ($row["visible"] == "no")
    $HTMLOUT .= tr("{$lang['details_visible']}", "<b>{$lang['details_no']}</b>{$lang['details_dead']}", 1);
if ($moderator)
    $HTMLOUT .= tr("{$lang['details_banned']}", $row["banned"]);
if (isset($row["cat_name"]))
    $HTMLOUT .= tr("{$lang['details_type']}", $row["cat_name"]);
    else
    $HTMLOUT .= tr("{$lang['details_type']}", "{$lang['details_none']}");
    $HTMLOUT .= tr("{$lang['details_last_seeder']}", "{$lang['details_last_activity']}" .get_date( $row['lastseed'],'',0,1));

if (!empty($row["poster"]))
    $HTMLOUT.= "<tr>
    <td class='rowhead'>{$lang['details_poster']}</td>
    <td align='left'>
    <img src='".$row["poster"]."'width='150' height='220' border='0' alt='Poster' title='Poster' /></td></tr>\n";
    else
    $HTMLOUT.= "<tr>
    <td class='rowhead'>{$lang['details_poster']}</td>
    <td align='left'>
    <img src='{$FMED['pic_base_url']}noposter.png' width='150' height='220' border=\'0\' alt='Poster' title='Poster' /></td></tr>\n";
if (!empty($row["tube"])){
    $HTMLOUT.= "<tr>
    <td class='rowhead'>Trailer</td>
    <td align='left'>
    <a href='".$row["tube"]."'</a>
    <object width='426' height='234'>
    <embed src='". str_replace("watch?v=", "v/", htmlspecialchars($row["tube"])) ."' type='application/x-shockwave-flash' width='426' height='234'></embed></object></td></tr>";
}



    $HTMLOUT .= tr("{$lang['details_size']}",mksize($row["size"]) . " (" . number_format($row["size"]) . "{$lang['details_bytes']})");
    $HTMLOUT .= tr("{$lang['details_added']}", get_date( $row['added'],"{$lang['details_long']}"));
    $HTMLOUT .= tr("{$lang['details_views']}", $row["views"]);
    $HTMLOUT .= tr("{$lang['details_hits']}", $row["hits"]);
    $HTMLOUT .= tr("{$lang['details_snatched']}", $row["times_completed"] . "{$lang['details_times']}");
    $uprow = (isset($row["username"]) ? ("<a href='userdetails.php?id=" . $row["owner"] . "'><b>" . htmlspecialchars($row["username"]) . "</b></a>") : "<i>{$lang['details_unknown']}</i>");
if ($owned)
    $uprow .= " $spacer<$editlink><b>{$lang['details_edit']}</b></a>";
    $HTMLOUT .= tr("Upped by", $uprow, 1);
if ($row["type"] == "multi") {
if (!isset($_GET["filelist"]))
    $HTMLOUT .= tr("{$lang['details_num_files']}<br /><a href=\"filelist.php?id=$id\" class=\"sublink\">{$lang['details_list']}</a>", $row["numfiles"] . " files", 1);
    else {
    $HTMLOUT .= tr("{$lang['details_num-files']}", $row["numfiles"] . "{$lang['details_files']}", 1);
    }
}
    $HTMLOUT .= tr("{$lang['details_peers']}<br /><a href=\"peerlist.php?id=$id#seeders\" class=\"sublink\">{$lang['details_list']}</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . "{$lang['details_peer_total']}", 1);
    $HTMLOUT .= "</table>";
    $HTMLOUT .= "<h1>{$lang['details_comments']}<a href='details.php?id=$id'>" . htmlentities( $row["name"], ENT_QUOTES ) . "</a></h1>\n";
    $HTMLOUT .= "<p><a name=\"startcomments\"></a></p>\n";
    $commentbar = "<p align='center'><a class='index' href='comment.php?action=add&amp;tid=$id'>{$lang['details_add_comment']}</a></p>\n";
    $count = $row['comments'];
if (!$count){
    $HTMLOUT .= "<h2>{$lang['details_no_comment']}</h2>\n";
    }else{
    $pager = pager(20, $count, "details.php?id=$id&amp;", array('lastpagedefault' => 1));
    $subres = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, av_w, av_h, warned, username, title, class, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $id ORDER BY comments.id ".$pager['limit']) or sqlerr(__FILE__, __LINE__);
    $allrows = array();
    while ($subrow = mysqli_fetch_assoc($subres))
    $allrows[] = $subrow;
    $HTMLOUT .= $commentbar;
    $HTMLOUT .= $pager['pagertop'];
    $HTMLOUT .= commenttable($allrows);
    $HTMLOUT .= $pager['pagerbottom'];
}
    $HTMLOUT .= $commentbar;
    print stdhead("{$lang['details_details']}\"" . htmlentities($row["name"], ENT_QUOTES) . "\"") . $HTMLOUT . stdfoot();
?> 