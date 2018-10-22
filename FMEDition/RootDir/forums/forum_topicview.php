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
    print "{$lang['forum_topic_view_access']}";
    exit();
}
    $topicid = (int)$_GET["topicid"];
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : false;
if (!is_valid_id($topicid))
    stderr("{$lang['forum_topic_view_user_error']}", "{$lang['forum_topic_view_incorrect']}");
    $userid = $CURUSER["id"];
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res) or stderr("{$lang['forum_topic_view_forum_error']}", "{$lang['forum_topic_view_notfound']}");
    $locked = ($arr["locked"] == 'yes');
    $subject = htmlspecialchars($arr["subject"], ENT_QUOTES, 'UTF-8');
    $sticky = $arr["sticky"] == "yes";
    $forumid = $arr["forumid"];
    $maypost = false;
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE topics SET views = views + 1 WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res) or die("{$lang['forum_topic_view_null']}");
    $forum = $arr["name"];
if ($CURUSER["class"] < $arr["minclassread"])
    stderr("{$lang['forum_topic_view_error']}", "{$lang['forum_topic_view_not_permitted']}");
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_row($res);
    $postcount = $arr[0];
    $pagemenu = "<p>\n";
    $perpage = $postsperpage;
    $pages = ceil($postcount / $perpage);
