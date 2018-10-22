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
require_once("include/benc.php");
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
dbconn();
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('takeupload') );
if ($CURUSER['class'] < UC_UPLOADER)
    header( "Location: {$FMED['baseurl']}/upload.php" );
    foreach(explode(":","descr:type:name") as $v) {
if (!isset($_POST[$v]))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_formdata']);
}
if (!isset($_FILES["file"]))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_formdata']);
if (!empty($_POST['vip']))
    $vip = unesc($_POST['vip']);
if (!empty($_POST['box']))
    $box = unesc($_POST['box']);
if (!empty($_POST['poster']))
    $poster = unesc($_POST['poster']);
    $f = $_FILES["file"];
    $fname = unesc($f["name"]);
if (empty($fname))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_filename']);
    $nfo = sqlesc('');
if(isset($_FILES['nfo']) && !empty($_FILES['nfo']['name'])) {
    $nfofile = $_FILES['nfo'];
if ($nfofile['name'] == '')
    stderr($lang['takeupload_failed'], $lang['takeupload_no_nfo']);
if ($nfofile['size'] == 0)
    stderr($lang['takeupload_failed'], $lang['takeupload_0_byte']);
if ($nfofile['size'] > 65535)
    stderr($lang['takeupload_failed'], $lang['takeupload_nfo_big']);
    $nfofilename = $nfofile['tmp_name'];
if (@!is_uploaded_file($nfofilename))
    stderr($lang['takeupload_failed'], $lang['takeupload_nfo_failed']);
    $nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
}
    $descr = unesc($_POST["descr"]);
if (!$descr)
    stderr($lang['takeupload_failed'], $lang['takeupload_no_descr']);
if(isset($_POST['strip']) && $_POST['strip']){
    include 'include/strip.php';
    $descr = preg_replace("/[^\\x20-\\x7e\\x0a\\x0d]/", " ", $descr);
    strip($descr);
}
    $catid = (0 + $_POST["type"]);
if (!is_valid_id($catid))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_cat']);
if (!validfilename($fname))
    stderr($lang['takeupload_failed'], $lang['takeupload_invalid']);
if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
    stderr($lang['takeupload_failed'], $lang['takeupload_not_torrent']);
    $shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
    $torrent = unesc($_POST["name"]);
    $tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
    stderr($lang['takeupload_failed'], $lang['takeupload_eek']);
if (!filesize($tmpname))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_file']);
    $dict = bdec_file($tmpname, $FMED['max_torrent_size']);
if (!isset($dict))
    stderr($lang['takeupload_failed'], $lang['takeupload_not_benc']);
function dict_check($d, $s) {
global $lang;
if ($d["type"] != "dictionary")
    stderr($lang['takeupload_failed'], $lang['takeupload_not_dict']);
    $a = explode(":", $s);
    $dd = $d["value"];
    $ret = array();
    $t='';
    foreach ($a as $k) {
    unset($t);
if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
    $k = $m[1];
    $t = $m[2];
}
if (!isset($dd[$k]))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_keys']);
if (isset($t)) {
if ($dd[$k]["type"] != $t)
    stderr($lang['takeupload_failed'], $lang['takeupload_invalid_entry']);
    $ret[] = $dd[$k]["value"];
    }else
    $ret[] = $dd[$k];
}
    return $ret;
}
function dict_get($d, $k, $t) {
global $lang;
if ($d["type"] != "dictionary")
    stderr($lang['takeupload_failed'], $lang['takeupload_not_dict']);
    $dd = $d["value"];
if (!isset($dd[$k]))
    return;
    $v = $dd[$k];
if ($v["type"] != $t)
    stderr($lang['takeupload_failed'], $lang['takeupload_dict_type']);
    return $v["value"];
}
    list($ann, $info) = dict_check($dict, "announce(string):info");
    $tmaker = (isset($dict['value']['created by']) && !empty($dict['value']['created by']['value'])) ? sqlesc($dict['value']['created by']['value']) : sqlesc($lang['takeupload_unkown']);
    unset($dict);
    list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");
