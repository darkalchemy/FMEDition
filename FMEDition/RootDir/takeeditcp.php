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
require_once "include/password_functions.php";
require_once "include/html_functions.php";
require_once ROOT_PATH."/cache/timezones.php";
dbconn();
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('takeeditcp') );
    function resize_image($in){
    $out = array( 'img_width'  => $in['cur_width'], 'img_height' => $in['cur_height']);
if ( $in['cur_width'] > $in['max_width'] ){
    $out['img_width']  = $in['max_width'];
    $out['img_height'] = ceil( ( $in['cur_height'] * ( ( $in['max_width'] * 100 ) / $in['cur_width'] ) ) / 100 );
    $in['cur_height'] = $out['img_height'];
    $in['cur_width']  = $out['img_width'];
}
if ( $in['cur_height'] > $in['max_height'] ){
    $out['img_height']  = $in['max_height'];
    $out['img_width']   = ceil( ( $in['cur_width'] * ( ( $in['max_height'] * 100 ) / $in['cur_height'] ) ) / 100 );
}
    return $out;
}
    $action = isset($_POST["action"]) ? htmlspecialchars(trim($_POST["action"])) : '';
    $updateset = array();
    $urladd='';
if ($action == "avatar") {
    $avatars = ($_POST["avatars"] != "" ? "yes" : "no");
    $avatar = trim( urldecode( $_POST["avatar"] ) );
if ( preg_match( "/^http:\/\/$/i", $avatar )
    or preg_match( "/[?&;]/", $avatar )
    or preg_match("#javascript:#is", $avatar )
    or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $avatar ) ){
    $avatar='';
}
if( !empty($avatar) ){
    $img_size = @GetImageSize( $avatar );
if ($img_size == FALSE || !in_array($img_size['mime'], $FMED['allowed_ext']))
    stderr($lang['takeprofedit_user_error'], $lang['takeprofedit_image_error']);
if ($img_size[0] < 5 || $img_size[1] < 5)
    stderr($lang['takeprofedit_user_error'], $lang['takeprofedit_small_image']);
if ( ( $img_size[0] > $FMED['av_img_width'] ) OR ( $img_size[1] > $FMED['av_img_height'] ) ){
    $image = resize_image( array('max_width'  => $FMED['av_img_width'],'max_height' => $FMED['av_img_height'],'cur_width'  => $img_size[0],'cur_height' => $img_size[1]));
    }else {
    $image['img_width'] = $img_size[0];
    $image['img_height'] = $img_size[1];
}
    $updateset[] = "av_w = " . $image['img_width'];
    $updateset[] = "av_h = " . $image['img_height'];
}
    $updateset[] = "avatar = " . sqlesc($avatar);
    $updateset[] = "avatars = '$avatars'";
    $action = "avatar";
    }elseif ($action == "signature") {
    $signatures = (isset($_POST['signatures']) && $_POST["signatures"] != "" ? "yes" : "no");
    $signature = trim( urldecode( $_POST["signature"] ) );
if ( preg_match( "/^http:\/\/$/i", $signature )
    or preg_match( "/[?&;]/", $signature )
    or preg_match("#javascript:#is", $signature )
    or !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $signature )){
    $signature='';
}
if( !empty($signature) ) {
    $img_size = @GetImageSize( $signature );
if ($img_size == FALSE || !in_array($img_size['mime'], $FMED['allowed_ext']))
    stderr('USER ERROR', 'Not an image or unsupported image!');
if ($img_size[0] < 5 || $img_size[1] < 5)
    stderr('USER ERROR', 'Image is too small');
if ( ( $img_size[0] > $FMED['sig_img_width'] ) OR ( $img_size[1] > $FMED['sig_img_height'] ) ){
    $image = resize_image( array('max_width'  => $FMED['sig_img_width'],'max_height' => $FMED['sig_img_height'],'cur_width'  => $img_size[0],'cur_height' => $img_size[1]));
    }else{
    $image['img_width'] = $img_size[0];
    $image['img_height'] = $img_size[1];
}
    $updateset[] = "sig_w = " . $image['img_width'];
    $updateset[] = "sig_h = " . $image['img_height'];
    $updateset[] = "signature = " . sqlesc("[img]".$signature."[/img]\n");
}
    $updateset[] = "signatures = '$signatures'";
