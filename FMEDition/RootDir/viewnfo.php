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
require "include/user_functions.php";
require "include/bbcode_functions.php";
dbconn(false);
loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('viewnfo') );
    $id = 0 + $_GET["id"];
if ($CURUSER['class'] < UC_POWER_USER || !is_valid_id($id))
    die;
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT name,nfo FROM torrents WHERE id=$id") or sqlerr();
    $a = mysqli_fetch_assoc($r) or die("{$lang['text_puke']}");
    $HTMLOUT = '';
    $HTMLOUT .= "<h1>{$lang['text_nfofor']}<a href='details.php?id=$id'>".htmlspecialchars($a['name'])."</a></h1>\n";
    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'><tr><td class='text'>\n";
    $HTMLOUT .= "<pre>" . format_urls(htmlentities($a['nfo'], ENT_QUOTES, 'UTF-8')) . "</pre>\n";
    $HTMLOUT .= "</td></tr></table>\n";
    $HTMLOUT .= "<p align='center'>{$lang['text_forbest']}" ."<a href='ftp://{$_SERVER['HTTP_HOST']}/misc/linedraw.ttf'>{$lang['text_linedraw']}</a>{$lang['text_font']}</p>\n";
    print stdhead() . $HTMLOUT . stdfoot();
?> 