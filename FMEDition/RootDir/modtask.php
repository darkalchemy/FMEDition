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

    $lang = load_language('modtask');
if ($CURUSER['class'] < UC_MODERATOR) stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
if ((isset($_POST['action'])) && ($_POST['action'] == "edituser")){
if (isset($_POST['userid'])) $userid = $_POST['userid'];
    else stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
if (!is_valid_id($userid)) stderr("{$lang['modtask_error']}", "{$lang['modtask_bad_id']}");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
    $user = mysqli_fetch_assoc($res) or sqlerr(__FILE__, __LINE__);
if ($CURUSER["class"] <= $user['class'] && ($CURUSER['id']!= $userid && $CURUSER["class"] < UC_ADMINISTRATOR))
    stderr('Error','You cannot edit someone of the same or higher class.. injecting stuff arent we? Action logged');
    $updateset = array();
    $modcomment = (isset($_POST['modcomment']) && $CURUSER['class'] == UC_SYSOP) ? $_POST['modcomment'] : $user['modcomment'];
if ((isset($_POST['class'])) && (($class = $_POST['class']) != $user['class'])){
if ($class >= UC_SYSOP || ($class >= $CURUSER['class']) || ($user['class'] >= $CURUSER['class']))
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
if (!is_valid_user_class($class) || $CURUSER["class"] <= $_POST['class']) stderr( ("Error"), "Bad class :P");
    $what = ($class > $user['class'] ? "{$lang['modtask_promoted']}" : "{$lang['modtask_demoted']}");
    $msg = sqlesc(sprintf($lang['modtask_have_been'], $what)." '" . get_user_class_name($class) . "' {$lang['modtask_by']} ".$CURUSER['username']);
    $added = time();
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "class = ".sqlesc($class);
    $modcomment = get_date( time(), 'DATE', 1 ) . " - $what to '" . get_user_class_name($class) . "' by $CURUSER[username].\n". $modcomment;
}
if (isset($_POST['warned']) && (($warned = $_POST['warned']) != $user['warned'])){
    $updateset[] = "warned = " . sqlesc($warned);
    $updateset[] = "warneduntil = 0";
if ($warned == 'no'){
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_warned']}" . $CURUSER['username'] . ".\n". $modcomment;
    $msg = sqlesc("{$lang['modtask_warned_removed']}" . $CURUSER['username'] . ".");
    $added = time();
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
}
if (isset($_POST['warnlength']) && ($warnlength = 0 + $_POST['warnlength'])){
    unset($warnpm);
if (isset($_POST['warnpm'])) $warnpm = $_POST['warnpm'];
if ($warnlength == 255){
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_warned_by']}" . $CURUSER['username'] . ".\n{$lang['modtask_reason']} $warnpm\n" . $modcomment;
    $msg = sqlesc("{$lang['modtask_warning_received']}".$CURUSER['username'].($warnpm ? "\n\n{$lang['modtask_reason']} $warnpm" : ""));
    $updateset[] = "warneduntil = 0";
    }else{
    $warneduntil = (time() + $warnlength * 604800);
    $dur = $warnlength . "{$lang['modtask_week']}" . ($warnlength > 1 ? "s" : "");
    $msg = sqlesc(sprintf($lang['modtask_warning_duration'], $dur).$CURUSER['username'].($warnpm ? "\n\nReason: $warnpm" : ""));
    $modcomment = get_date( time(), 'DATE', 1 ) . sprintf($lang['modtask_warned_for'], $dur) . $CURUSER['username'] . ".\n{$lang['modtask_reason']} $warnpm\n" . $modcomment;
    $updateset[] = "warneduntil = ".sqlesc($warneduntil);
}
    $added = time();
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "warned = 'yes'";
}
if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor'])){
    $updateset[] = "donor = " . sqlesc($donor);
    $updateset[] = "donoruntil = 0";
if ($donor == 'no'){
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_removed']}".$CURUSER['username'].".\n". $modcomment;
    $msg = sqlesc("{$lang['modtask_donor_expired']}");
    $added = time();
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }
}
if ((isset($_POST['donorlength'])) && ($donorlength = 0 + $_POST['donorlength'])) {
if ($donorlength == 255) {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_set']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("You have received donor status from " . $CURUSER['username']);
    $subject = sqlesc("Thank You for Your Donation!");
    $updateset[] = "donoruntil = '0'";
    } else {
    $donoruntil = (time() + $donorlength * 604800);
    $dur = $donorlength . " week" . ($donorlength > 1 ? "s" : "");
    $msg = sqlesc("Dear " . $user['username'] . "
    :wave:
    Thanks for your support to {$FMED['site_name']} !
    Your donation helps us in the costs of running the site!
    As a donor, you are given some bonus gigs added to your uploaded amount, the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

    so, thanks again, and enjoy!
    cheers,
    {$FMED['site_name']} Staff

    PS. Your donator status will last for $dur and can be found on your user details page and can only be seen by you :smile: It was set by " .$CURUSER['username']);
    $subject = sqlesc("Thank You for Your Donation!");
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_set']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "donoruntil = " . sqlesc($donoruntil);
    $updateset[] = "vipclass_before = " . $user["class"];
}
    $added = sqlesc(time());
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__file__, __line__);
    $updateset[] = "donor = 'yes'";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT class FROM users WHERE id = $userid") or sqlerr(__file__,__line__);
    $arr = mysqli_fetch_array($res);
if ($user['class'] < UC_UPLOADER)
    $updateset[] = "class = '2'"; //=== set this to the number for vip on your server
}
if ((isset($_POST['donorlengthadd'])) && ($donorlengthadd = 0 + $_POST['donorlengthadd'])) {
    $donoruntil = $user["donoruntil"];
    $dur = $donorlengthadd . " week" . ($donorlengthadd > 1 ? "s" : "");
    $msg = sqlesc("Dear " . $user['username'] . "
    :wave:
    Thanks for your continued support to {$FMED['site_name']} !
    Your donation helps us in the costs of running the site. Everything above the current running costs will go towards next months costs!
    As a donor, you are given some bonus gigs added to your uploaded amount, and, you have the the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

    so, thanks again, and enjoy!
    cheers,
    {$FMED['site_name']} Staff

    PS. Your donator status will last for an extra $dur on top of your current donation status, and can be found on your user details page and can only be seen by you :smile: It was set by " .$CURUSER['username']);

    $subject = sqlesc("Thank You for Your Donation... Again!");
    $modcomment = get_date( time(), 'DATE', 1 ) . " - Donator status set for another $dur by " . $CURUSER['username'] .".\n" . $modcomment;
    $donorlengthadd = $donorlengthadd * 7;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET vipclass_before=".$user["class"].", donoruntil = IF(donoruntil=0, ".TIME_NOW." + 86400 * $donorlengthadd, donoruntil + 86400 * $donorlengthadd) WHERE id = $userid") or sqlerr(__file__, __line__);
    $added = sqlesc(time());
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__file__, __line__);
    $updateset[] = "donated = $user[donated] + " . sqlesc($_POST['donated']);
    $updateset[] = "total_donated = $user[total_donated] + " . sqlesc($_POST['donated']);
}
if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor'])) {
    $updateset[] = "donor = " . sqlesc($donor);
    $updateset[] = "donoruntil = '0'";
    $updateset[] = "donated = '0'";
    $updateset[] = "class = " . $user["vipclass_before"];
if ($donor == 'no') {
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_donor_removed']} " . $CURUSER['username'] .".\n" . $modcomment;
    $msg = sqlesc(sprintf($lang['modtask_donor_removed']) . $CURUSER['username']);
    $added = sqlesc(time());
    $subject = sqlesc("Donator status expired.");
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__file__, __line__);
    }
}
if ((isset($_POST['enabled'])) && (($enabled = $_POST['enabled']) != $user['enabled'])){
if ($enabled == 'yes')
    $modcomment = get_date( time(), 'DATE', 1 ) . " {$lang['modtask_enabled']}" . $CURUSER['username'] . ".\n" . $modcomment;
    else
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_disabled']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "enabled = " . sqlesc($enabled);
}
    /* If your running the forum post enable/disable, uncomment this section
    // Forum Post Enable / Disable
if ((isset($_POST['forumpost'])) && (($forumpost = $_POST['forumpost']) != $user['forumpost'])){
if ($forumpost == 'yes'){
    $modcomment = gmdate("Y-m-d")." - Posting enabled by ".$CURUSER['username'].".\n" . $modcomment;
    $msg = sqlesc("Your Posting rights have been given back by ".$CURUSER['username'].". You can post to forum again.");
    $added = time();
    mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }else{
    $modcomment = gmdate("Y-m-d")." - Posting disabled by ".$CURUSER['username'].".\n" . $modcomment;
    $msg = sqlesc("Your Posting rights have been removed by ".$CURUSER['username'].", Please PM ".$CURUSER['username']." for the reason why.");
    $added = time();
    mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
    $updateset[] = "forumpost = " . sqlesc($forumpost);
} */
if ((isset($_POST['title'])) && (($title = $_POST['title']) != ($curtitle = $user['title']))){
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_custom_title']}'".$title."' from '".$curtitle."'{$lang['modtask_by']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "title = " . sqlesc($title);
}
if ((isset($_POST['resetpasskey'])) && ($_POST['resetpasskey'])){
    $newpasskey = md5($user['username'].time().$user['passhash']);
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_passkey']}".sqlesc($user['passkey'])."{$lang['modtask_reset']}".sqlesc($newpasskey)."{$lang['modtask_by']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "passkey=".sqlesc($newpasskey);
}
if ((isset($_POST['avatar'])) && (($avatar = $_POST['avatar']) != ($curavatar = $user['avatar']))){
      $avatar = trim( urldecode( $avatar ) );
if ( preg_match( "/^http:\/\/$/i", $avatar )
    or preg_match( "/[?&;]/", $avatar )
    or preg_match("#javascript:#is", $avatar )
    or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $avatar )){
    $avatar='';
}
if( !empty($avatar) ){
    $img_size = @GetImageSize( $avatar );
if($img_size == FALSE || !in_array($img_size['mime'], $FMED['allowed_ext']))
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_not_image']}");
if($img_size[0] < 5 || $img_size[1] < 5)
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_image_small']}");
if ( ( $img_size[0] > $FMED['av_img_width'] ) OR ( $img_size[1] > $FMED['av_img_height'] ) ){
    $image = resize_image( array('max_width'  => $FMED['av_img_width'],
    'max_height' => $FMED['av_img_height'],
    'cur_width'  => $img_size[0],
    'cur_height' => $img_size[1]));
    }else{
    $image['img_width'] = $img_size[0];
    $image['img_height'] = $img_size[1];
}
    $updateset[] = "av_w = " . sqlesc($image['img_width']);
    $updateset[] = "av_h = " . sqlesc($image['img_height']);
}
    $modcomment = get_date( time(), 'DATE', 1 ) . "{$lang['modtask_avatar_change']}".htmlspecialchars($curavatar)."{$lang['modtask_to']}".htmlspecialchars($avatar)."{$lang['modtask_by']}" . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "avatar = ".sqlesc($avatar);
}
    /* Uncomment if you have the First Line Support mod installed...
    // Support
if ((isset($_POST['support'])) && (($support = $_POST['support']) != $user['support'])){
if ($support == 'yes'){
    $modcomment = gmdate("Y-m-d") . " - Promoted to FLS by " . $CURUSER['username'] . ".\n" . $modcomment;
    }elseif ($support == 'no'){
    $modcomment = gmdate("Y-m-d") . " - Demoted from FLS by " . $CURUSER['username'] . ".\n" . $modcomment;
    }else
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
    $supportfor = $_POST['supportfor'];
    $updateset[] = "support = " . sqlesc($support);
    $updateset[] = "supportfor = ".sqlesc($supportfor);
} */
if (isset($_POST['immunity']) && ($immunity = 0 + $_POST['immunity'])){
    unset($immunity_pm);
if (isset($_POST['immunity_pm']))
    $immunity_pm = $_POST['immunity_pm'];
    $subject = sqlesc('Notification!');
    $added = time();
if ($immunity == 255){
    $modcomment = get_date($added, 'DATE', 1)." - Immune Status enabled by ".
    $CURUSER['username'].".\nReason: $immunity_pm\n".$modcomment;
    $msg = sqlesc("You have received immunity Status from ".$CURUSER['username'].($immunity_pm ? "\n\nReason: $immunity_pm" : ''));
    $updateset[] = 'immunity = 1';
    } elseif ($immunity == 42){
    $modcomment = get_date($added, 'DATE', 1)." - Immunity Status removed by ".
    $CURUSER['username'].".\n".$modcomment;
    $msg = sqlesc("Your Immunity Status has been removed by ".
    $CURUSER['username'].".");
    $updateset[] = 'immunity = 0';
    } else{
    $immunity_until = ($added + $immunity * 604800);
    $dur = $immunity.' week'.($immunity > 1 ? 's' : '');
    $msg = sqlesc("You have received $dur Immunity Status from ".
    $CURUSER['username'].($immunity_pm ? "\n\nReason: $immunity_pm" : ''));
    $modcomment = get_date($added, 'DATE', 1)." - Immunity Status for $dur by ".
    $CURUSER['username'].".\nReason: $immunity_pm\n".$modcomment;
    $updateset[] = "immunity = ".$immunity_until;
}
     mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, subject, msg, added) VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__file__, __line__);
}
if ((isset($_POST['seedbonus'])) && (($seedbonus = $_POST['seedbonus']) != $user['seedbonus'])){
    $modcomment = get_date( time(), 'DATE', 1 ) . " - Seeding bonus set to $seedbonus by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "seedbonus = " . sqlesc($seedbonus);
}
if ((isset($_POST['invites'])) && (($invites = $_POST['invites']) != $user['invites'])){
    $modcomment = get_date( time(), 'DATE', 1 ) . " - Invites set to $invites by " . $CURUSER['username'] . ".\n" . $modcomment;
    $updateset[] = "invites = " . sqlesc($invites);
}
if ((isset($_POST['downloadpos'])) && (($downloadpos = $_POST['downloadpos']) != $user['downloadpos'])){
if ($downloadpos == 'yes'){
    $modcomment = gmdate("Y-m-d") . " - Download enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your download rights have been given back by " . $CURUSER['username'] . ". You can download torrents again.");
    $added = time();
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }elseif ($downloadpos == 'no'){
    $modcomment = gmdate("Y-m-d") . " - Download disabled by " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your download rights have been removed by " . $CURUSER['username'] . ", Please PM ".$CURUSER['username']." for the reason why.");
    $added = time();
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    }else
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}"); // Error
    $updateset[] = "downloadpos = " . sqlesc($downloadpos);
}
if (($CURUSER['class'] == UC_SYSOP && ($user['modcomment'] != $_POST['modcomment'] || $modcomment!=$_POST['modcomment'])) || ($CURUSER['class']<UC_SYSOP && $modcomment != $user['modcomment']))
    $updateset[] = "modcomment = " . sqlesc($modcomment);
if (sizeof($updateset)>0)
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET  " . implode(", ", $updateset) . " WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
    $returnto = $_POST["returnto"];
    header("Location: {$FMED['baseurl']}/$returnto");
    stderr("{$lang['modtask_user_error']}", "{$lang['modtask_try_again']}");
}
stderr("{$lang['modtask_user_error']}", "{$lang['modtask_no_idea']}");
?>