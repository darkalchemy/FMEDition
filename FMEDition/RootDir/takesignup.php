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
require_once "include/password_functions.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";      
dbconn();
global $CURUSER;
if(!$CURUSER){
get_template();
}
    $lang = array_merge( load_language('global'), load_language('takesignup') );
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_row($res);
if ($arr[0] >= $FMED['maxusers'])
    stderr($lang['takesignup_error'], $lang['takesignup_limit']);
    foreach( array('wantusername','wantpassword','passagain','email','captcha') as $x ){
if( !isset($_POST[ $x ]) ){
    stderr($lang['takesignup_user_error'], $lang['takesignup_form_data']);
}
    ${$x} = $_POST[ $x ];
}
    session_start();
if(empty($captcha) || $_SESSION['captcha_id'] != strtoupper($captcha)){
    header('Location: signup.php');
    exit();
}
function validusername($username){
global $lang;
if ($username == "")
    return false;
    $namelength = strlen($username);
if( ($namelength < 3) OR ($namelength > 32) ){
    stderr($lang['takesignup_user_error'], $lang['takesignup_username_length']);
}
    $allowedchars = $lang['takesignup_allowed_chars'];
    for ($i = 0; $i < $namelength; ++$i){
if (strpos($allowedchars, $username[$i]) === false)
    return false;
}
    return true;
}
    $country = (int)$_POST["country"];
    $gender = $_POST["gender"];
if (empty($wantusername) || empty($wantpassword) || ($country == 0)  || empty($email))
    stderr($lang['takesignup_user_error'], $lang['takesignup_blank']);
if ($wantpassword != $passagain)
    stderr($lang['takesignup_user_error'], $lang['takesignup_nomatch']);
if (strlen($wantpassword) < 6)
    stderr($lang['takesignup_user_error'], $lang['takesignup_pass_short']);
if (strlen($wantpassword) > 40)
    stderr($lang['takesignup_user_error'], $lang['takesignup_pass_long']);
if ($wantpassword == $wantusername)
    stderr($lang['takesignup_user_error'], $lang['takesignup_same']);
if (!validemail($email))
    stderr($lang['takesignup_user_error'], $lang['takesignup_validemail']);
if (!validusername($wantusername))
    stderr($lang['takesignup_user_error'], $lang['takesignup_invalidname']);
if (!(isset($_POST['day']) || isset($_POST['month']) || isset($_POST['year'])))
    stderr('Error','You have to fill in your birthday');
if (checkdate($_POST['month'], $_POST['day'], $_POST['year']))
    $birthday = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
    else
    stderr('Error','You have to fill in your birthday correctly');
if ((date('Y') - $_POST['year']) < 17)
    stderr('Error','You must be at least 18 years old to register.');
    $a = (@mysqli_fetch_row(@mysqli_query($GLOBALS["___mysqli_ston"], "select count(*) from users where email='$email'"))) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
if ($a[0] != 0)
    stderr("The e-mail address $email is already in use.");
    $c = (@mysqli_fetch_row(@mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM users WHERE ip='" . $_SERVER['REMOTE_ADDR'] . "'"))) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
if ($c[0] != 0)
    stderr("The ip <b>" . $_SERVER['REMOTE_ADDR'] . "</b> is already in use. We only allow one account per ip address.");
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
    stderr($lang['takesignup_failed'], $lang['takesignup_qualify']);
    $a = (@mysqli_fetch_row(@mysqli_query($GLOBALS["___mysqli_ston"], "select count(*) from users where email='$email'"))) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
if ($a[0] != 0)
    stderr($lang['takesignup_user_error'], $lang['takesignup_email_used']);
if(isset($_POST["user_timezone"]) && preg_match('#^\-?\d{1,2}(?:\.\d{1,2})?$#', $_POST['user_timezone'])){
    $time_offset = sqlesc($_POST['user_timezone']);
    }else{
    $time_offset = isset($FMED['time_offset']) ? sqlesc($FMED['time_offset']) : '0'; }
    $dst_in_use = localtime(time() + ($time_offset * 3600), true);
    $secret = mksecret();
    $wantpasshash = make_passhash( $secret, md5($wantpassword) );
    $editsecret = ( !$arr[0] ? "" : make_passhash_login_key() );
    $ret = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO users (username, passhash, secret, editsecret, birthday, gender, country, email, status, ". (!$arr[0]?"class, ":"") ."added, time_offset, dst_in_use) VALUES (" . implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $birthday, $gender, $country, $email, (!$arr[0]?'confirmed':'pending')))). ", ". (!$arr[0]?UC_SYSOP.", ":""). "". time() ." , $time_offset, {$dst_in_use['tm_isdst']})");
    $message = "Welcome New {$FMED['site_name']} Member : - " . htmlspecialchars($wantusername) . "";
if (!$ret){
if (mysqli_errno($GLOBALS["___mysqli_ston"]) == 1062)
    stderr($lang['takesignup_user_error'], $lang['takesignup_user_exists']);
}
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    $psecret = $editsecret;
    autoshout($message);
    $body = str_replace(array('<#SITENAME#>', '<#USEREMAIL#>', '<#IP_ADDRESS#>', '<#REG_LINK#>'),
    array($FMED['site_name'], $email, $_SERVER['REMOTE_ADDR'], "{$FMED['baseurl']}/confirm.php?id=$id&secret=$psecret"),
    $lang['takesignup_email_body']);
if ($arr[0])
    mail($email, "{$FMED['site_name']} {$lang['takesignup_confirm']}", $body, "{$lang['takesignup_from']} {$FMED['site_email']}");
    else
    logincookie($id, $wantpasshash);
    header("Refresh: 0; url=ok.php?type=". (!$arr[0]?"sysop":("signup&email=" . urlencode($email))));
?>