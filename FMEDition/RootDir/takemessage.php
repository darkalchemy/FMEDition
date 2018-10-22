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
if ($_SERVER["REQUEST_METHOD"] != "POST")
    stderr("Error", "Method");
dbconn();
loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('takemessage') );
function ratios($up, $down){
if ($down > 0){
    $ratio = number_format($up / $down, 3);
    return "<font color='" . get_ratio_color($ratio) . "'>$ratio</font>";
    }else{
if ($up > 0)
    return $lang['takemessage_inf'];
    else
    return "---";
}
    return;
}
    $n_pms = isset($_POST["n_pms"]) ? $_POST["n_pms"] : false;
if ($n_pms){
if ($CURUSER['class'] < UC_MODERATOR)
    stderr($lang['takemessage_error'], $lang['takemessage_denied']);
    $msg = trim($_POST["msg"]);
if (!$msg)
    stderr($lang['takemessage_error'],$lang['takemessage_something']);
    $subject = trim($_POST['subject']);
    $sender_id = ($_POST['sender'] == $lang['takemessage_system'] ? 0 : $CURUSER['id']);
    foreach( explode(':', $_POST['pmees']) as $k => $v ) {
if( ctype_digit($v) )
    $from_is[] = sqlesc($v);
}
    $from_is = "FROM users u WHERE u.id IN (" . join(',', $from_is) .")";
    $query = "INSERT INTO messages (sender, receiver, added, msg, subject, location, poster) "."SELECT $sender_id, u.id, " . time() . ", " . sqlesc($msg) .", ". sqlesc($subject).", 1, $sender_id " . $from_is;
    mysqli_query($GLOBALS["___mysqli_ston"], $query) or sqlerr(__FILE__, __LINE__);
    $n = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
    $snapshot = isset($_POST['snap']) ? $_POST['snap'] : '';
if ($comment || $snapshot){
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT u.id, u.uploaded, u.downloaded, u.modcomment ".$from_is) or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0){
    $l = 0;
    while ($user = mysqli_fetch_assoc($res)){
    unset($new);
    $new = '';
    $old = $user['modcomment'];
if ($comment)
    $new .= $comment;
if ($snapshot){
    $new .= ($new?"\n":"") .
    "{$lang['takemessage_mmed']}, " . gmdate("Y-m-d") . ", " .
    "{$lang['takemessage_ul']}: " . mksize($user['uploaded']) . ", " .
    "{$lang['takemessage_dl']}: " . mksize($user['downloaded']) . ", " .
    "{$lang['takemessage_r']}: " . ratios($user['uploaded'],$user['downloaded']) . " - " .
    ($_POST['sender'] == $lang['takemessage_system'] ? $lang['takemessage_System']:$CURUSER['username']);
}
    $new .= $old?("\n".$old):$old;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET modcomment = " . sqlesc($new) . " WHERE id = " . $user['id']) or sqlerr(__FILE__, __LINE__);
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    $l++;
    }
  }
}
    }else{                                                                                                           //////  PM  ///
    $receiver = isset($_POST["receiver"]) ? $_POST["receiver"] : false;
    $origmsg = isset($_POST["origmsg"]) ? $_POST["origmsg"] : false;
    $save = isset($_POST["save"]) ? $_POST["save"] : false;
    $returnto = isset($_POST["returnto"]) ? $_POST["returnto"] : '';
if (!is_valid_id($receiver) || ($origmsg && !is_valid_id($origmsg)))
    stderr($lang['takemessage_error'], $lang['takemessage_id']);
    $msg = trim($_POST["msg"]);
if (!$msg)
    stderr($lang['takemessage_error'], $lang['takemessage_something']);
    $save = ($save == 'yes') ? "yes" : "no";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT acceptpms, email, notifs, last_access as la FROM users WHERE id=$receiver") or sqlerr(__FILE__, __LINE__);
    $user = mysqli_fetch_assoc($res);
if (!$user)
    stderr($lang['takemessage_error'], $lang['takemessage_no_user']);
if ($CURUSER['class'] < UC_MODERATOR){
if ($user["acceptpms"] == "yes"){
    $res2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM blocks WHERE userid=$receiver AND blockid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res2) == 1)
    stderr($lang['takemessage_refused'], $lang['takemessage_blocked']);
    }elseif ($user["acceptpms"] == "friends"){
    $res2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM friends WHERE userid=$receiver AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res2) != 1)
    stderr($lang['takemessage_refused'], $lang['takemessage_friends']);
    }elseif ($user["acceptpms"] == "no")
    stderr($lang['takemessage_refused'], $lang['takemessage_no_pms']);
}
    $subject = trim($_POST['subject']);
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (poster, sender, receiver, added, msg, subject, saved, location) VALUES(" . $CURUSER["id"] . ", " . $CURUSER["id"] . ", $receiver, " . time() . ", " . sqlesc($msg) . ", " . sqlesc($subject) . ", " . sqlesc($save) . ", 1)") or sqlerr(__FILE__, __LINE__);
if (strpos($user['notifs'], '[pm]') !== false){
if (time() - $user["la"] >= 300){
$username = $CURUSER["username"];
$body = <<<EOD
You have received a PM from $username!

You can use the URL below to view the message (you may have to login).

{$FMED['baseurl']}/messages.php

--
{$FMED['site_name']}
EOD;
    @mail($user["email"], "{$lang['takemessage_received']} " . $username . "!",
    $body, "{$lang['takemessage_from']} {$FMED['site_email']}");
    }
}
    $delete = isset($_POST["delete"]) ? $_POST["delete"] : '';
if ($origmsg){
if ($delete == "yes"){
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) == 1){
    $arr = mysqli_fetch_assoc($res);
if ($arr["receiver"] != $CURUSER["id"])
    stderr($lang['takemessage_woot'], $lang['takemessage_happen']);
if ($arr["saved"] == "no")
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
    elseif ($arr["saved"] == "yes")
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE messages SET location = '0' WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
    }
}
if (!$returnto)
    $returnto = "{$FMED['baseurl']}/messages.php";
}
if ($returnto){
    header("Location: $returnto");
    die;
    }
}
    $l = (isset($l)?$l:'');
    stderr($lang['takemessage_succeed'], (($n_pms > 1) ? "$n {$lang['takemessage_out_of']} $n_pms {$lang['takemessage_were']}" : "{$lang['takemessage_msg_was']}")." {$lang['takemessage_sent']}" . ($l ? " $l {$lang['takemessage_comment']}" . (($l>1) ? "{$lang['takemessage_s_were']}" : " {$lang['takemessage_was']}") . " {$lang['takemessage_updated']}" : ""));
exit;
?> 