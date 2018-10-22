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
require_once "include/html_functions.php";
require_once "include/bbcode_functions.php";
dbconn(false);
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('userdetails') );
function maketable($res){
global $FMED, $lang;
    $htmlout = '';
    $htmlout .= "<table class='main' border='1' cellspacing='0' cellpadding='5'>
    " ."
    <tr>
    <td class='colhead' align='center'>{$lang['userdetails_type']}</td>
    <td class='colhead'>{$lang['userdetails_name']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_size']}</td>
    <td class='colhead' align='right'>{$lang['userdetails_se']}</td>
    <td class='colhead' align='right'>{$lang['userdetails_le']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_upl']}</td>\n
    " ."
    <td class='colhead' align='center'>{$lang['userdetails_downl']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ratio']}</td></tr>\n";
    foreach ($res as $arr){
if ($arr["downloaded"] > 0){
    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
    $ratio = "<font color='" . get_ratio_color($ratio) . "'>$ratio</font>";
    }else
if ($arr["uploaded"] > 0)
    $ratio = "{$lang['userdetails_inf']}";
    else
    $ratio = "---";
    $catimage = "{$FMED['pic_base_url']}caticons/{$arr['image']}";
    $catname = htmlspecialchars($arr["catname"]);
    $catimage = "<img src=\"".htmlspecialchars($catimage) ."\" title=\"$catname\" alt=\"$catname\" width='42' height='42' />";
    $size = str_replace(" ", "<br />", mksize($arr["size"]));
    $uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
    $downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
    $seeders = number_format($arr["seeders"]);
    $leechers = number_format($arr["leechers"]);
    $htmlout .= "<tr>
    <td style='padding: 0px'>$catimage</td>\n
    " ."
    <td><a href='details.php?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr["torrentname"]) ."</b></a></td>
    <td align='center'>$size</td>
    <td align='right'>$seeders</td>
    <td align='right'>$leechers</td>
    <td align='center'>$uploaded</td>\n
    " ."
    <td align='center'>$downloaded</td>
    <td align='center'>$ratio</td></tr>\n";
}
    $htmlout .= "</table>\n";
    return $htmlout;
}
    $id = 0 + $_GET["id"];
if (!is_valid_id($id))
    stderr("{$lang['userdetails_error']}", "{$lang['userdetails_bad_id']}");
    $r = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE id=$id") or sqlerr();
    $user = mysqli_fetch_assoc($r) or stderr("{$lang['userdetails_error']}", "{$lang['userdetails_no_user']}");
if ($user["status"] == "pending") die;
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT t.id, t.name, t.seeders, t.leechers, c.name AS cname, c.image FROM torrents t LEFT JOIN categories c ON t.category = c.id WHERE t.owner = $id ORDER BY t.name") or sqlerr(__FILE__,__LINE__);
if (mysqli_num_rows($r) > 0){
    $torrents = "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n
    " ."
    <tr>
    <td class='colhead'>{$lang['userdetails_type']}</td>
    <td class='colhead'>{$lang['userdetails_name']}</td>
    <td class='colhead'>{$lang['userdetails_seeders']}</td>
    <td class='colhead'>{$lang['userdetails_leechers']}</td></tr>\n";
    while ($a = mysqli_fetch_assoc($r)){
    $cat = "<img src=\"". htmlspecialchars("{$FMED['pic_base_url']}caticons/{$a['image']}") ."\" title=\"{$a['cname']}\" alt=\"{$a['cname']}\" />";
    $torrents .= "<tr>
    <td style='padding: 0px'>$cat</td>
    <td><a href='details.php?id=" . $a['id'] . "&amp;hit=1'><b>" . htmlspecialchars($a["name"]) . "</b></a></td>
    " ."
    <td align='right'>{$a['seeders']}</td>
    <td align='right'>{$a['leechers']}</td></tr>\n";
}
    $torrents .= "</table>";
}
if ($user['ip'] && ($CURUSER['class'] >= UC_MODERATOR || $user['id'] == $CURUSER['id'])){
    $dom = @gethostbyaddr($user['ip']);
    $addr = ($dom == $user['ip'] || @gethostbyname($dom) != $user['ip']) ? $user['ip'] : $user['ip'].' ('.$dom.')';
}
if ($user['added'] == 0)
    $joindate = "{$lang['userdetails_na']}";
    else
    $joindate = get_date( $user['added'],'');
    $lastseen = $user["last_access"];
if ($lastseen == 0)
    $lastseen = "{$lang['userdetails_never']}";
    else{
    $lastseen = get_date( $user['last_access'],'',0,1);
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM comments WHERE user=" . $user['id']) or sqlerr();
    $arr3 = mysqli_fetch_row($res);
    $torrentcomments = $arr3[0];
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM posts WHERE userid=" . $user['id']) or sqlerr();
    $arr3 = mysqli_fetch_row($res);
    $forumposts = $arr3[0];
    $country = '';
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT name,flagpic FROM countries WHERE id=".$user['country']." LIMIT 1") or sqlerr();
if (mysqli_num_rows($res) == 1){
    $arr = mysqli_fetch_assoc($res);
    $country = "<td class='embedded'><img src=\"{$FMED['pic_base_url']}flag/{$arr['flagpic']}\" alt=\"". htmlspecialchars($arr['name']) ."\" style='margin-left: 8pt' /></td>";
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT p.torrent, p.uploaded, p.downloaded, p.seeder, t.added, t.name as torrentname, t.size, t.category, t.seeders, t.leechers, c.name as catname, c.image FROM peers p LEFT JOIN torrents t ON p.torrent = t.id LEFT JOIN categories c ON t.category = c.id WHERE p.userid=$id") or sqlerr();
    while ($arr = mysqli_fetch_assoc($res)){
if ($arr['seeder'] == 'yes')
    $seeding[] = $arr;
    else
    $leeching[] = $arr;
}
    $HTMLOUT = '';
    $enabled = $user["enabled"] == 'yes';
    $HTMLOUT .= "<p></p><table class='main' border='0' cellspacing='0' cellpadding='0'>
    "."
    <tr>
    <td class='embedded'><h1 style='margin:0px'>" . format_username($user, true) . "</h1></td>$country</tr></table><p></p>\n";
if (!$enabled)
    $HTMLOUT .= "<p><b>{$lang['userdetails_disabled']}</b></p>\n";
    elseif ($CURUSER["id"] <> $user["id"]){
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM friends WHERE userid=$CURUSER[id] AND friendid=$id") or sqlerr(__FILE__, __LINE__);
    $friend = mysqli_num_rows($r);
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM blocks WHERE userid=$CURUSER[id] AND blockid=$id") or sqlerr(__FILE__, __LINE__);
    $block = mysqli_num_rows($r);
if ($friend)
    $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>{$lang['userdetails_remove_friends']}</a>)</p>\n";
    elseif($block)
    $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=block&amp;targetid=$id'>{$lang['userdetails_remove_blocks']}</a>)</p>\n";
    else{
    $HTMLOUT .= "<p>(<a href='friends.php?action=add&amp;type=friend&amp;targetid=$id'>{$lang['userdetails_add_friends']}</a>)";
    $HTMLOUT .= " - (<a href='friends.php?action=add&amp;type=block&amp;targetid=$id'>{$lang['userdetails_add_blocks']}</a>)</p>\n";
    }
}
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= "<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
    <td class='rowhead' width='1%'>{$lang['userdetails_joined']}</td>
    <td align='left' width='99%'>{$joindate}</td></tr>
    <tr>
    <td class='rowhead'>{$lang['userdetails_seen']}</td>
    <td align='left'>{$lastseen}</td></tr>";
if ($user["donor"] && $CURUSER["id"] == $user["id"] || $CURUSER["class"] == UC_SYSOP) {
    $donoruntil = $user['donoruntil'];
if ($donoruntil == '0')
    $HTMLOUT.= "";
    else {
    $HTMLOUT .= "<tr>
    <td class='rowhead'>Donated&nbsp;Until</td>
    <td align='left'>
    &nbsp;-&nbsp;
    ".get_date($user['donoruntil'], 'DATE'). "";
    $HTMLOUT.=" [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go...
    <font size=\"-2\">
    To re-new your donation click
    <a class='altlink' href='{$FMED['baseurl']}/donate.php'>Here</a>.</font><br />\n";
    }
}
    $q1 = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT connectable, port,agent FROM peers WHERE userid = '.$id.' LIMIT 1') or sqlerr();
if ($a = mysqli_fetch_row($q1)){
    $connect = $a[0];
if ($connect == "yes"){
    $connectable = "<img src='{$FMED['pic_base_url']}aff_tick.gif' alt='Yes' title='Connectable' style='border:none;padding:2px;' /><font color='green'><b>{$lang['userdetails_yes']}</b></font>";
    }else{
    $connectable = "<img src='{$FMED['pic_base_url']}aff_cross.gif' alt='No' title='Not Connectable' style='border:none;padding:2px;' />
    <font color='red'><b>{$lang['userdetails_no']}</b></font>";
}
    }else{
    $connectable = "<img src='{$FMED['pic_base_url']}smilies/unsure.gif' alt='Unknown' title='Not connected To Peers' style='border:none;padding:2px;' />
    <font color='blue'><b>{$lang['userdetails_unknown']}</b></font>";
}
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_connectable']}</td>
    <td align='left'>".$connectable."</td></tr>";
    $port= $a[1];
    $agent = $a[2];
if (!empty($port))
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_port']}</td>
    <td align='left'>".$port."</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['userdetails_client']}</td>
    <td align='left'>".htmlentities($agent)."</td></tr>";
if ($CURUSER['class'] >= UC_MODERATOR)
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_email']}</td>
    <td align='left'><a href='{$FMED['baseurl']}/email-gateway.php?id={$user['id']}'>{$user['email']}</a></td></tr>\n";
if (isset($addr))
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_address']}</td>
    <td align='left'>$addr</td></tr>\n";
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_uploaded']}</td>
    <td align='left'>".mksize($user["uploaded"])."</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['userdetails_downloaded']}</td>
    <td align='left'>".mksize($user["downloaded"])."</td></tr>";
if ($user["downloaded"] > 0){
    $sr = $user["uploaded"] / $user["downloaded"];
if ($sr >= 4)
    $s = "w00t";
    else if ($sr >= 2)
    $s = "grin";
    else if ($sr >= 1)
    $s = "smile1";
    else if ($sr >= 0.5)
    $s = "noexpression";
    else if ($sr >= 0.25)
    $s = "sad";
    else
    $s = "cry";
    $sr = floor($sr * 1000) / 1000;
    $sr = "<table border='0' cellspacing='0' cellpadding='0'>
    <tr>
    <td class='embedded'><font color='" . get_ratio_color($sr) . "'>" . number_format($sr, 3) . "</font></td>
    <td class='embedded'>&nbsp;&nbsp;<img src=\"{$FMED['pic_base_url']}smilies/{$s}.gif\" alt='' /></td></tr></table>";
    $HTMLOUT .= "<tr>
    <td class='rowhead' style='vertical-align: middle'>Share ratio</td>
    <td align='left' valign='middle' style='padding-top: 1px; padding-bottom: 0px'>$sr</td></tr>\n";
}
if ($user["avatar"])
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_avatar']}</td>
    <td align='left'>
    <img src='" . htmlspecialchars($user["avatar"]) . "' width='{$user['av_w']}' height='{$user['av_h']}' alt='' /></td></tr>\n";
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_gender']}</td>
    <td align='left'>" . htmlspecialchars($user["gender"]) . "</td></tr>\n";
    $HTMLOUT .="<tr>
    <td class='rowhead'>{$lang['userdetails_dobBirthday']}</td>
    <td align='left'>" . htmlspecialchars($user["birthday"]) . "</td></tr>\n";
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_class']}</td>
    <td align='left'><img src=" . get_user_class_image($user["class"]) . " " . get_user_class_name($user["class"]) . "</td></tr>\n";
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_comments']}</td>";
if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || $CURUSER['class'] >= UC_MODERATOR))
    $HTMLOUT .= "<td align='left'><a href='userhistory.php?action=viewcomments&amp;id=$id'>$torrentcomments</a></td></tr>\n";
    else
    $HTMLOUT .= "<td align='left'>$torrentcomments</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_posts']}</td>";
