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
require_once 'include/bittorrent.php';
require_once 'include/user_functions.php';
dbconn();
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('takeedit') );
if (!mkglobal('name:descr:type'))
    stderr($lang['takedit_failed'], $lang['takedit_no_data']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ( !is_valid_id($id) )
    stderr($lang['takedit_failed'], $lang['takedit_no_data']);
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT owner, filename, save_as FROM torrents WHERE id = $id");
if ( false == mysqli_num_rows($res) )
    stderr($lang['takedit_failed'], $lang['takedit_no_data']);
    $row = mysqli_fetch_assoc($res);
if ($CURUSER['id'] != $row['owner'] && $CURUSER['class'] < UC_MODERATOR)
    stderr($lang['takedit_failed'], $lang['takedit_not_owner']);
    $updateset = array();
    $fname = $row['filename'];
    preg_match('/^(.+)\.torrent$/si', $fname, $matches);
    $shortfname = $matches[1];
    $dname = $row['save_as'];
    $nfoaction = $_POST['nfoaction'];
if ($nfoaction == 'update'){
    $nfofile = $_FILES['nfo'];
if (!$nfofile) die("No data " . var_dump($_FILES));
if ($nfofile['size'] > 65535)
    stderr($lang['takedit_failed'], $lang['takedit_nfo_error']);
    $nfofilename = $nfofile['tmp_name'];
if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
    $updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
    }else
if ($nfoaction == 'remove')
    $updateset[] = 'nfo = ""';
    $updateset[] = "name = " . sqlesc($name);
    $updateset[] = "search_text = " . sqlesc(searchfield("$shortfname $dname $name"));
    $updateset[] = "descr = " . sqlesc($descr);
    $updateset[] = "ori_descr = " . sqlesc($descr);
    $updateset[] = "category = " . (0 + $type);
    $updateset[] = "sticky = '".($_POST["sticky"]==1 ? 'yes' : 'no')."'";
    $updateset[] = "free = '".($_POST["free"]==1 ? 'yes' : 'no')."'";
    $updateset[] = "box = '".($_POST["box"]==1 ? 'yes' : 'no')."'";
    $updateset[] = "vip = '".($_POST["vip"]==1 ? 'yes' : 'no')."'";
if (isset($_POST['tube']) && (($tube = $_POST['tube']) != $fetch_assoc['tube'] && !empty($tube))){
if (!preg_match("/^http:\/\/[^\s'\"<>]+\.(Youtube)$/i", $tube))
    $updateset[] = 'tube = ' . sqlesc($tube);
}
if (isset($_POST['poster']) && (($poster = $_POST['poster']) != $fetch_assoc['poster'] && !empty($poster))){
if (!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $poster))
    $updateset[] = 'poster = ' . sqlesc($poster);
}
if ($CURUSER['class'] > UC_MODERATOR) {
if ( isset($_POST['banned']) ) {
    $updateset[] = 'banned = "yes"';
    $_POST['visible'] = 0;
    }else
    $updateset[] = 'banned = "no"';
}
    $updateset[] = "visible = '" . ( isset($_POST['visible']) ? 'yes' : 'no') . "'";
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");
    write_log(sprintf($lang['takedit_log'], $id, $name, $CURUSER['username']));
    $returnto = "{$FMED['baseurl']}/details.php?id=$id&amp;edited=1";
header("Location: $returnto");
?> 