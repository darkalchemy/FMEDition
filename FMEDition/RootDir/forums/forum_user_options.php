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
    print "{$lang['forum_user_options_access']}";
    exit();
}
if ($action == "editpost"){
    $postid = 0+$_GET["postid"];
if (!is_valid_id($postid))
    stderr("{$lang['forum_user_options_user_error']}", "{$lang['forum_user_options_incorrect']}");
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) != 1)
    stderr("{$lang['forum_user_options_error']}", "{$lang['forum_user_options_no_post']}");
    $arr = mysqli_fetch_assoc($res);
    $res2 = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT locked FROM topics WHERE id = " . $arr["topicid"]) or sqlerr(__FILE__, __LINE__);
    $arr2 = mysqli_fetch_assoc($res2);
if (mysqli_num_rows($res) != 1)
    stderr("{$lang['forum_user_options_error']}", "{$lang['forum_user_options_associated_topic']}");
    $locked = ($arr2["locked"] == 'yes');
if (($CURUSER["id"] != $arr["userid"] || $locked) && get_user_class() < UC_MODERATOR)
    stderr("{$lang['forum_user_options_error']}", "{$lang['forum_user_options_denied']}");
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $body = $_POST['body'];
if ($body == "")
    stderr("{$lang['forum_user_options_error']}", "{$lang['forum_user_options_body']}");
    $body = sqlesc($body);
    $editedat = time();
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE posts SET body=$body, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$postid") or sqlerr(__FILE__, __LINE__);
    $returnto = $_POST["returnto"];
if ($returnto != ""){
    $returnto .= "&page=p$postid#$postid";
    header("Location: $returnto");
    }else
    stderr("{$lang['forum_user_options_success']}", "{$lang['forum_user_options_edit_success']}");
}
    $HTMLOUT = '';
    $HTMLOUT .= "<h1>{$lang['forum_user_options_edit_post_header']}</h1>
    <form method='post' action='forums.php?action=editpost&amp;postid=$postid'>
    <input type='hidden' name='returnto' value='" . htmlspecialchars($_SERVER["HTTP_REFERER"]) . "' />
      <table border='1' cellspacing='0' cellpadding='5'>
        <tr>
          <td style='padding: 0px'>
            <textarea name='body' cols='100' rows='20' style='border: 0px'>" . htmlspecialchars($arr["body"]) . "</textarea>
          </td>
        </tr>
        <tr>
          <td align='center'>
            <input type='submit' value='{$lang['forum_user_options_okay']}' class='btn' />
          </td>
        </tr>
      </table>
      </form>\n";
    print stdhead('Editing post') . $HTMLOUT . stdfoot();
    die;
}
if ($action == "deletepost"){
    $postid = isset($_GET["postid"]) ? (int)$_GET["postid"] : 0;
    $forumid = isset($_GET["forumid"]) ? (int)$_GET["forumid"] : 0;
    $sure = isset($_GET["sure"]) ? $_GET["sure"] : 0;
if ( get_user_class() < UC_MODERATOR || !is_valid_id($postid) || !is_valid_id($forumid) )
    stderr("{$lang['forum_user_options_user_error']}", "{$lang['forum_user_options_access']}");
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT topicid FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_row($res) or stderr("{$lang['forum_user_options_error']}", "{$lang['forum_user_options_not_found']}");
    $topicid = $arr[0];
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_row($res);
if ($arr[0] < 2){
    $err = "<form method='post' action='forums.php?action=deletetopic'>
    <input name='action' value='deletetopic' type='hidden'>
    <input name='topicid' value='$topicid' type='hidden'>
    <input name='forumid' value='$forumid' type='hidden'>
    <input name='sure' value='1' type='checkbox'>I'm sure
    <input value='Delete Topic' type='submit'>
    </form>";
    stderr("{$lang['forum_user_options_error']}", "{$lang['forum_user_options_no_delete']}" ."{$lang['forum_user_options_delete_topic']}<br />$err");
}
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM posts WHERE topicid=$topicid AND id < $postid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) == 0){
    $redirtopost = "";
    }else{
    $arr = mysqli_fetch_row($res);
    $redirtopost = "&amp;page=p{$arr[0]}#{$arr[0]}";
}
if (!$sure){
    stderr("{$lang['forum_user_options_delete_post']}", "{$lang['forum_user_options_sanity_check']}" ."<a href='forums.php?action=deletepost&amp;postid=$postid&amp;forumid=$forumid&amp;sure=1'>{$lang['forum_user_options_here']}</a> {$lang['forum_user_options_sure']}");
}
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);
    update_topic_last_post($topicid);
    header("Location: {$FMED['baseurl']}/forums.php?action=viewtopic&topicid=$topicid$redirtopost");
die;
}
?> 