if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || $CURUSER['class'] >= UC_MODERATOR))
    $HTMLOUT .= "<td align='left'><a href='userhistory.php?action=viewposts&amp;id=$id'>$forumposts</a></td></tr>\n";
    else
    $HTMLOUT .= "<td align='left'>$forumposts</td></tr>\n";
if (isset($torrents))
    $HTMLOUT .= "<tr valign=\"top\">
    <td class=\"rowhead\" width=\"10%\">{$lang['userdetails_uploaded_t']}</td>
    <td align=\"left\" width=\"90%\">
    <a href=\"javascript: klappe_news('a')\">
    <img border=\"0\" src=\"{$FMED['pic_base_url']}plus.gif\" id=\"pica\" alt=\"Show/Hide\" /></a>
    <b><font color=\"red\"></font></b>
    <div id=\"ka\" style=\"display: none;\">$torrents</div></td></tr>\n";
if (isset($seeding))
    $HTMLOUT .= "<tr valign=\"top\">
    <td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_seed']}</td>
    <td align=\"left\" width=\"90%\">
    <a href=\"javascript: klappe_news('a1')\">
    <img border=\"0\" src=\"{$FMED['pic_base_url']}plus.gif\" id=\"pica1\" alt=\"Show/Hide\" /></a>
    <b><font color=\"red\"></font></b>
    <div id=\"ka1\" style=\"display: none;\">" . maketable($seeding) . "</div></td></tr>\n";
