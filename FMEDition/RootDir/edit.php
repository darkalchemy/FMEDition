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
require_once "include/bittorrent.php" ;
require_once "include/user_functions.php" ;
require_once "include/html_functions.php" ;
if (!mkglobal("id"))
    die();
    $id = 0 + $id;
if (!$id)
    die();
dbconn();
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('edit') );
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM torrents WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
if (!$row)
    stderr($lang['edit_user_error'], $lang['edit_no_torrent']);
if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)){
    stderr($lang['edit_user_error'], sprintf($lang['edit_no_permission'], urlencode($_SERVER['REQUEST_URI'])));
}
    $HTMLOUT = '';
    $HTMLOUT  .= "<form method='post' action='takeedit.php' enctype='multipart/form-data'><input type='hidden' name='id' value='$id' />";
if (isset($_GET["returnto"]))
    $HTMLOUT  .= "<input type='hidden' name='returnto' value='" . htmlspecialchars($_GET["returnto"]) . "' />\n";
    $HTMLOUT  .=  "<table border='1' cellspacing='0' cellpadding='10'>\n";
    $HTMLOUT  .= tr($lang['edit_torrent_name'], "<input type='text' name='name' value='" . htmlspecialchars($row["name"]) . "' size='80' />", 1);
    $HTMLOUT.= "<td class='rowhead' >Youtube</td>
    <td align='left'>
    <input type='text' name='tube' value='".htmlspecialchars($row['tube'])."'size='80' />
    <br />
    (Direct link for the youtube trailer to be shown on the details page)</td></tr>\n";
    $HTMLOUT.= "<td class='rowhead' >{$lang['edit_poster']}</td>
    <td align='left'>
    <input type='text' name='poster' size='80' value='" . htmlspecialchars($row["poster"]) . "' />
    <br />
    {$lang['edit_poster1']}</td></tr>\n";
    $HTMLOUT  .= tr($lang['edit_nfo'], "<input type='radio' name='nfoaction' value='keep' checked='checked' />{$lang['edit_keep_current']}<br />"."<input type='radio' name='nfoaction' value='update' />{$lang['edit_update']}<br /><input type='file' name='nfo' size='80' />", 1);
if ((strpos($row["ori_descr"], "<") === false) || (strpos($row["ori_descr"], "&lt;") !== false)){
    $c = "";
    }else{
    $c = " checked";
}
    $HTMLOUT  .= tr($lang['edit_description'], "<textarea name='descr' rows='10' cols='80'>" . htmlspecialchars($row["ori_descr"]) . "</textarea><br />({$lang['edit_tags']})", 1);
    $s = "<select name='type'>\n";
    $cats = genrelist();
    foreach ($cats as $subrow){
    $s .= "<option value='" . $subrow["id"] . "'";
if ($subrow["id"] == $row["category"])
    $s .= " selected='selected'";
    $s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
}
    $s .= "</select>\n";
    $HTMLOUT  .= tr($lang['edit_type'], $s, 1);
    $HTMLOUT  .= tr($lang['edit_visible'], "<input type='checkbox' name='visible'" . (($row["visible"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> {$lang['edit_visible_mainpage']}<br /><table border='0' cellspacing='0' cellpadding='0' width='420'><tr><td class='embedded'>{$lang['edit_visible_info']}</td></tr></table>", 1);
if (get_user_class() >= UC_MODERATOR){
    $HTMLOUT  .= tr($lang['edit_banned'], "<input type='checkbox' name='banned'" . (($row["banned"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> {$lang['edit_banned']}", 1);
    $HTMLOUT.= "<td class='rowhead'>Sticky</td>
    <td align='left'>
    <input type='radio' name='sticky'" . ($row["sticky"] == "yes" ? " checked='checked'" : "") . " value='1' />
    Yes
    <input type='radio' name='sticky'" . ($row["sticky"] == "no" ? " checked='checked'" : "") . " value='no' />No</td></tr>\n";
    $HTMLOUT.= "<td class='rowhead' >Freeleach</td>
    <td align='left'>
    <input type='radio' name='free'" . ($row["free"] == "yes" ? " checked='checked'" : "") . " value='1' />
    Yes
    <input type='radio' name='free'" . ($row["free"] == "no" ? " checked='checked'" : "") . " value='no' />No</td></tr>\n";
    $HTMLOUT.= "<td class='rowhead' >Seedbox</td>
    <td align='left'>
    <input type='radio' name='box'" . ($row["box"] == "yes" ? " checked='checked'" : "") . " value='1' />
    Yes
    <input type='radio' name='box'" . ($row["box"] == "no" ? " checked='checked'" : "") . " value='no' />No</td></tr>\n";
    $HTMLOUT.= "<td class='rowhead' >vip</td>
    <td align='left'>
    <input type='radio' name='vip'" . ($row["vip"] == "yes" ? " checked='checked'" : "") . " value='1' />
    Yes
    <input type='radio' name='vip'" . ($row["vip"] == "no" ? " checked='checked'" : "") . " value='no' />No</td></tr>\n";
}
    $HTMLOUT  .= "<tr><td colspan='2' align='center'><input type='submit' value='{$lang['edit_submit']}' class='btn' /> <input type='reset' value='{$lang['edit_revert']}' class='btn' /></td></tr>
    </table>
    </form>
    <br />
    <form method='post' action='delete.php'>
    <table border='1' cellspacing='0' cellpadding='5'>
    <tr>
      <td class='embedded' style='background-color: #F5F4EA;padding-bottom: 5px' colspan='2'><b>{$lang['edit_delete_torrent']}.</b> {$lang['edit_reason']}</td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='1' />&nbsp;{$lang['edit_dead']} </td><td> {$lang['edit_peers']}</td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='2' />&nbsp;{$lang['edit_dupe']}</td><td><input type='text' size='40' name='reason[]' /></td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='3' />&nbsp;{$lang['edit_nuked']}</td><td><input type='text' size='40' name='reason[]' /></td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='4' />&nbsp;{$lang['edit_rules']}</td><td><input type='text' size='40' name='reason[]' />({$lang['edit_req']})</td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='5' checked='checked' />&nbsp;{$lang['edit_other']}</td><td><input type='text' size='40' name='reason[]' />({$lang['edit_req']})<input type='hidden' name='id' value='$id' /></td>
    </tr>";
if (isset($_GET["returnto"])){
    $HTMLOUT  .= "<input type='hidden' name='returnto' value='" . htmlspecialchars($_GET["returnto"]) . "' />\n";
}
    $HTMLOUT  .= "<tr><td colspan='2' align='center'><input type='submit' value='{$lang['edit_delete']}' class='btn' /></td>
    </tr>
    </table>
    </form>";
    print stdhead("{$lang['edit_stdhead']} '{$row["name"]}'") . $HTMLOUT . stdfoot();
?> 