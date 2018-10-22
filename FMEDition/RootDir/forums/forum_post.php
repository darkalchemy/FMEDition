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
    print "{$lang['forum_post_access']}";
    exit();
}
    $forumid = isset($_POST["forumid"]) ? (int)$_POST["forumid"] : 0;
    $topicid = isset($_POST["topicid"]) ? (int)$_POST["topicid"] : 0;
if (!is_valid_id($forumid) && !is_valid_id($topicid))
    stderr("{$lang['forum_post_error']}", "{$lang['forum_post_bad_id']}");
    $newtopic = $forumid > 0;
if ($newtopic){
    $subject = trim(strip_tags($_POST["subject"]));
if (!$subject)
    stderr("{$lang['forum_post_error']}", "{$lang['forum_post_subject']}");
if (strlen($subject) > $maxsubjectlength)
    stderr("{$lang['forum_post_error']}", "{$lang['forum_post_subject_limit']}");
    }else
    $forumid = get_topic_forum($topicid) or die("{$lang['forum_post_bad_topic']}");
    $arr = get_forum_access_levels($forumid) or die("{$lang['forum_post_bad_forum']}");
if (get_user_class() < $arr["write"] || ($newtopic && get_user_class() < $arr["create"]))
    stderr("{$lang['forum_post_error']}", "{$lang['forum_post_denied']}");
    $body = trim($_POST["body"]);
if ($body == "")
    stderr("{$lang['forum_post_error']}", "{$lang['forum_post_body']}");
    $userid = $CURUSER["id"];
if ($newtopic){
    $subject = sqlesc($subject);
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO topics (userid, forumid, subject) VALUES($userid, $forumid, $subject)") or sqlerr(__FILE__, __LINE__);
    $topicid = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res) or stderr("{$lang['forum_post_error']}", "{$lang['forum_post_topic_id']}");
    }else{
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res) or die("{$lang['forum_post_topic_na']}");
if ($arr["locked"] == 'yes' && get_user_class() < UC_MODERATOR)
    stderr("{$lang['forum_post_error']}", "{$lang['forum_post_locked']}");
    $forumid = $arr["forumid"];
}
    $added = time();
    $body = sqlesc($body);
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO posts (topicid, userid, added, body) " ."VALUES($topicid, $userid, $added, $body)") or sqlerr(__FILE__, __LINE__);
    $postid = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res) or die("{$lang['forum_post_post_na']}");
    update_topic_last_post($topicid);
    $headerstr = "Location: {$FMED['baseurl']}/forums.php?action=viewtopic&topicid=$topicid&page=last";
if ($newtopic)
    header($headerstr);
    else
    header("$headerstr#$postid");
die;
?> 