if (isset($leeching))
    $HTMLOUT .= "<tr valign=\"top\">
    <td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_leech']}</td>
    <td align=\"left\" width=\"90%\">
    <a href=\"javascript: klappe_news('a2')\">
    <img border=\"0\" src=\"{$FMED['pic_base_url']}plus.gif\" id=\"pica2\" alt=\"Show/Hide\" /></a>
    <b><font color=\"red\">
    </font></b><div id=\"ka2\" style=\"display: none;\">" . maketable($leeching) . "</div></td></tr>\n";
    $count_snatched='';
if ($CURUSER['class'] >= UC_MODERATOR){
if (isset($_GET["snatched_table"])){
    $HTMLOUT .="<tr>
    <td class='rowhead' align='right' valign='top'><b>Snatched stuff:</b>
    <br />
    [ <a class='altlink' href=\"userdetails.php?id=$id\" class=\"sublink\">Hide list</a> ]</td>
    <td class='rowhead'>";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT sn.start_date AS s, sn.complete_date AS c, sn.last_action AS l_a, sn.seedtime AS s_t, sn.seedtime, sn.leechtime AS l_t, sn.leechtime, sn.downspeed, sn.upspeed, sn.uploaded, sn.downloaded, sn.torrentid, sn.start_date, sn.complete_date, sn.seeder, sn.last_action, sn.connectable, sn.agent, sn.seedtime, sn.port, cat.name, cat.image, t.size, t.seeders, t.leechers, t.owner, t.name AS torrent_name "."FROM snatched AS sn ". "LEFT JOIN torrents AS t ON t.id = sn.torrentid ". "LEFT JOIN categories AS cat ON cat.id = t.category ". "WHERE sn.userid=$id ORDER BY sn.start_date DESC") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $HTMLOUT .= "<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'>
    <tr>
    <td class='colhead' align='center'>Category</td>
    <td class='colhead' align='left'>Torrent</td>
    "."
    <td class='colhead' align='center'>S/L</td>
    <td class='colhead' align='center'>Up/Down</td>
    <td class='colhead' align='center'>Torrent Size</td>
    "."
    <td class='colhead' align='center'>Ratio</td>
    <td class='colhead' align='center'>Client</td></tr>";
    while ($arr = mysqli_fetch_assoc($res)){
    $count2='';
    $count2= (++$count2)%2;
    $class = 'clearalt'.($count2==0?'6':'7');
if ($arr["upspeed"] > 0)
    $ul_speed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
    else
    $ul_speed = mksize(($arr["uploaded"] / ( $arr['l_a'] - $arr['s'] + 1 )));
if ($arr["downspeed"] > 0)
    $dl_speed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
    else
    $dl_speed = mksize(($arr["downloaded"] / ( $arr['c'] - $arr['s'] + 1 )));
    $dlc="";
    switch (true){
    case ($dl_speed > 600):
    $dlc = 'red';
    break;
    case ($dl_speed > 300 ):
    $dlc = 'orange';
    break;
    case ($dl_speed > 200 ):
    $dlc = 'yellow';
    break;
    case ($dl_speed < 100 ):
    $dlc = 'Chartreuse';
    break;
}
if ($arr["downloaded"] > 0){
    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
    $ratio = "<font color=" . get_ratio_color($ratio) . "><b>Ratio:</b>
    <br />
    $ratio</font>";
    }else
if ($arr["uploaded"] > 0)
    $ratio = "Inf.";
    else
    $ratio = "N/A";
    $HTMLOUT .= "<tr>
    <td class='colhead' class='$class' align='center'>".($arr['owner'] == $id ? "<b><font color='orange'>Torrent owner</font></b>
    <br />
    " : "
    ".($arr['complete_date'] != '0'  ? "<b><font color='lightgreen'>Finished</font></b>
    <br />
    " : "
    <b><font color='red'>Not Finished</font><br />")."")."<img src={$FMED['pic_base_url']}caticons/$arr[image] alt=$arr[name]></td>
    "."
    <td class='colhead' class='$class'>
    <a class='altlink' href={$FMED['baseurl']}/details.php?id=$arr[torrentid]><b>$arr[torrent_name]</b></a>".($arr['complete_date'] != '0'  ?"
    <br />
    "."
    <font color='yellow'>started: ".get_date($arr['start_date'], 0,1) ."</font>
    <br />
    <font color='pink'>finished:" : ""."
    <br />
    </font><font color='yellow'>started:".get_date($arr['start_date'], 0,1) ."</font>
    <br />
    <font color='orange'>Last Action:".get_date($arr['last_action'], 0,1) ."</font>
    "."".get_date($arr['complete_date'], 0,1) ." ".($arr['complete_date'] == '0'  ? "".($arr['owner'] == $id ? "" : "[ ".mksize($arr["size"] - $arr["downloaded"])." still to go ]")."" : "")."")." ".get_date($arr['complete_date'], 0,1) ." ".($arr['complete_date'] != '0'  ? "
    <br />
    "."
    <font color='silver'>time to download: ".($arr['leechtime'] != '0' ? mkprettytime($arr['leechtime']) : mkprettytime($arr['c'] - $arr['s'])."")."</font> <font color='$dlc'>[ DLed at: $dl_speed ]<font>
    "."
    <br />
    " : "
    <br />
    ")."<font color='lightblue'>".($arr['seedtime'] != '0' ? "total seeding time: ".mkprettytime($arr['seedtime'])." <font color='$dlc'> " : "total seeding time: N/A")."
    "."
    </font><font color='lightgreen'> [ up speed: ".$ul_speed." ] </font>".get_date($arr['complete_date'], 0,1) ."".($arr['complete_date'] == '0'  ? "
    <br />
    <font color='$dlc'>
    Download speed:
    $dl_speed</font>" : "")."</td>
    "."
    <td class='colhead' align='center' class='$class'>Seeds: ".$arr['seeders']."
    <br />
    Leech:
    ".$arr['leechers']."</td>
    <td class='colhead' align='center' class='$class'><font color='lightgreen'>
    Uploaded:
    <br />
    "."
    <b>".$uploaded =mksize($arr["uploaded"])."</b></font>
    <br />
    <font color='orange'>
    Downloaded:
    <br />
    <b>".$downloaded = mksize($arr["downloaded"])."</b></font></td>
    "."
    <td class='colhead' align='center' class='$class'>".mksize($arr["size"])."
    <br />
    difference of:
    <br />
    <font color='orange'><b>".mksize($arr['size'] - $arr["downloaded"])."</b></font></td>
    "."
    <td class='colhead' align='center' class='$class'>$ratio
    <br />
    ".($arr['seeder'] == 'yes' ? "<font color='lightgreen'><b>seeding</b></font>" : "<font color='red'><b>not seeding</b></font>")."
    "."
    </td><td class='colhead' align='center' class='$class'>".$arr["agent"]."
    <br />
    port: ".$arr["port"]."
    <br />
    ".($arr["connectable"] == 'yes' ? "<b>connectable: <font color='lightgreen'>yes</font>
    "."
    </b>" : "<b>connectable: <font color='red'><b>no</b></font>")."</td></tr>\n";
}
    $HTMLOUT.= "</table></td></tr>\n";
    } else
    $HTMLOUT.= "<tr>
    <td class='rowhead' width='1%'><b>Snatched:</b>
    <br />
    </td>
    <td align='left' width='99%'>[ <a href=\"userdetails.php?id=$id&amp;snatched_table=1#snatched_table\" class=\"sublink\">Show</a> ]  - $count_snatched <font color='red'><b>staff only!!!</b></font></td></tr>";
}
    $completed = "";
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
    torrents.name,torrents.added AS torrent_added, snatched.start_date AS
    s, snatched.complete_date AS c, snatched.downspeed, snatched.seedtime,
    snatched.seeder, snatched.torrentid as id, categories.id as category,
    categories.image, categories.name as catname, snatched.uploaded,
    snatched.downloaded, snatched.hit_and_run, snatched.mark_of_cain,
    snatched.complete_date, snatched.last_action, torrents.seeders,
    torrents.leechers, torrents.owner, snatched.start_date AS st,
    snatched.start_date FROM snatched JOIN torrents ON torrents.id =
    snatched.torrentid JOIN categories ON categories.id = torrents.category
    WHERE snatched.finished='yes' AND userid=$id AND torrents.owner != $id ORDER BY snatched.id DESC") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($r) > 0){
    $completed .= "<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'>
    <tr>
    <td class='colhead'>{$lang['userdetails_type']}</td>
    <td class='colhead'>{$lang['userdetails_name']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_s']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_l']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ul']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_dl']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ratio']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_wcompleted']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_laction']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_speed']}</td></tr>";
    while ($a = mysqli_fetch_assoc($r)){
    $count2='';
    $count2= (++$count2)%2;
    $class = 'clearalt'.($count2 == 0 ? 6 : 7);
    $torrent_needed_seed_time = ($a['st'] - $a['torrent_added']);
    switch (true){
    case ($user['class'] < UC_POWER_USER):
    $days_3 = 3*86400; //== 3 days
    $days_14 = 2*86400; //== 2 days
    $days_over_14 = 86400; //== 1 day
    break;
    case ($user['class'] == UC_POWER_USER):
    $days_3 = 2*86400; //== 2 days
    $days_14 = 129600; //== 36 hours
    $days_over_14 = 64800; //== 18 hours
    break;
    case ($user['class'] == UC_VIP):
    $days_3 = 129600; //== 36 hours
    $days_14 = 86400; //== 24 hours
    $days_over_14 = 43200; //== 12 hours
    break;
    case ($user['class'] >= UC_UPLOADER):
    $days_3 = 86400; //== 24 hours
    $days_14 = 43200; //== 12 hours
    $days_over_14 = 21600; //== 6 hours
    break;
}
    switch(true){
    case (($a['st'] - $a['torrent_added']) < 7*86400):
    $minus_ratio = ($days_3 - $a['seedtime']) - ($a['uploaded'] / $a['downloaded'] * 3 * 86400);
    break;
    case (($a['st'] - $a['torrent_added']) < 21*86400):
    $minus_ratio = ($days_14 - $a['seedtime']) - ($a['uploaded'] / $a['downloaded'] * 2 * 86400);
    break;
    case (($a['st'] - $a['torrent_added']) >= 21*86400):
    $minus_ratio = ($days_over_14 - $a['seedtime']) - ($a['uploaded'] / $a['downloaded'] * 86400);
    break;
}
    $color = (($minus_ratio > 0 && $a['uploaded'] < $a['downloaded']) ? get_ratio_color($minus_ratio) : 'limegreen');
    $minus_ratio = mkprettytime($minus_ratio);
if ($arr["downspeed"] > 0)
    $dl_speed = ($a["downspeed"] > 0 ? mksize($a["downspeed"]) : ($a["leechtime"] > 0 ? mksize($a["downloaded"] / $a["leechtime"]) : mksize(0)));
    else
    $dl_speed = mksize(($a["downloaded"] / ( $a['c'] - $a['s'] + 1 )));
    $dlc="";
    switch (true){
    case ($dl_speed > 600):
    $dlc = 'red';
    break;
    case ($dl_speed > 300 ):
    $dlc = 'orange';
    break;
    case ($dl_speed > 200 ):
    $dlc = 'yellow';
    break;
    case ($dl_speed < 100 ):
    $dlc = 'Chartreuse';
    break;
}
    $checkbox_for_delete = ($CURUSER['class'] >=  UC_MODERATOR ? " [<a href='".$FMED['baseurl']."/userdetails.php?id=".$id."&amp;delete_hit_and_run=".$a['id']."'>Remove</a>]" : '');
    $mark_of_cain = ($a['mark_of_cain'] == 'yes' ? "<img src='{$FMED['pic_base_url']}del.png' width='10'alt='Mark Of Cain' title='the mark of Cain!' />".$checkbox_for_delete : '');
    $hit_n_run = ($a['hit_and_run'] > 0 ? "<img src='{$FMED['pic_base_url']}off.png' width='10' alt='hit and run' title='hit and run!' />" : '');
    $completed .= "<tr><td class='colhead' style='padding: 0px' class='$class'><img src='{$FMED['pic_base_url']}caticons/$a[image]' alt='$a[name]' title='$a[name]' /></td>
    <td class='colhead' class='$class'><a class='altlink' href='{$FMED['baseurl']}/details.php?id=".$a['id']."&amp;hit=1'><b>".htmlspecialchars($a['name'])."</b></a>
    <br /><font color='.$color.'>  ".(($CURUSER['class'] >= UC_MODERATOR || $user['id'] == $CURUSER['id']) ? "seeded for: ".mkprettytime($a['seedtime']).(($minus_ratio != '0:00' && $a['uploaded'] < $a['downloaded']) ? "<br />should still seed for: ".$minus_ratio."</font>&nbsp;&nbsp;" : '').
    ($a['seeder'] == 'yes' ? "&nbsp;<font color='limegreen'> [<b>seeding</b>]</font>" : $hit_n_run."&nbsp;".$mark_of_cain) : '')."</td>
    <td class='colhead' align='center' class='$class'>".$a['seeders']."</td>
    <td class='colhead' align='center' class='$class'>".$a['leechers']."</td>
    <td class='colhead' align='center' class='$class'>".mksize($a['uploaded'])."</td>
    <td class='colhead' align='center' class='$class'>".mksize($a['downloaded'])."</td>
    <td class='colhead' align='center' class='$class'>".($a['downloaded'] > 0 ? "<font color='" . get_ratio_color(number_format($a['uploaded'] / $a['downloaded'], 3)) . "'>".number_format($a['uploaded'] / $a['downloaded'], 3)."</font>" : ($a['uploaded'] > 0 ? 'Inf.' : '---'))."<br /></td>
    <td class='colhead' align='center' class='$class'>".get_date($a['complete_date'], 'DATE')."</td>
    <td class='colhead' align='center' class='$class'>".get_date($a['last_action'], 'DATE')."</td>
    <td class='colhead' align='center' class='$class'><font color='$dlc'>[ DLed at: $dl_speed ]</font></td></tr>";
}
    $completed .= "</table>";
}
if ($completed && $CURUSER['class'] >= UC_POWER_USER || $completed && $user['id'] == $CURUSER['id']){
if (!isset($_GET['completed']))
    $HTMLOUT .= tr(''.$lang['userdetails_completedt'].'<br />','[ <a href=\'./userdetails.php?id='.$id.'&amp;completed=1#completed\' class=\'sublink\'>Show</a> ]&nbsp;&nbsp;-&nbsp;'.mysqli_num_rows($r), 1);
    elseif (mysqli_num_rows($r) == 0)
    $HTMLOUT .= tr(''.$lang['userdetails_completed'].'<br />','[ <a href=\'./userdetails.php?id='.$id.'&amp;completed=1\' class=\'sublink\'>Show</a> ]&nbsp;&nbsp;-&nbsp;'.mysqli_num_rows($r), 1);
    else
    $HTMLOUT .= tr('<a name=\'completed\'>'.$lang['userdetails_completed'].'</a><br />[ <a href=\'./userdetails.php?id='.$id.'#history\' class=\'sublink\'>Hide list</a> ]', $completed, 1);
}
if ($user["info"])
    $HTMLOUT .= "<tr valign='top'>
    <td align='left' colspan='2' class='text' bgcolor='#F4F4F0'>" . format_comment($user["info"]) . "</td></tr>\n";