if ($page[0] == "p"){
    $findpost = substr($page, 1);
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM posts WHERE topicid=$topicid ORDER BY added") or sqlerr(__FILE__, __LINE__);
    $i = 1;
    while ($arr = mysqli_fetch_row($res)){
if ($arr[0] == $findpost)
    break;
    ++$i;
}
    $page = ceil($i / $perpage);
}
if ($page == "last")
    $page = $pages;
    else{
if($page < 1)
    $page = 1;
    elseif ($page > $pages)
    $page = $pages;
}
    $offset = $page * $perpage - $perpage;
    for ($i = 1; $i <= $pages; ++$i){
if ($i == $page)
    $pagemenu .= "<font class='gray'><b>$i</b></font>\n";
    else
    $pagemenu .= "<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=$i'><b>$i</b></a>\n";
}
if ($page == 1)
    $pagemenu .= "<br /><font class='gray'><b>{$lang['forum_topic_view_prev']}</b></font>";
    else
    $pagemenu .= "<br /><a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=" . ($page - 1) ."'><b>{$lang['forum_topic_view_prev']}</b></a>";
    $pagemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if ($page == $pages)
    $pagemenu .= "<font class='gray'><b>{$lang['forum_topic_view_next']}</b></font></p>\n";
    else
    $pagemenu .= "<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=" . ($page + 1) ."'><b>{$lang['forum_topic_view_next']}</b></a></p>\n";
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT p. * , u.username, u.class, u.avatar, u.av_w, u.av_h,
    u.donor, u.title, u.enabled, u.warned, u.reputation
    FROM posts p
    LEFT JOIN users u ON u.id = p.userid
    WHERE topicid = $topicid ORDER BY p.id LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT = '';
    $HTMLOUT .= "<script type='text/javascript' src='./scripts/popup.js'></script>";
    $HTMLOUT .= "<a name='top'></a><a href='forums.php?action=viewforum&amp;forumid=$forumid'>$forum</a> &gt; $subject\n";
    $HTMLOUT .= $pagemenu;
    $HTMLOUT .= begin_main_frame();
    $pc = mysqli_num_rows($res);
    $pn = 0;
    $r = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT lastpostread FROM readposts WHERE userid=" . $CURUSER["id"] . " AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
    $a = mysqli_fetch_row($r);
    $lpr = $a[0];
    while ($arr = mysqli_fetch_assoc($res)){
    ++$pn;
    $postid = $arr["id"];
    $posterid = $arr["userid"];
    $added = get_date( $arr['added'],'');
    $postername = $arr["username"];
if ($postername == ""){
    $by = sprintf($lang['forum_topic_view_unknown'], $posterid);
    }else{
    $title = $arr["title"];
if (!$title)
    $title = get_user_class_name($arr["class"]);
    $by = "<a href='userdetails.php?id=$posterid'><b><font color='#" . get_user_class_color($arr['class']) . "'> " . htmlspecialchars($arr['username']) . "</font></b></a>"
    . ($arr["donor"] == "yes" ? "<img src='{$FMED['pic_base_url']}star.gif' alt='{$lang['forum_topic_view_donor']}' />" : "")
    . ($arr["enabled"] == "no" ? "<img src='{$FMED['pic_base_url']}disabled.gif' alt='{$lang['forum_topic_view_disabled']}' style='margin-left: 2px' />" : ($arr["warned"] == "yes" ? "<a href='rules.php#warning' class='altlink'><img src='{$FMED['pic_base_url']}warned.gif' alt='{$lang['forum_topic_view_warned']}' border='0' /></a>" : "")) . " ($title)";
}
if ($CURUSER["avatars"] == "yes"){
    $avatar = $arr['avatar'] ? "<div style='text-align:center;padding:5px;'><img width='{$arr['av_w']}' height='{$arr['av_h']}' src='".htmlentities($arr['avatar'], ENT_QUOTES)."' alt='' /></div>" : "<img width='100' src='{$forum_pic_url}default_avatar.gif' alt='' />";
    }else{
    $avatar = "<img width='100' src='{$forum_pic_url}default_avatar.gif' alt='' />";
}
    $HTMLOUT .= "<a name='$postid'></a>\n";
if ($pn == $pc){
    $HTMLOUT .= "<a name='last'></a>\n";
}
    $HTMLOUT .= "<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded' width='99%'>#$postid by $by at $added";
if (!$locked || get_user_class() >= UC_MODERATOR)
    $HTMLOUT .= " - [<a href='forums.php?action=quotepost&amp;topicid=$topicid&amp;postid=$postid&amp;forumid=$forumid'><b>{$lang['forum_topic_view_quote']}</b></a>]";
if (($CURUSER["id"] == $posterid && !$locked) || get_user_class() >= UC_MODERATOR)
    $HTMLOUT .= " - [<a href='forums.php?action=editpost&amp;postid=$postid&amp;forumid=$forumid'><b>{$lang['forum_topic_view_edit']}</b></a>]";
if (get_user_class() >= UC_MODERATOR)
    $HTMLOUT .= " - [<a href='forums.php?action=deletepost&amp;postid=$postid&amp;forumid=$forumid'><b>{$lang['forum_topic_view_delete']}</b></a>]";
    $HTMLOUT .= "</td><td class='embedded' width='1%'><a href='#top'><img src='{$forum_pic_url}top.gif' border='0' alt='{$lang['forum_topic_view_top']}' /></a></td></tr>";
    $HTMLOUT .= "</table>\n";
    $HTMLOUT .= begin_table(true);
    $body = wordwrap( format_comment($arr["body"]), 80, "\n", true);
if (is_valid_id($arr['editedby'])){
    $res2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT username, class FROM users WHERE id={$arr['editedby']}");
if (mysqli_num_rows($res2) == 1){
    $arr2 = mysqli_fetch_assoc($res2);
    $body .= "<p><font size='1' class='small'>{$lang['forum_topic_view_edit_by']}<a href='userdetails.php?id={$arr['editedby']}'><b><font color='#" . get_user_class_color($arr2['class']) . "'> " . htmlspecialchars($arr2['username']) . "</font></b></a> on ".get_date( $arr['editedat'],'')."</font></p>\n";
    }
}
    $member_reputation = $arr['username'] != '' ? get_reputation($arr) : '';
    $HTMLOUT .= "<tr valign='top'>
    <td width='150' align='center' style='padding: 0px'>" .($avatar ? $avatar : ""). "<br /><div>$member_reputation</div></td>
    <td class='comment'>$body</td>
    </tr>\n";
    $HTMLOUT .= end_table();
    $postadd = $arr['added'];
if (($postid > $lpr) AND ($postadd > (time() - $FMED['readpost_expiry']))){
if ($lpr){
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE readposts SET lastpostread=$postid WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
    }else{
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO readposts (userid, topicid, lastpostread) VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);
    }
  }
}
    $HTMLOUT .= end_main_frame();
    $HTMLOUT .= $pagemenu;
