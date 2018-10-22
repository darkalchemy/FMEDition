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
function linkcolor($num) {
if (!$num)
    return "red";
    return "green";
}
function torrenttable($res, $variant = "index") {
global $FMED, $CURUSER, $lang;
    $wait = 0;
    $htmlout = '';
if ($CURUSER["class"] < UC_VIP){
    $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
    $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
if ($ratio < 0.5 || $gigs < 5) $wait = 0;
    elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 0;
    elseif ($ratio < 0.8 || $gigs < 8) $wait = 0;
    elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 0;
    else $wait = 0;
}
    $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='5'>
    <tr>
    <td class='colhead' align='center'>{$lang["torrenttable_type"]}</td>
    <td class='colhead' width='400' align='left'>{$lang["torrenttable_name"]}</td>
    <!--<td class='heading' align='left'>{$lang["torrenttable_dl"]}</td>-->";
if ($wait){
    $htmlout .= "<td class='colhead' align='center'>{$lang["torrenttable_wait"]}</td>\n";
}
if ($variant == "mytorrents"){
      $htmlout .= "<td class='colhead' align='center'>{$lang["torrenttable_edit"]}</td>\n";
    $htmlout .= "<td class='colhead' align='center'>{$lang["torrenttable_visible"]}</td>\n";
}
    $htmlout .= "<td class='colhead' align='right'>{$lang["torrenttable_files"]}</td>
    <td class='colhead' align='right'>{$lang["torrenttable_comments"]}</td>
    <!--<td class='colhead' align='center'>{$lang["torrenttable_rating"]}</td>-->
    <td class='colhead' align='center'>{$lang["torrenttable_added"]}</td>
    <!--<td class='colhead' align='center'>{$lang["torrenttable_ttl"]}</td>-->
    <td class='colhead' align='center'>{$lang["torrenttable_size"]}</td>
    <!--
    <td class='colhead' align='right'>{$lang["torrenttable_views"]}</td>
    <td class='colhead' align='right'>{$lang["torrenttable_hits"]}</td>
    -->
    <td class='colhead' align='center'>{$lang["torrenttable_snatched"]}</td>
    <td class='colhead' align='right'>{$lang["torrenttable_seeders"]}</td>
    <td class='colhead' align='right'>{$lang["torrenttable_leechers"]}</td>";
if ($variant == 'index')
    $htmlout .= "<td class='colhead' align='center'>{$lang["torrenttable_uppedby"]}</td>\n";
    $htmlout .= "</tr>\n";
    while ($row = mysqli_fetch_assoc($res)) {
    $id = $row["id"];
    $htmlout .= "<tr>\n";
    $htmlout .= "<td align='center' style='padding: 0px'>";
if (isset($row["cat_name"])){
    $htmlout .= "<a href='browse.php?cat={$row['category']}'>";
if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
    $htmlout .= "<img border='0' width='42' height='42' src='{$FMED['pic_base_url']}caticons/{$row['cat_pic']}' alt='{$row['cat_name']}' />";
    else{
    $htmlout .= $row["cat_name"];
}
    $htmlout .= "</a>";
    }else{
    $htmlout .= "-";
}
    $htmlout .= "</td>\n";
    $dispname = htmlspecialchars($row["name"]);
    $htmlout .= "<td align='left'><a href='details.php?";
if ($variant == "mytorrents")
    $htmlout .= "returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;";
    $htmlout .= "id=$id";
if ($variant == "index")
    $htmlout .= "&amp;hit=1";
    $sticky = ($row['sticky']=="yes" ? "<img src='".$FMED['pic_base_url']."sticky.gif'  border='0' width='9' height='10' title='Sticky' />" : "");
    $thisisfree = ($row['free']=="yes" ? "<img src='".$FMED['pic_base_url']."freedownload.gif'  border='0' title='FreeLeach' />" : "");
    $box = ($row["box"]=="yes" ? "<img src='".$FMED['pic_base_url']."webseeder.png'  border='0' title='SeedBox' />" : "");
    $vip = ($row['vip']=="yes" ? "<img src='".$FMED['pic_base_url']."vip.gif'  border='0' title='V.I.P' />" : "");
    $htmlout .= "'>$sticky&nbsp;<b>$dispname</b></a><div style=\"float:right;vertical-align:bottom\">&nbsp;".($row['added'] >= $CURUSER['last_browse'] ? " <b><font color='red'>[New]</font></b>" : "")."</div><br />$thisisfree&nbsp;$box&nbsp;$vip</td>\n";
if ($wait){
    $elapsed = floor((time() - $row["added"]) / 3600);
if ($elapsed < $wait){
    $color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
    $htmlout .= "<td align='center'><span style='white-space: nowrap;'><a href='faq.php#dl8'><font color='$color'>" . number_format($wait - $elapsed) . " ".$lang["torrenttable_wait_h"]."</font></a></span></td>\n";
    }else
    $htmlout .= "<td align='center'><span style='white-space: nowrap;'>{$lang["torrenttable_wait_none"]}</span></td>\n";
}
if ($variant == "mytorrents")
    $htmlout .= "</td><td align='center'><a href='edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id={$row['id']}'>".$lang["torrenttable_edit"]."</a>\n";
    $htmlout .= "</td>\n";
if ($variant == "mytorrents"){
    $htmlout .= "<td align='right'>";
if ($row["visible"] == "no")
    $htmlout .= "<b>".$lang["torrenttable_not_visible"]."</b>";
    else
    $htmlout .= "".$lang["torrenttable_visible"]."";
    $htmlout .= "</td>\n";
}
if ($row["type"] == "single"){
    $htmlout .= "<td align='right'>{$row["numfiles"]}</td>\n";
    } else {
if ($variant == "index"){
    $htmlout .= "<td align='right'><b><a href='filelist.php?id=$id'>" . $row["numfiles"] . "</a></b></td>\n";
    }else{
    $htmlout .= "<td align='right'><b><a href='filelist.php?id=$id'>" . $row["numfiles"] . "</a></b></td>\n";
    }
}
if (!$row["comments"]){
    $htmlout .= "<td align='right'>{$row["comments"]}</td>\n";
    } else {
if ($variant == "index"){
    $htmlout .= "<td align='right'><b><a href='details.php?id=$id&amp;hit=1&amp;tocomm=1'>" . $row["comments"] . "</a></b></td>\n";
    }else{
    $htmlout .= "<td align='right'><b><a href='details.php?id=$id&amp;page=0#startcomments'>" . $row["comments"] . "</a></b></td>\n";
    }
}
    $htmlout .= "<td align='center'><span style='white-space: nowrap;'>" . str_replace(",", "<br />", get_date( $row['added'],'')) . "</span></td>\n";
    $htmlout .= "<td align='center'>" . str_replace(" ", "<br />", mksize($row["size"])) . "</td>\n";
if ($row["times_completed"] != 1)
    $_s = "".$lang["torrenttable_time_plural"]."";
    else
    $_s = "".$lang["torrenttable_time_singular"]."";
    $htmlout .= "<td align='center'><a href=snatches.php?id=$id>" . number_format($row["times_completed"]) . "<br /> $_s</td>\n";
if ($row["seeders"]){
if ($variant == "index"){
if ($row["leechers"]) $ratio = $row["seeders"] / $row["leechers"]; else $ratio = 1;
    $htmlout .= "<td align='right'><b><a href='peerlist.php?id=$id#seeders'><font color='" .get_slr_color($ratio) . "'>{$row["seeders"]}</font></a></b></td>\n";
    }else{
    $htmlout .= "<td align='right'><b><a class='" . linkcolor($row["seeders"]) . "' href='peerlist.php?id=$id#seeders'>{$row["seeders"]}</a></b></td>\n";
}
    }else{
    $htmlout .= "<td align='right'><span class='" . linkcolor($row["seeders"]) . "'>" . $row["seeders"] . "</span></td>\n";
}
if ($row["leechers"]){
if ($variant == "index")
    $htmlout .= "<td align='right'><b><a href='peerlist.php?id=$id#leechers'>" .number_format($row["leechers"]) . "</a></b></td>\n";
    else
    $htmlout .= "<td align='right'><b><a class='" . linkcolor($row["leechers"]) . "' href='peerlist.php?id=$id#leechers'>{$row["leechers"]}</a></b></td>\n";
    }else
    $htmlout .= "<td align='right'>0</td>\n";
if ($variant == "index")
    $htmlout .= "<td align='center'>" . (isset($row["username"]) ? ("<a href='userdetails.php?id=" . $row["owner"] . "'><b>" . htmlspecialchars($row["username"]) . "</b></a>") : "<i>(".$lang["torrenttable_unknown_uploader"].")</i>") . "</td>\n";
    $htmlout .= "</tr>\n";
}
    $htmlout .= "</table>\n";
    return $htmlout;
}
?>