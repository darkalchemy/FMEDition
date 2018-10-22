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
    print "{$lang['forum_quote_post_access']}";
    exit();
}
    $topicid = (int)$_GET["topicid"];
if (!is_valid_id($topicid))
    stderr("{$lang['forum_quote_post_error']}", "{$lang['forum_quote_post_invalid']}");
    $HTMLOUT = stdhead("{$lang['forum_quote_post_reply']}");
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= insert_compose_frame($topicid, false, true);
    $HTMLOUT .= end_main_frame();
    $HTMLOUT .= stdfoot();
    print $HTMLOUT;
die;
?> 