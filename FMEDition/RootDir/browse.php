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
require_once "include/torrenttable_functions.php";
require_once "include/pager_functions.php";
dbconn(false);
loggedinorreturn();
if (isset($_GET['clear_new']) && $_GET['clear_new'] == '1'){
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET last_browse=".TIME_NOW." where id=".$CURUSER['id']);
    header("Location: {$FMED['baseurl']}/browse.php");
}
    $lang = array_merge( load_language('global'), load_language('browse'), load_language('torrenttable_functions') );
    $HTMLOUT = '';
    $cats = genrelist();
if(isset($_GET["search"])){
    $searchstr = unesc($_GET["search"]);
    $cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
    unset($cleansearchstr);
}
    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
    $addparam = "";
    $wherea = array();
    $wherecatina = array();
if (isset($_GET["incldead"]) &&  $_GET["incldead"] == 1){
    $addparam .= "incldead=1&amp;";
if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR)
    $wherea[] = "banned != 'yes'";
    }else{
if (isset($_GET["incldead"]) && $_GET["incldead"] == 2){
    $addparam .= "incldead=2&amp;";
    $wherea[] = "visible = 'no'";
    }else
    $wherea[] = "visible = 'yes'";
}
    $category = (isset($_GET["cat"])) ? (int)$_GET["cat"] : false;
    $all = isset($_GET["all"]) ? $_GET["all"] : false;
if (!$all){
if (!$_GET && $CURUSER["notifs"]){
    $all = True;
    foreach ($cats as $cat){
    $all &= $cat['id'];
if (strpos($CURUSER["notifs"], "[cat" . $cat['id'] . "]") !== False){
    $wherecatina[] = $cat['id'];
    $addparam .= "c{$cat['id']}=1&amp;";
    }
}
    }elseif ($category){
if (!is_valid_id($category))
    stderr("{$lang['browse_error']}", "{$lang['browse_invalid_cat']}");
    $wherecatina[] = $category;
    $addparam .= "cat=$category&amp;";
    }else{
    $all = True;
    foreach ($cats as $cat){
    $all &= isset($_GET["c{$cat['id']}"]);
if (isset($_GET["c{$cat['id']}"])){
    $wherecatina[] = $cat['id'];
    $addparam .= "c{$cat['id']}=1&amp;";
    }
  }
 }
}
if ($all){
    $wherecatina = array();
    $addparam = "";
}
if (count($wherecatina) > 1)
    $wherecatin = implode(",",$wherecatina);
    elseif (count($wherecatina) == 1)
    $wherea[] = "category = $wherecatina[0]";
    $wherebase = $wherea;
if (isset($cleansearchstr)){
    $wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
    $addparam .= "search=" . urlencode($searchstr) . "&amp;";
    $orderby = "";
    $searchcloud = sqlesc($cleansearchstr);
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO searchcloud (searchedfor, howmuch) VALUES ($searchcloud, 1) ON DUPLICATE KEY UPDATE howmuch=howmuch+1");
}
    $where = implode(" AND ", $wherea);
if (isset($wherecatin))
    $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
if ($where != "")
    $where = "WHERE $where";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM torrents $where") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $row = mysqli_fetch_array($res, MYSQLI_NUM);
    $count = $row[0];
if (!$count && isset($cleansearchstr)){
    $wherea = $wherebase;
    $orderby = "ORDER BY id DESC";
    $searcha = explode(" ", $cleansearchstr);
    $sc = 0;
    foreach ($searcha as $searchss){
if (strlen($searchss) <= 1)
    continue;
    $sc++;
if ($sc > 5)
    break;
    $ssa = array();
    foreach (array("search_text", "ori_descr") as $sss)
    $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
    $wherea[] = "(" . implode(" OR ", $ssa) . ")";
}
if ($sc){
    $where = implode(" AND ", $wherea);
if ($where != "")
    $where = "WHERE $where";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM torrents $where");
    $row = mysqli_fetch_array($res, MYSQLI_NUM);
    $count = $row[0];
    }
}
    $torrentsperpage = $CURUSER["torrentsperpage"];
if (!$torrentsperpage)
    $torrentsperpage = 15;
