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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn(false);
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('friends') );
    $userid = isset($_GET['id']) ? (int)$_GET['id'] : $CURUSER['id'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';
if (!is_valid_id($userid))
    stderr($lang['friends_error'], $lang['friends_invalid_id']);
if ($userid != $CURUSER["id"])
    stderr($lang['friends_error'], $lang['friends_no_access']);
if ($action == 'add'){
    $targetid = 0+$_GET['targetid'];
    $type = $_GET['type'];
if (!is_valid_id($targetid))
    stderr($lang['friends_error'], $lang['friends_invalid_id']);
if ($type == 'friend'){
    $table_is = $frag = 'friends';
    $field_is = 'friendid';
    }elseif ($type == 'block'){
    $table_is = $frag = 'blocks';
    $field_is = 'blockid';
    }else
    stderr($lang['friends_error'], $lang['friends_unknown']);
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM $table_is WHERE userid=$userid AND $field_is=$targetid") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($r) == 1)
    stderr($lang['friends_error'], sprintf($lang['friends_already'], htmlentities($table_is)));
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO $table_is VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);
    header("Location: {$FMED['baseurl']}/friends.php?id=$userid#$frag");
    die;
}
if ($action == 'delete'){
    $targetid = (int)$_GET['targetid'];
    $sure = isset($_GET['sure']) ? htmlentities($_GET['sure']) : false;
    $type = isset($_GET['type']) ? ($_GET['type'] == 'friend' ? 'friend' : 'block') : stderr($lang['friends_error'], 'LoL');
if (!is_valid_id($targetid))
    stderr($lang['friends_error'], $lang['friends_invalid_id']);
if (!$sure)
    stderr("{$lang['friends_delete']} $type", sprintf($lang['friends_sure'], $type, $userid, $type, $targetid) );
if ($type == 'friend'){
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM friends WHERE userid=$userid AND friendid=$targetid") or sqlerr(__FILE__, __LINE__);
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0)
    stderr($lang['friends_error'], $lang['friends_no_friend']);
    $frag = "friends";
    }elseif ($type == 'block'){
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM blocks WHERE userid=$userid AND blockid=$targetid") or sqlerr(__FILE__, __LINE__);
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0)
    stderr($lang['friends_error'], $lang['friends_no_block']);
    $frag = "blocks";
    }else
    stderr($lang['friends_error'], $lang['friends_unknown']);
    header("Location: {$FMED['baseurl']}/friends.php?id=$userid#$frag");
    die;
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
    $user = mysqli_fetch_assoc($res) or stderr($lang['friends_error'], $lang['friends_no_user']);
    $HTMLOUT = '';
    $donor = ($user["donor"] == "yes") ? "<img src='{$FMED['pic_base_url']}starbig.gif' alt='{$lang['friends_donor']}' style='margin-left: 4pt' />" : '';
    $warned = ($user["warned"] == "yes") ? "<img src='{$FMED['pic_base_url']}warnedbig.gif' alt='{$lang['friends_warned']}' style='margin-left: 4pt' />" : '';
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    $count = mysqli_num_rows($res);
    $friends = '';
if( !$count){
    $friends = "<em>{$lang['friends_friends_empty']}.</em>";
    }else{
    while ($friend = mysqli_fetch_assoc($res)){
    $title = $friend["title"];
if (!$title)
    $title = get_user_class_name($friend["class"]);
    $userlink = "<a href='userdetails.php?id={$friend['id']}'><b>".htmlentities($friend['name'], ENT_QUOTES)."</b></a>";
    $userlink .= get_user_icons($friend) . " ($title)<br />{$lang['friends_last_seen']} " . get_date( $friend['last_access'],'');
    $delete = "<span class='btn'><a href='friends.php?id=$userid&amp;action=delete&amp;type=friend&amp;targetid={$friend['id']}'>{$lang['friends_remove']}</a></span>";
    $pm = "&nbsp;<span class='btn'><a href='sendmessage.php?receiver={$friend['id']}'>{$lang['friends_pm']}</a></span>";
    $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");
if (!$avatar)
    $avatar = "{$FMED['pic_base_url']}default_avatar.gif";
    $friends .= "<div style='border: 1px solid black;padding:5px;'>".($avatar ? "<img width='50px' src='$avatar' style='float:right;' alt='' />" : ""). "<p >{$userlink}<br /><br />{$delete}{$pm}</p></div><br />";
    }
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT b.blockid as id, u.username AS name, u.donor, u.warned, u.enabled, u.last_access FROM blocks AS b LEFT JOIN users as u ON b.blockid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    $blocks = '';
if(mysqli_num_rows($res) == 0){
    $blocks = "{$lang['friends_blocks_empty']}<em>.</em>";
    }else{
    while ($block = mysqli_fetch_assoc($res)){
    $blocks .= "<div style='border: 1px solid black;padding:5px;'>";
    $blocks .= "<span class='btn' style='float:right;'><a href='friends.php?id=$userid&amp;action=delete&amp;type=block&amp;targetid={$block['id']}'>{$lang['friends_delete']}</a></span><br />";
    $blocks .= "<p><a href='userdetails.php?id={$block['id']}'><b>" . htmlentities($block['name'], ENT_QUOTES) . "</b></a>";
    $blocks .= get_user_icons($block) . "</p></div><br />";
    }
}
    $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='0'>
    "."
    <tr>
    <td class='embedded'><h1 style='margin:0px'> {$lang['friends_personal']} <b><font color='#" . get_user_class_color($user['class']) . "'> " . htmlspecialchars($user['username']) . "</font></b></h1>$donor$warned</td></tr></table>";
    $HTMLOUT .= "<table class='main' width='750' border='0' cellspacing='0' cellpadding='0'>
    <tr>
      <td class='colhead'><h2 align='left' style='width:50%;'><a name='friends'>{$lang['friends_friends_list']}</a></h2></td>
      <td class='colhead'><h2 align='left' style='width:50%;vertical-align:top;'><a name='blocks'>{$lang['friends_blocks_list']}</a></h2></td>
    </tr>
    <tr>
      <td style='padding:10px;background-color:#ECE9D8;width:50%;'>$friends</td>
      <td style='padding:10px;background-color:#ECE9D8' valign='top'>$blocks</td>
    </tr>
    </table>";
    $HTMLOUT .= " <p><a href='users.php'><b>{$lang['friends_user_list']}</b></a></p>";
    print stdhead("{$lang['friends_stdhead']} {$user['username']}") . $HTMLOUT . stdfoot();
?>