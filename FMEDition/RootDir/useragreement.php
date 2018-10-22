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
require_once "include/html_functions.php";
require_once "include/user_functions.php";

dbconn();

    $lang = array_merge( load_language('global'), load_language('useragreement') );
    $HTMLOUT = '';
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= begin_frame($FMED['site_name']." {$lang['frame_usragrmnt']}");
    $HTMLOUT .= "<p></p> {$lang['text_usragrmnt']}"; 
    $HTMLOUT .= end_frame();
    $HTMLOUT .= end_main_frame();
    print stdhead("{$lang['stdhead_usragrmnt']}") . $HTMLOUT . stdfoot();
?>