if ($count){
    $pager = pager($torrentsperpage, $count, "browse.php?" . $addparam);

    $query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.sticky, torrents.free, torrents.box, torrents.vip, torrents.type, torrents.comments,torrents.numfiles,torrents.filename,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .
    //  "IF(torrents.numratings < {$FMED['minvotes']}, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
    "categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby {$pager['limit']}";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    }else{
    unset($res);
}
if (isset($cleansearchstr))
    $title = "{$lang['browse_search']}\"$searchstr\"";
    else
    $title = '';
    $HTMLOUT .= "<br><div id='wrapper' style='width:90%;border:1px solid black;background-color:gray;'>";
    require_once "include/searchcloud_functions.php";
    $HTMLOUT .= cloud() . "</div><br /><br />";
    $HTMLOUT .= "<form method='get' action='browse.php'>
    <table class='bottom'>
    <tr>
    <td class='bottom'>
    <table class='bottom'>
    <tr>";
    $i = 0;
    $catsperrow = 15;
    foreach ($cats as $cat){
    $HTMLOUT .= ($i && $i % $catsperrow == 0) ? "</tr><tr>" : "";
    $HTMLOUT .= "<td class='bottom' style='padding-bottom: 2px;padding-left: 7px;
    <input name='c".$cat['id']."' type=\"checkbox\" " . (in_array($cat['id'],$wherecatina) ? "checked='checked' " : "") . "value='1' />
    <img width='42' src=/".$FMED['pic_base_url']."caticons/" . htmlspecialchars($cat['image']) . "></td>\n";
    $i++;
}
    $alllink = "<div align='left'>(<a href='browse.php?all=1'><b>{$lang['browse_show_all']}</b></a>)</div>";
    $ncats = count($cats);
    $nrows = ceil($ncats/$catsperrow);
    $lastrowcols = $ncats % $catsperrow;
if ($lastrowcols != 0){
if ($catsperrow - $lastrowcols != 1){
    $HTMLOUT .= "<td class='bottom' rowspan='" . ($catsperrow  - $lastrowcols - 1) . "'>&nbsp;</td>";
}
    $HTMLOUT .= "<br><td class='bottom' style=\"padding-left: 5px\">$alllink</td>\n";
}
if ($CURUSER['clear_new_tag_manually'] == 'yes') {
    $HTMLOUT .="<a href='?clear_new=1'><input type='submit' value='clear new tag' class='button' /></a>";
    } else {
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET last_browse=".TIME_NOW." where id=".$CURUSER['id']);
}
    $selected = (isset($_GET["incldead"])) ? (int)$_GET["incldead"] : "";
    $HTMLOUT .= "</tr>
    </table>
    </td>
    <td class='bottom'>
    <table class='main'>
    <tr>
        <td class='bottom' style='padding: 1px;padding-left: 10px'>
        <select name='incldead'>
    <option value='0'>{$lang['browse_active']}</option>
    <option value='1'".($selected == 1 ? " selected='selected'" : "").">{$lang['browse_inc_dead']}</option>
    <option value='2'".($selected == 2 ? " selected='selected'" : "").">{$lang['browse_dead']}</option>
    </select>
    </td>";
if ($ncats % $catsperrow == 0){
    $HTMLOUT .= "<td class='bottom' style='padding-left: 15px' rowspan='$nrows' valign='middle' align='right'>$alllink</td>\n";
}
    $HTMLOUT .= "</tr>
    <tr>
        <td class='bottom' style='padding: 1px;padding-left: 10px'>
        <div align='center'>
          <input type='submit' class='btn' value='{$lang['browse_go']}' />
    </div>
    </td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    </form>";
if (isset($cleansearchstr)){
    $HTMLOUT .= "<h2>{$lang['browse_search']}\"" . htmlentities($searchstr, ENT_QUOTES) . "\"</h2>\n";
}
    $HTMLOUT .="<br /><div class='browse'><div style='background:lightgrey;height:25px;'><span style='font-weight:bold;font-size:12pt;'>&nbsp;-&nbsp;Browse Torrants</span>&nbsp;</b></div>";
if ($count){
    $HTMLOUT .= $pager['pagertop'];
    $HTMLOUT .= torrenttable($res);
    $HTMLOUT .= $pager['pagerbottom'];
    }else{
if (isset($cleansearchstr)){
    $HTMLOUT .= "<div class='info'>";
    $HTMLOUT .= "<p><b>{$lang['browse_not_found']}</b>, {$lang['browse_tryagain']}</p>\n";
    $HTMLOUT .= "</div>";
    }else{
    $HTMLOUT .= "<div class='info'>";
    $HTMLOUT .= "<p><b>{$lang['browse_nothing']}</b>, {$lang['browse_sorry']}(</p>\n";
    $HTMLOUT .= "</div>";
    }
}
    print stdhead($title) . $HTMLOUT . stdfoot();
?>