if ($locked && get_user_class() < UC_MODERATOR)
    $HTMLOUT .= "<p>{$lang['forum_topic_view_locked']}</p>\n";
    else{
    $arr = get_forum_access_levels($forumid) or die;
if (get_user_class() < $arr["write"]){
    $HTMLOUT .= "<p><i>{$lang['forum_topic_view_permission']}</i></p>\n";
    }else{
    $maypost = true;
    }
}
    $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='0'>
    <tr>
        <td class='embedded'>
        <form method='post' action='forums.php?action=viewunread'>
        <input type='hidden' name='action' value='viewunread' />
        <input type='submit' value='{$lang['forum_topic_view_unread']}' class='btn' />
    </form>
    </td>\n";
if ($maypost){
    $HTMLOUT .= "<td class='embedded' style='padding-left: 10px'>
          <form method='post' action='forums.php?action=reply&amp;topicid={$topicid}'>
          <input type='hidden' name='action' value='reply' />
          <input type='hidden' name='topicid' value='$topicid' />
          <input type='submit' value='Add Reply' class='btn' />
    </form>
    </td>\n";
}
    $HTMLOUT .= "</tr></table>\n";
if (get_user_class() >= UC_MODERATOR){
    $req_uri = htmlentities($_SERVER['PHP_SELF']);
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,name,minclasswrite FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='0'>
    <tr>
        <td align='right'>{$lang['forum_topic_view_sticky']}</td>
    <td>
        <form method='post' action='forums.php?action=setsticky'>
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='returnto' value='{$req_uri}' />
        <input type='radio' name='sticky' value='yes' " . ($sticky ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_yes']} <input type='radio' name='sticky' value='no' " . (!$sticky ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_no']}
        <input type='submit' value='{$lang['forum_topic_view_set']}' />
    </form>
    </td>
    </tr>
    <tr>
        <td align='right'>{$lang['forum_topic_view_set_locked']}</td>
    <td>
        <form method='post' action='forums.php?action=setlocked'>
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='returnto' value='{$req_uri}' />
        <input type='radio' name='locked' value='yes' " . ($locked ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_yes']} <input type='radio' name='locked' value='no' " . (!$locked ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_no']}
        <input type='submit' value='{$lang['forum_topic_view_set']}' /></form>
    </td>
    </tr>
    <tr>
        <td align='right'>{$lang['forum_topic_view_rename']}</td>
    <td>
        <form method='post' action='forums.php?action=renametopic'>
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='returnto' value='{$req_uri}' />
        <input type='text' name='subject' size='60' maxlength='$maxsubjectlength' value='" . htmlspecialchars($subject) . "' />
        <input type='submit' value='{$lang['forum_topic_view_okay']}' />
    </form>
    </td>
    </tr>
    <tr>
        <td align='right'>Move this thread to:&nbsp;</td>
    <td>
        <form method='post' action='forums.php?action=movetopic'>
        <input type='hidden' name='topicid' value='$topicid' />
    <select name='forumid'>";
    while ($arr = mysqli_fetch_assoc($res))
if ($arr["id"] != $forumid && get_user_class() >= $arr["minclasswrite"])
    $HTMLOUT .= "<option value='{$arr["id"]}'>{$arr["name"]}</option>\n";
    $HTMLOUT .= "</select>
    <input type='submit' value='{$lang['forum_topic_view_okay']}' />
    </form>
    </td>
    </tr>
    <tr>
        <td align='right'>{$lang['forum_topic_view_delete_topic']}</td>
    <td>
        <form method='post' action='forums.php?action=deletetopic'>
        <input type='hidden' name='action' value='deletetopic' />
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='forumid' value='$forumid' />
        <input type='checkbox' name='sure' value='1' />I'm sure
        <input type='submit' value='{$lang['forum_topic_view_okay']}' />
    </form>
    </td>
    </tr>
    </table>\n";
}
    $HTMLOUT .= insert_quick_jump_menu($forumid);
    print stdhead("{$lang['forum_topic_view_view_topic']}") . $HTMLOUT . stdfoot();
die;
?> 