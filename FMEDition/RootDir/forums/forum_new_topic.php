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
if ( ! defined( 'IN_FMED_FORUM' ) ){
    print "{$lang['forum_new_topic_access']}";
    exit();
}
    $forumid = (int)$_GET["forumid"];
if (!is_valid_id($forumid))
    header("Location: {$FMED['baseurl']}/forums.php");
    $HTMLOUT = stdhead("{$lang['forum_new_topic_newtopic']}");
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= insert_compose_frame($forumid);
    $HTMLOUT .= end_main_frame();
    $HTMLOUT .= stdfoot();
    print $HTMLOUT;
die;
?> 