if (isset($_POST["info"]) && (($info = $_POST["info"]) != $CURUSER["info"])){
    $updateset[] = "info = " . sqlesc($info);
}
    $action = "signature";
    }elseif ($action == "security") {
if (!mkglobal("email:chpassword:passagain:chmailpass:secretanswer"))
        stderr("Error", $lang['takeeditcp_no_data']);
if ($chpassword != ""){
if (strlen($chpassword) > 40)
    stderr("Error", $lang['takeeditcp_pass_long']);
if ($chpassword != $passagain)
    stderr("Error", $lang['takeeditcp_pass_not_match']);
    $secret = mksecret();
    $passhash = make_passhash( $secret, md5($chpassword) );
    $updateset[] = "secret = " . sqlesc($secret);
    $updateset[] = "passhash = " . sqlesc($passhash);
    logincookie($CURUSER["id"], md5($passhash.$_SERVER["REMOTE_ADDR"]));
}
if ($email != $CURUSER["email"]) {
if (!validemail($email))
    stderr("Error", $lang['takeeditcp_not_valid_email']);
    $r = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM users WHERE email=" . sqlesc($email)) or sqlerr();
if ( mysqli_num_rows($r) > 0 || ($CURUSER["passhash"] != make_passhash( $CURUSER['secret'], md5($chmailpass) ) ) )
    stderr("Error", $lang['takeeditcp_address_taken']);
    $changedemail = 1;
}
    $urladd = "";
    $changedemail = 0;
if ($changedemail) {
    $sec = mksecret();
    $hash = md5($sec . $email . $sec);
    $obemail = urlencode($email);
    $updateset[] = "editsecret = " . sqlesc($sec);
    $body = str_replace(array('<#USERNAME#>', '<#SITENAME#>', '<#USEREMAIL#>', '<#IP_ADDRESS#>', '<#CHANGE_LINK#>'),
    array($CURUSER['username'], $FMED['site_name'], $email, $_SERVER['REMOTE_ADDR'], "{$FMED['baseurl']}/confirmemail.php?uid={$CURUSER['id']}&key=$hash&email=$obemail"),
    $lang['takeeditcp_email_body']);
    mail($email, "$thisdomain {$lang['takeeditcp_confirm']}", $body, "From: {$FMED['site_email']}");
    $urladd .= "&mailsent=1";
}
    $action = "security";
    }elseif ($action == "torrents") {
    $pmnotif = isset($_POST["pmnotif"]) ? $_POST["pmnotif"] : '';
    $emailnotif = isset($_POST["emailnotif"]) ? $_POST["emailnotif"] : '';
    $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
    $notifs .= ($emailnotif == 'yes' ? "[email]" : "");
    $r = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM categories") or sqlerr();
    $rows = mysqli_num_rows($r);
    for ($i = 0; $i < $rows; ++$i){
    $a = mysqli_fetch_assoc($r);
if (isset($_POST["cat{$a['id']}"]) && $_POST["cat{$a['id']}"] == 'yes')
    $notifs .= "[cat{$a['id']}]";
}
    $updateset[] = "notifs = '$notifs'";
    $action = "torrents";
    }elseif ($action == "personal") {
if (isset($_POST["country"]) && (($country = $_POST["country"]) != $CURUSER["country"]) && is_valid_id($country))
    $updateset[] = "country = $country";
if (isset($_POST["torrentsperpage"]) && (($torrentspp = min(100, 0 + $_POST["torrentsperpage"])) != $CURUSER["torrentsperpage"]))
    $updateset[] = "torrentsperpage = $torrentspp";
if (isset($_POST["topicsperpage"]) && (($topicspp = min(100, 0 + $_POST["topicsperpage"])) != $CURUSER["topicsperpage"]))
    $updateset[] = "topicsperpage = $topicspp";
if (isset($_POST["postsperpage"]) && (($postspp = min(100, 0 + $_POST["postsperpage"])) != $CURUSER["postsperpage"]))
    $updateset[] = "postsperpage = $postspp";
if (isset($_POST["user_timezone"]) && preg_match('#^\-?\d{1,2}(?:\.\d{1,2})?$#', $_POST['user_timezone']))
    $updateset[] = "time_offset = " . sqlesc($_POST['user_timezone']);
    $updateset[] = "auto_correct_dst = " .(isset($_POST['checkdst']) ? 1 : 0);
    $updateset[] = "dst_in_use = " .(isset($_POST['manualdst']) ? 1 : 0);
if ($CURUSER['birthday'] == "0000-00-00") {
    $year = isset($_POST["year"]) ? 0 + $_POST["year"] : 0;
    $month = isset($_POST["month"]) ? 0 + $_POST["month"] : 0;
    $day = isset($_POST["day"]) ? 0 + $_POST["day"] : 0;
    $birthday = date("$year.$month.$day");
if ($year == '0000')
    stderr("Error", "Please set your birth year.");
if ($month == '00')
    stderr("Error","Please set your birth month.");
if ($day == '00')
    stderr("Error","Please set your birth day.");
if (!checkdate($month, $day, $year))
    stderr("Error", "<br /><div align='center'><font color='red' size='+1'>The date entered is not a valid date, please try again</font></div><br />");
    $updateset[] = "birthday = ".sqlesc($birthday);

}
if (isset($_POST["gender"]) && ($gender = $_POST["gender"]) != $CURUSER["gender"])
    $updateset[] = "gender = " . sqlesc($gender);
    $shoutboxbg = 0 + $_POST["shoutboxbg"];
    $updateset[] = "shoutboxbg = " . sqlesc($shoutboxbg);
    $action = "personal";
    }elseif ($action == "pm") {
    $acceptpms_choices = array('yes' => 1, 'friends' => 2, 'no' => 3);
    $acceptpms = (isset($_POST['acceptpms']) ? $_POST['acceptpms'] : 'all');
if (isset($acceptpms_choices[$acceptpms]))
    $updateset[] = "acceptpms = " . sqlesc($acceptpms);
    $deletepms = isset($_POST["deletepms"]) ? "yes" : "no";
    $updateset[] = "deletepms = '$deletepms'";
    $savepms = (isset($_POST['savepms']) && $_POST["savepms"] != "" ? "yes" : "no");
    $updateset[] = "savepms = '$savepms'";
    $action = "pm";
}
if (sizeof($updateset)>0)
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
    header("Location: {$FMED['baseurl']}/usercp.php?edited=1&action=$action" . $urladd);
?>