if ($CURUSER["id"] != $user["id"])
if ($CURUSER['class'] >= UC_MODERATOR)
    $showpmbutton = 1;
    elseif ($user["acceptpms"] == "yes"){
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM blocks WHERE userid={$user['id']} AND blockid={$CURUSER['id']}") or sqlerr(__FILE__,__LINE__);
    $showpmbutton = (mysqli_num_rows($r) == 1 ? 0 : 1);
    }elseif ($user["acceptpms"] == "friends"){
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM friends WHERE userid=$user[id] AND friendid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
    $showpmbutton = (mysqli_num_rows($r) == 1 ? 1 : 0);
}
if (isset($showpmbutton))
    $HTMLOUT .= "<tr>
    <td colspan='2' align='center'>
    <form method='get' action='sendmessage.php'>
    <input type='hidden' name='receiver' value='{$user["id"]}' />
    <input type='submit' value='{$lang['userdetails_msg_btn']}' class='btn' />
    </form>
    </td></tr>";
    $HTMLOUT .= "</table>\n";
if ($CURUSER['class'] >= UC_MODERATOR && $user["class"] < $CURUSER['class']){
    $HTMLOUT .= begin_frame("{$lang['userdetails_edit_user']}", true);
    $HTMLOUT .= "<form method='post' action='modtask.php'>\n";
    $HTMLOUT .= "<input type='hidden' name='action' value='edituser' />\n";
    $HTMLOUT .= "<input type='hidden' name='userid' value='$id' />\n";
    $HTMLOUT .= "<input type='hidden' name='returnto' value='userdetails.php?id=$id' />\n";
    $HTMLOUT .= "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_title']}</td><td colspan='2' align='left'><input type='text' size='60' name='title' value='" . htmlspecialchars($user['title']) . "' /></td></tr>\n";
    $avatar = htmlspecialchars($user["avatar"]);
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_avatar_url']}</td><td colspan='2' align='left'><input type='text' size='60' name='avatar' value='$avatar' /></td></tr>\n";
if ($CURUSER["class"] == UC_SYSOP) {
    $donor = $user["donor"] == "yes";
    $HTMLOUT .="<tr>
    <td class='rowhead' align='right'>{$lang['userdetails_donor']}</td>
    <td colspan='2' align='center'>";
if ($donor) {
    $donoruntil = $user['donoruntil'];
if ($donoruntil == '0')
    $HTMLOUT .="Arbitrary duration";
    else {
    $HTMLOUT .="".$lang['userdetails_donor2']." ".get_date($user['donoruntil'], 'DATE'). " ";
    $HTMLOUT .=" [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go\n";
}
    } else {
    $HTMLOUT .="{$lang['userdetails_dfor']}<select name='donorlength'>
    <option value='0'>------</option><option value='4'>1 month</option>
    " . "
    <option value='6'>6 weeks</option>
    <option value='8'>2 months</option>
    <option value='10'>10 weeks</option>
    " . "
    <option value='12'>3 months</option>
    <option value='255'>Unlimited</option></select>\n";
}
    $HTMLOUT .="<br />{$lang['userdetails_cdonation']}
    <input type='text' size='6' name='donated' value=\"" .htmlspecialchars($user["donated"]) . "\" />" . "<b>{$lang['userdetails_tdonations']}</b>" . htmlspecialchars($user["total_donated"]) . "";
if ($donor) {
    $HTMLOUT .="<br />{$lang['userdetails_adonor']}
    <select name='donorlengthadd'>
    <option value='0'>------</option><option value='4'>1 month</option>
    " . "
    <option value='6'>6 weeks</option>
    <option value='8'>2 months</option>
    <option value='10'>10 weeks</option>
    " . "
    <option value='12'>3 months</option>
    <option value='255'>Unlimited</option></select>\n";
    $HTMLOUT .="<br />{$lang['userdetails_rdonor']}
    <input name='donor' value='no' type='checkbox' /> [ If they were bad ]";
}
    $HTMLOUT .="</td></tr>\n";
}
if ($CURUSER['class'] == UC_MODERATOR && $user["class"] > UC_VIP)
    $HTMLOUT .= "<input type='hidden' name='class' value='{$user['class']}' />\n";
    else{
    $HTMLOUT .= "<tr>
    <td class='rowhead'>Class</td>
    <td colspan='2' align='left'><select name='class'>\n";
if ($CURUSER['class'] == UC_MODERATOR)
    $maxclass = UC_VIP;
    else
    $maxclass = $CURUSER['class'] - 1;
    for ($i = 0; $i <= $maxclass; ++$i)
    $HTMLOUT .= "<option value='$i'" . ($user["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n";
    $HTMLOUT .= "</select></td></tr>\n";
}
    $bonuscomment = htmlspecialchars($user["bonuscomment"]);
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_bonus_comment']}</td>
    <td colspan='2' align='left'>
    <textarea cols='60' rows='6' name='bonuscomment' readonly='readonly' style='background:purple;color:yellow;'>
    $bonuscomment</textarea></td></tr>\n";
    $modcomment = htmlspecialchars($user["modcomment"]);
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='modcomment'>$modcomment</textarea></td></tr>\n";
    $warned = $user["warned"] == "yes";
    $HTMLOUT .= "<tr><td class='rowhead'" . (!$warned ? " rowspan='2'": "") . ">{$lang['userdetails_warned']}</td>
    <td align='left' width='20%'>" .
    ( $warned
    ? "<input name=warned value='yes' type='radio' checked='checked' />{$lang['userdetails_yes']}<input name='warned' value='no' type='radio' />{$lang['userdetails_no']}"
    : "{$lang['userdetails_no']}" ) ."</td>";
if ($warned){
    $warneduntil = $user['warneduntil'];
if ($warneduntil == 0)
    $HTMLOUT .= "<td align='center'>{$lang['userdetails_dur']}</td></tr>\n";
    else{
    $HTMLOUT .= "<td align='center'>{$lang['userdetails_until']} ".get_date($warneduntil, 'DATE');
    $HTMLOUT .= " (" . mkprettytime($warneduntil - time())  . " {$lang['userdetails_togo']})</td></tr>\n";
}
    }else{
    $HTMLOUT .= "<td>{$lang['userdetails_warn_for']} <select name='warnlength'>\n";
    $HTMLOUT .= "<option value='0'>{$lang['userdetails_warn0']}</option>\n";
    $HTMLOUT .= "<option value='1'>{$lang['userdetails_warn1']}</option>\n";
    $HTMLOUT .= "<option value='2'>{$lang['userdetails_warn2']}</option>\n";
    $HTMLOUT .= "<option value='4'>{$lang['userdetails_warn4']}</option>\n";
    $HTMLOUT .= "<option value='8'>{$lang['userdetails_warn8']}</option>\n";
    $HTMLOUT .= "<option value='255'>{$lang['userdetails_warninf']}</option>\n";
    $HTMLOUT .= "</select>{$lang['userdetails_pm_comm']}</td></tr>\n";
    $HTMLOUT .= "<tr>
    <td colspan='2' align='left'>
    <input type='text' size='60' name='warnpm' /></td></tr>";
}
if ($CURUSER['class'] >= UC_MODERATOR)
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_invites']}</td>
    <td class='col1' colspan='2' align='left'>
    <input type='text' size='6' name='invites' value='".(int)$user['invites']."' /></td></tr>";
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_bonus_points']}</td>
    <td class='col1' colspan='2' align='left'>
    <input type='text' size='6' name='seedbonus' value='".(int)$user['seedbonus']."' /></td></tr>";
if ($CURUSER['class'] >= UC_STAFF) {
    $immunity = $user['immunity'] != 0;
    $HTMLOUT .= "<tr>
    <td class='rowhead'".(!$immunity ? ' rowspan="2"' : '').">{$lang['userdetails_immunity']}</td>
    <td align='left' width='20%'>".($immunity ? "<input name='immunity' value='42' type='radio' />Remove immune Status" : "No immunity Status Set")."</td>\n";
if ($immunity){
if ($user['immunity'] == 1)
    $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
    else
    $HTMLOUT .= "<td align='center'>Until ".get_date($user['immunity'], 'DATE'). " (".mkprettytime($user['immunity'] - time()). " to go)</td></tr>";
    } else{
    $HTMLOUT .= '<td>Immunity for <select name="immunity">
    <option value="0">------</option>
    <option value="1">1 week</option>
    <option value="2">2 weeks</option>
    <option value="4">4 weeks</option>
    <option value="8">8 weeks</option>
    <option value="255">Unlimited</option>
    </select></td></tr><tr>
    <td colspan="2" align="left">
    PM comment:
    <input type="text" size="60" name="immunity_pm" /></td></tr>';
    }
}
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_downloadenabled']}</td>
    <td colspan='2' align='left'>
    <input type='radio' name='downloadpos' value='yes'" .($user["downloadpos"] == "yes" ? " checked='checked'" : "")." />{$lang['userdetails_yes']}
    <input type='radio' name='downloadpos' value='no'" .($user["downloadpos"] == "no" ? " checked='checked'" : "")." />{$lang['userdetails_no']}</td></tr>\n";
    $HTMLOUT .= "<tr>
    <td class='rowhead'>{$lang['userdetails_enabled']}</td>
    <td colspan='2' align='left'>
    <input name='enabled' value='yes' type='radio'" . ($enabled ? " checked='checked'" : "") . " />{$lang['userdetails_yes']} <input name='enabled' value='no' type='radio'" . (!$enabled ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_reset']}</td>
    <td colspan='2'>
    <input type='checkbox' name='resetpasskey' value='1' /><font class='small'>{$lang['userdetails_pass_msg']}</font></td></tr>";
    $HTMLOUT .= "<tr>
    <td colspan='3' align='center'>
    <input type='submit' class='btn' value='{$lang['userdetails_okay']}' /></td></tr>\n";
    $HTMLOUT .= "</table>\n";
    $HTMLOUT .= "</form>\n";
    $HTMLOUT .= end_frame();
}
    $HTMLOUT .= end_main_frame();
    print stdhead("{$lang['userdetails_details']} " . $user["username"]) . $HTMLOUT . stdfoot();
?> 