if (!in_array($ann, $FMED['announce_urls'], 1))
    stderr($lang['takeupload_failed'], sprintf($lang['takeupload_url'], $FMED['announce_urls'][0]));
if (strlen($pieces) % 20 != 0)
    stderr($lang['takeupload_failed'], $lang['takeupload_pieces']);
    $filelist = array();
    $totallen = dict_get($info, "length", "integer");
if (isset($totallen)) {
    $filelist[] = array($dname, $totallen);
    $type = "single";
    } else {
    $flist = dict_get($info, "files", "list");
if (!isset($flist))
    stderr($lang['takeupload_failed'], $lang['takeupload_both']);
if (!count($flist))
    stderr($lang['takeupload_failed'], $lang['takeupload_no_files']);
    $totallen = 0;
    foreach ($flist as $fn) {
    list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
    $totallen += $ll;
    $ffa = array();
    foreach ($ff as $ffe) {
if ($ffe["type"] != "string")
    stderr($lang['takeupload_failed'], $lang['takeupload_error']);
    $ffa[] = $ffe["value"];
}
if (!count($ffa))
    stderr($lang['takeupload_failed'], $lang['takeupload_error']);
    $ffe = implode("/", $ffa);
    $filelist[] = array($ffe, $ll);
}
    $type = "multi";
}
    $infohash = sha1($info["string"]);
    unset($info);
    $torrent = str_replace("_", " ", $torrent);
    $ret = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO torrents (search_text, filename, owner, visible, poster, vip, box, info_hash, name, size, numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo, client_created_by) VALUES (" . implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no",$poster, $vip, $box, $infohash, $torrent, $totallen, count($filelist), $type, $descr, $descr, 0 + $_POST["type"], $dname))) . ", " . time() . ", " . time() . ", $nfo, $tmaker)");
if (!$ret) {
if (mysqli_errno($GLOBALS["___mysqli_ston"]) == 1062)
    stderr($lang['takeupload_failed'], $lang['takeupload_already']);
    stderr($lang['takeupload_failed'], "mysql puked: ".mysqli_error($GLOBALS["___mysqli_ston"]));
}
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM files WHERE torrent = $id");
function file_list($arr,$id){
    foreach($arr as $v)
    $new[] = "($id,".sqlesc($v[0]).",".$v[1].")";
    return join(",",$new);
}
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO files (torrent, filename, size) VALUES ".file_list($filelist,$id));
    move_uploaded_file($tmpname, "{$FMED['torrent_dir']}/$id.torrent");
    write_log(sprintf($lang['takeupload_log'], $id, $torrent, $CURUSER['username']));
if (($fd1 = @fopen("rss.xml", "w")) && ($fd2 = fopen("rssdd.xml", "w"))){
    $cats = "";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, name FROM categories");
    while ($arr = mysqli_fetch_assoc($res))
    $cats[$arr["id"]] = $arr["name"];
    $s = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n<rss version=\"0.91\">\n<channel>\n" ."<title>{$FMED['site_name']}</title>\n<description>We are the best!</description>\n<link>{$FMED['baseurl']}/</link>\n";
    @fwrite($fd1, $s);
    @fwrite($fd2, $s);
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,name,descr,filename,category FROM torrents ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
    while ($a = mysqli_fetch_assoc($r)){
    $cat = $cats[$a["category"]];
    $s = "<item>\n<title>" . htmlspecialchars($a["name"] . " ($cat)") . "</title>\n" ."<description>" . htmlspecialchars($a["descr"]) . "</description>\n";
    @fwrite($fd1, $s);
    @fwrite($fd2, $s);
    @fwrite($fd1, "<link>{$FMED['baseurl']}/details.php?id=$a[id]&amp;hit=1</link>\n</item>\n");
    $filename = htmlspecialchars($a["filename"]);
    @fwrite($fd2, "<link>{$FMED['baseurl']}/download.php/$a[id]/$filename</link>\n</item>\n");
}
    $s = "</channel>\n</rss>\n";
    @fwrite($fd1, $s);
    @fwrite($fd2, $s);
    @fclose($fd1);
    @fclose($fd2);
}
    header("Location: {$FMED['baseurl']}/details.php?id=$id&uploaded=1");
?> 