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

ini_set('session.use_trans_sid', '0');
session_start();
dbconn();
global $CURUSER;
if(!$CURUSER){
get_template();
}

   $lang = array_merge( load_language('global'), load_language('recover') );
if ($_SERVER["REQUEST_METHOD"] == "POST"){
if(empty($_POST['captcha']) || $_SESSION['captcha_id'] != strtoupper($_POST['captcha'])){
    header('Location: recover.php');
    exit();
}
    $email = trim($_POST["email"]);
if (!validemail($email))
    stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_invalidemail']}");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE email=" . sqlesc($email) . " LIMIT 1") or sqlerr();
    $arr = mysqli_fetch_assoc($res) or stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_notfound']}");
    $sec = mksecret();
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET editsecret=" . sqlesc($sec) . " WHERE id=" . $arr["id"]) or sqlerr();
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_dberror']}");
    $hash = md5($sec . $email . $arr["passhash"] . $sec);
    $body = sprintf($lang['email_request'], $email, $_SERVER["REMOTE_ADDR"], $FMED['baseurl'], $arr["id"], $hash).$FMED['site_name'];
    @mail($arr["email"], "{$FMED['site_name']} {$lang['email_subjreset']}", $body, "From: {$FMED['site_email']}") or stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_nomail']}");
    stderr($lang['stderr_successhead'], $lang['stderr_confmailsent']);
    }elseif($_GET){
    $id = 0 + $_GET["id"];
    $md5 = $_GET["secret"];
if (!$id)
    httperr();
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT username, email, passhash, editsecret FROM users WHERE id = $id");
    $arr = mysqli_fetch_assoc($res) or httperr();
    $email = $arr["email"];
    $sec = $arr['editsecret'];
if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
    httperr();
    $newpassword = make_password();
    $sec = mksecret();
    $newpasshash = make_passhash( $sec, md5($newpassword) );
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=$id AND editsecret=" . sqlesc($arr["editsecret"]));
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    stderr("{$lang['stderr_errorhead']}", "{$lang['stderr_noupdate']}");
    $body = sprintf($lang['email_newpass'], $arr["username"], $newpassword, $FMED['baseurl']).$FMED['site_name'];
    @mail($email, "{$FMED['site_name']} {$lang['email_subject']}", $body, "From: {$FMED['site_email']}") or stderr($lang['stderr_errorhead'], $lang['stderr_nomail']);
    stderr($lang['stderr_successhead'], sprintf($lang['stderr_mailed'], $email));
    }else{
if (isset($_SESSION['captcha_time']))
    (time() - $_SESSION['captcha_time'] < 10) ? exit($lang['captcha_spam']) : NULL;
    $HTMLOUT = '';
    $HTMLOUT .= "<script type='text/javascript' src='captcha/captcha.js'></script>
    <h1>{$lang['recover_unamepass']}</h1>
    <p>{$lang['recover_form']}</p>
      <form method='post' action='recover.php'>
      <table border='1' cellspacing='0' cellpadding='10'>
        <tr>
        <td>&nbsp;</td>
        <td>
          <div id='captchaimage'>
          <a href='recover.php' onclick=\"refreshimg(); return false;\" title='{$lang['captcha_refresh']}'>
          <img class='cimage' src='captcha/GD_Security_image.php?".time()."' alt='{$lang['captcha_imagealt']}' />
          </a>
          </div>
         </td>
      </tr>
      <tr>
          <td class='rowhead'>{$lang['captcha_pin']}</td>
          <td>
            <input type='text' maxlength='6' name='captcha' id='captcha' onblur='check(); return false;'/>
          </td>
      </tr>
      <tr>
          <td class='rowhead'>{$lang['recover_regdemail']}</td>
          <td><input type='text' size='40' name='email' /></td></tr>
      <tr>
          <td colspan='2' align='center'><input type='submit' value='{$lang['recover_btn']}' class='btn' /></td>
      </tr>
      </table>
      </form>";
      print stdhead($lang['head_recover']). $HTMLOUT . stdfoot();
}
?> 