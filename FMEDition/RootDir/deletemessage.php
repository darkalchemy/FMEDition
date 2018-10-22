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
require "include/bittorrent.php";
  $id = 0+$_GET["id"];
if (!is_numeric($id) || $id < 1 || floor($id) != $id)
    die;
    $type = $_GET["type"];
dbconn(false);
loggedinorreturn();
  $lang = array_merge( load_language('global'), load_language('deletemessage') );
if ($type == 'in'){
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT receiver, location FROM messages WHERE id=" . sqlesc($id)) or die("barf");
    $arr = mysqli_fetch_assoc($res) or die("{$lang['deletemessage_bad_id']}");
if ($arr["receiver"] != $CURUSER["id"])
    die("{$lang['deletemessage_dont_do']}");
if ($arr["location"] == 'in')
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM messages WHERE id=" . sqlesc($id)) or die("{$lang['deletemessage_code1']}");
    else if ($arr["location"] == 'both')
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE messages SET location = 'out' WHERE id=" . sqlesc($id)) or die("{$lang['deletemessage_code2']}");
    else
    die("{$lang['deletemessage_not_inbox']}");
    }elseif ($type == 'out'){
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT sender, location FROM messages WHERE id=" . sqlesc($id)) or die("barf");
    $arr = mysqli_fetch_assoc($res) or die("{$lang['deletemessage_bad_id']}");
if ($arr["sender"] != $CURUSER["id"])
    die("{$lang['deletemessage_dont_do']}");
if ($arr["location"] == 'out')
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM messages WHERE id=" . sqlesc($id)) or die("{$lang['deletemessage_code3']}");
    else if ($arr["location"] == 'both')
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE messages SET location = 'in' WHERE id=" . sqlesc($id)) or die("{$lang['deletemessage_code4']}");
    else
    die("{$lang['deletemessage_sentbox']}");
    }else
    die("{$lang['deletemessage_unknown']}");
  header("Location: {$FMED['baseurl']}/inbox.php".($type == 'out'?"?out=1":""));
?> 