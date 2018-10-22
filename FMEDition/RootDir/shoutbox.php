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
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn( false );
loggedinorreturn();
    $HTMLOUT ="";
if ( ( isset( $_GET['show_shout'] ) ) && ( ( $show_shout = $_GET['show'] ) !== $CURUSER['show_shout'] ) ) {
    mysqli_query($GLOBALS["___mysqli_ston"],  "UPDATE users SET show_shout = " . sqlesc( $_GET['show'] ) . " WHERE id = $CURUSER[id]" );
    header( "Location: " . $_SERVER['HTTP_REFERER'] );
}
    unset( $insert );
    $insert = false;
    $query = "";
    $sb = array('charset' => 'UTF-8');
if ( isset( $_GET['del'] ) && $CURUSER['class'] >= UC_MODERATOR && is_valid_id( $_GET['del'] ) )
    mysqli_query($GLOBALS["___mysqli_ston"],  "DELETE FROM shoutbox WHERE id=" . sqlesc( $_GET['del'] ) );
if (isset($_GET['delall']) && $CURUSER['class'] == UC_SYSOP) {
    mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE TABLE shoutbox") or sqlerr(__FILE__, __LINE__); 
}
if (isset($_GET['edit']) && $CURUSER['class'] >= UC_MODERATOR && is_valid_id($_GET['edit'])){
    $sql = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT id, text FROM shoutbox WHERE id='.sqlesc($_GET['edit']));
    $res = mysqli_fetch_assoc($sql);
    unset($sql);
    $HTMLOUT .="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <meta http-equiv='Pragma' content='no-cache' />
    <meta http-equiv='expires' content='-1' />
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <meta http-equiv='REFRESH' content='60; URL=./shoutbox.php' />
    <meta http-equiv='Content-Type' content='text/html; charset={$sb['charset']}' />
    <script type='text/javascript' src='./scripts/shout.js'></script>
    <style type='text/css'>
    #specialbox{border: 1px solid gray;width: 600px;background: #FBFCFA;font: 11px verdana, sans-serif;color: #000000;padding: 3px;   outline: none;}
    #specialbox:focus{border: 1px solid black;}
    .btn {cursor:pointer;border:outset 1px #ccc;background:#999;color:#666;font-weight:bold;padding: 1px 2px;background: #000000 repeat-x left top;}
    </style>
    </head>
    <body bgcolor='#F5F4EA' class='date'>
    <form method='post' action='./shoutbox.php'>
    <input type='hidden' name='id' value='".(int)$res['id']."' />
    <textarea name='text' rows='3' id='specialbox'>".htmlspecialchars($res['text'])."</textarea>
    <input type='submit' name='save' value='save' class='btn' />
    </form></body></html>";
    print $HTMLOUT;
    die;
}
if (isset($_GET['edit']) && ($_GET['user'] == $CURUSER['id']) && ($CURUSER['class'] >= UC_POWER_USER && $CURUSER['class'] <= UC_MODERATOR) && is_valid_id($_GET['edit'])){
    $sql = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT id, text, userid FROM shoutbox WHERE userid ='.sqlesc($_GET['user']).' AND id='.sqlesc($_GET['edit']));
    $res = mysqli_fetch_array($sql);
    $HTMLOUT .="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <meta http-equiv='Pragma' content='no-cache' />
    <meta http-equiv='expires' content='-1' />
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <meta http-equiv='REFRESH' content='60; URL=./shoutbox.php' />
    <meta http-equiv='Content-Type' content='text/html; charset={$sb['charset']}' />
    <script type='text/javascript' src='./scripts/shout.js'></script>
    <style type='text/css'>
    .specialbox{border: 1px solid gray;width: 600px;background: #FBFCFA;font: 11px verdana, sans-serif;color: #000000;padding: 3px;   outline: none;}
    .specialbox:focus{border: 1px solid black;}
    .btn {cursor:pointer;border:outset 1px #ccc;background:#999;color:#666;font-weight:bold;padding: 1px 2px;background: #000000 repeat-x left top;}
    </style>
    </head>
    <body bgcolor='#F5F4EA' class='date'>
    <form method='post' action='./shoutbox.php'>
    <input type='hidden' name='id' value='".(int)$res['id']."' />
    <input type='hidden' name='user' value='".(int)$res['userid']."' />
    <textarea name='text' rows='3' id='specialbox'>".htmlspecialchars($res['text'])."</textarea>
    <input type='submit' name='save' value='save' class='btn' />
    </form></body></html>";
    print $HTMLOUT;
    die;
}
if (isset($_POST['text']) && $CURUSER['class'] >= UC_MODERATOR && is_valid_id($_POST['id'])){
    $text = trim($_POST['text']);
    $text_parsed = format_comment($text);
    mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE shoutbox SET text = '.sqlesc($text).', text_parsed = '.sqlesc($text_parsed).' WHERE id='.sqlesc($_POST['id']));
    unset($text, $text_parsed);
}
if (isset($_POST['text']) && (isset($_POST['user']) == $CURUSER['id']) && ($CURUSER['class'] >= UC_POWER_USER && $CURUSER['class'] < UC_MODERATOR) && is_valid_id($_POST['id'])){
    $text = trim($_POST['text']);
    $text_parsed = format_comment($text);
    mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE shoutbox SET text = '.sqlesc($text).', text_parsed = '.sqlesc($text_parsed).' WHERE userid='.sqlesc($_POST['user']).' AND id='.sqlesc($_POST['id']));
    unset($text, $text_parsed);
}
    $HTMLOUT .="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <title>ShoutBox</title>
    <meta http-equiv='REFRESH' content='60; URL=./shoutbox.php' />
    <script type='text/javascript' src='./scripts/shout.js'></script>
    <meta http-equiv='Content-Type' content='text/html; charset={$sb['charset']}' />
    <style type='text/css'>
    A {color: #356AA0; font-weight: bold; font-size: 9pt; }
    A:hover {color: #FF0000;}
    .small {color: #ff0000; font-size: 9pt; font-family: arial; }
    .date {color: #ff0000; font-size: 9pt;}
    .error {color: #990000;background-color: #FFF0F0;padding: 7px;margin-top: 5px;margin-bottom: 10px;border: 1px dashed #990000;}
    A {color: #FFFFFF; font-weight: bold; }
    A:hover {color: #FFFFFF;}
    .small {font-size: 10pt; font-family: arial; }
    .date {font-size: 8pt;}
    span.size1 { font-size:0.75em; }
    span.size2 { font-size:1em; }
    span.size3 { font-size:1.25em; }
    span.size4 { font-size:1.5em; }
    span.size5 { font-size:1.75em; }
    span.size6 { font-size:2em; }
    span.size7 { font-size:2.25em; }
    </style>";
if ( $CURUSER['shoutboxbg'] == 1 ) {
    $HTMLOUT .="<style type='text/css'>
    A {color: #000000; font-weight: bold;  }
    A:hover {color: #FF273D;}
    .small {font-size: 10pt; font-family: arial; }
    .date {font-size: 8pt;}
    </style>";
    $bg = '#ffffff';
    $fontcolor = '#000000';
    $dtcolor = '#356AA0';
}
if ( $CURUSER['shoutboxbg'] == 2 ) {
    $HTMLOUT .="<style type='text/css'>
    A {color: #ffffff; font-weight: bold;  }
    A:hover {color: #FF273D;}
    .small {font-size: 10pt; font-family: arial; }
    .date {font-size: 8pt;}
    </style>";
    $bg = '#777777';
    $fontcolor = '#000000';
    $dtcolor = '#FFFFFF';
}
if ( $CURUSER['shoutboxbg'] == 3 ) {
    $HTMLOUT .="<style type='text/css'>
    A {color: #FFFFFF; font-weight: bold; ; }
    A:hover {color: #FFFFFF;}
    .small {font-size: 10pt; font-family: arial; }
    .date {font-size: 8pt;}
    </style>";
    $bg = '#1f1f1f';
    $fontcolor = '#FFFFFF';
    $dtcolor = '#FFFFFF';
}
    $HTMLOUT .="</head><body>";
if ($CURUSER['chatpost'] == 0|| $CURUSER['chatpost'] > 1){
    $HTMLOUT .="<div class='error' align='center'><br /><font color='red'>Sorry, you are not authorized to Shout.</font>  (<a href=\"./rules.php\" target=\"_blank\"><font color='red'>Contact Site Admin For The Reason Why</font></a>)<br /><br /></div></body></html>";
    print $HTMLOUT;
    exit;
}
if ( isset( $_GET['sent'] ) && ( $_GET['sent'] == "yes" ) ) {
    $limit = 5;
    $userid = $CURUSER["id"];
    $date = sqlesc( time() );
    $text = (trim( $_GET["shbox_text"] ));
    $text_parsed = format_comment($text);
    $system_pattern = '/(^\/system)\s([\w\W\s]+)/is';
if(preg_match($system_pattern,$text,$out) && $CURUSER["class"] >= UC_MODERATOR){
    $userid = 2;
    $text = $out[2];
    $text_parsed = format_comment($text);
}
    $commands = array( "\/EMPTY", "\/GAG", "\/UNGAG", "\/WARN", "\/UNWARN", "\/DISABLE", "\/ENABLE" );
    $pattern = "/(" . implode( "|", $commands ) . "\w+)\s([a-zA-Z0-9_:\s(?i)]+)/";
    $private_pattern = "/(^\/private)\s([a-zA-Z0-9]+)\s([\w\W\s]+)/";
if (preg_match( $pattern, $text, $vars ) && $CURUSER["class"] >= UC_MODERATOR) {
    $command = $vars[1];
    $user = $vars[2];
    $c = mysqli_query($GLOBALS["___mysqli_ston"],  "SELECT id, class, modcomment FROM users where username=" . sqlesc( $user ) ) or sqlerr();
    $a = mysqli_fetch_row( $c );
if ( mysqli_num_rows( $c ) == 1 && $CURUSER["class"] > $a[1] ) {
    switch ( $command ) {
    case "/EMPTY" :
    $what = 'deleted all shouts';
    $msg = "[b]" . $user . "'s[/b] shouts have been deleted";
    $query = "DELETE FROM shoutbox where userid = " . $a[0];
    break;
    case "/GAG" :
    $what = 'gagged';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - [ShoutBox] User has been gagged by " . $CURUSER["username"] . "\n" . $a[2];
    $msg = "[b]" . $user . "[/b] - has been gagged by " . $CURUSER["username"];
    $query = "UPDATE users SET chatpost='0', modcomment = concat(" . sqlesc( $modcomment ) . ", modcomment) WHERE id = " . $a[0];
    break;
    case "/UNGAG" :
    $what = 'ungagged';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - [ShoutBox] User has been ungagged by " . $CURUSER["username"] . "\n" . $a[2];
    $msg = "[b]" . $user . "[/b] - has been ungagged by " . $CURUSER["username"];
    $query = "UPDATE users SET chatpost='1', modcomment = concat(" . sqlesc( $modcomment ) . ", modcomment) WHERE id = " . $a[0];
    break;
    case "/WARN" :
    $what = 'warned';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - [ShoutBox] User has been warned by " . $CURUSER["username"] . "\n" . $a[2];
    $msg = "[b]" . $user . "[/b] - has been warned by " . $CURUSER["username"];
    $query = "UPDATE users SET warned='1', modcomment = concat(" . sqlesc( $modcomment ) . ", modcomment) WHERE id = " . $a[0];
    break;
    case "/UNWARN" :
    $what = 'unwarned';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - [ShoutBox] User has been unwarned by " . $CURUSER["username"] . "\n" . $a[2];
    $msg = "[b]" . $user . "[/b] - warning removed by " . $CURUSER["username"];
    $query = "UPDATE users SET warned='0', modcomment = concat(" . sqlesc( $modcomment ) . ", modcomment) WHERE id = " . $a[0];
    break;
    case "/DISABLE" :
    $what = 'disabled';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - [ShoutBox] User has been disabled by " . $CURUSER["username"] . "\n" . $a[2];
    $msg = "[b]" . $user . "[/b] - has been disabled by " . $CURUSER["username"];
    $query = "UPDATE users SET enabled='no', modcomment = concat(" . sqlesc( $modcomment ) . ", modcomment) WHERE id = " . $a[0];
    break;
    case "/ENABLE" :
    $what = 'enabled';
    $modcomment = get_date( time(), 'DATE', 1 ) . " - [ShoutBox] User has been enabled by " . $CURUSER["username"] . "\n" . $a[2];
    $msg = "[b]" . $user . "[/b] - has been enabled by " . $CURUSER["username"];
    $query = "UPDATE users SET enabled='yes', modcomment = concat(" . sqlesc( $modcomment ) . ", modcomment) WHERE id = " . $a[0];
    break;
}
if ( mysqli_query($GLOBALS["___mysqli_ston"],  $query ) )
     autoshout($msg);
     $HTMLOUT .="<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
     write_log("Shoutbox user " . $user . " has been " . $what . " by " . $CURUSER["username"] );
     unset($text, $text_parsed, $query, $date, $modcomment, $what, $msg, $commands);
}
    }elseif(preg_match($private_pattern,$text,$vars)) {
    $to_user = mysqli_result(mysqli_query($GLOBALS["___mysqli_ston"], 'select id from users WHERE username = '.sqlesc($vars[2])), 0) or exit(mysqli_error($GLOBALS["___mysqli_ston"]));
if($to_user != 0 && $to_user != $CURUSER['id']) {
    $text = $vars[2]." - ".$vars[3];
    $text_parsed = format_comment($text);
    mysqli_query($GLOBALS["___mysqli_ston"],  "INSERT INTO shoutbox (userid, date, text, text_parsed,to_user) VALUES (".sqlesc($userid).", $date, " . sqlesc( $text ) . ",".sqlesc( $text_parsed) .",".sqlesc($to_user).")") or sqlerr( __FILE__, __LINE__ );
}
    $HTMLOUT .="<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
    } else {
    $a = mysqli_fetch_row( mysqli_query($GLOBALS["___mysqli_ston"],  "SELECT userid,date FROM shoutbox ORDER by id DESC LIMIT 1 " ) ) or print( "First shout or an error :)" );
if ( empty( $text ) || strlen( $text ) == 1 )
    $HTMLOUT .="<font class=\"small\" color=\"red\">Shout can't be empty</font>";
    elseif ( $a[0] == $userid && ( time() - $a[1] ) < $limit && $CURUSER['class'] < UC_MODERATOR )
    $HTMLOUT .="<font class=\"small\" color=\"red\">$limit seconds between shouts <font class=\"small\">Seconds Remaining : (" . ( $limit - ( time() - $a[1] ) ) . ")</font></font>";
    else {
    mysqli_query($GLOBALS["___mysqli_ston"],  "INSERT INTO shoutbox (id, userid, date, text, text_parsed) VALUES ('id'," . sqlesc( $userid ) . ", $date, " . sqlesc( $text ) . ",".sqlesc( $text_parsed ) .")" ) or sqlerr( __FILE__, __LINE__ );
    $HTMLOUT .="<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
    }
  }
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"],  "SELECT s.id, s.userid, s.date , s.text,s.to_user, u.username,  u.class, u.donor, u.warned,  u.enabled, u.chatpost,  (SELECT count(id) FROM messages WHERE receiver = ".$CURUSER['id']." AND unread = 'yes') as pms FROM shoutbox as s LEFT JOIN users as u ON s.userid=u.id ORDER BY s.date DESC LIMIT 30" ) or sqlerr( __FILE__, __LINE__ );
if ( mysqli_num_rows( $res ) == 0 )
    $HTMLOUT .="No shouts here";
    else {
    $HTMLOUT .="<table border='0' cellspacing='0' cellpadding='2' width='100%' align='left' class='small'>\n";
    $gotpm = 0;
    while ( $arr = mysqli_fetch_assoc( $res ) ) {
if($arr['pms'] > 0 && $gotpm == 0){
    $HTMLOUT .= '<tr><td align=\'center\'><a href=\''.$FMED['baseurl'].'/messages.php\' target=\'_parent\'><font color=\'blue\'>You have '.$arr['pms'].' new message'.($arr['pms'] > 1 ? 's' : '').'</font></a></td></tr>';
    $gotpm++;
}
if(($arr['to_user'] != $CURUSER['id'] && $arr['to_user'] != 0) && $arr['userid'] != $CURUSER['id'])
    continue;
    elseif($arr['to_user'] == $CURUSER['id'] || ($arr['userid'] == $CURUSER['id'] && $arr['to_user'] !=0) )
    $private = "<a href=\"javascript:private_reply('".$arr['username']."')\"><img src=\"{$FMED['pic_base_url']}private-shout.png\" alt=\"Private shout\" title=\"Private shout! click to reply to ".$arr['username']."\" width=\"16\" style=\"padding-left:2px;padding-right:2px;\" border=\"0\" /></a>";
    else
    $private = '';
    $edit = ($CURUSER['class'] >= UC_MODERATOR || ($arr['userid'] == $CURUSER['id']) && ($CURUSER['class'] >= UC_POWER_USER && $CURUSER['class'] <= UC_MODERATOR) ? "<a href='{$FMED['baseurl']}/shoutbox.php?edit=" . $arr['id'] . "&amp;user=".$arr['userid']."'><img src='{$FMED['pic_base_url']}/shoutbox/button_edit2.gif' border='0' width='13' height='11' alt=\"Edit Shout\"  title=\"Edit Shout\" /></a> " : "" );
    $del = ( $CURUSER['class'] >= UC_MODERATOR ? "<a href='./shoutbox.php?del=" . $arr['id'] . "'><img src='{$FMED['pic_base_url']}/shoutbox/button_delete2.gif' border='0' width='13' height='11' alt=\"Delete Single Shout\" title=\"Delete Single Shout\" /></a> " : "" );
    $delall = ( $CURUSER['class'] >= UC_SYSOP ? "<a href='./shoutbox.php?delall' onclick=\"confirm_delete(); return false;\"><img src='{$FMED['pic_base_url']}/shoutbox/del.png' border='0' width='13' height='11' alt=\"Empty Shout\" title=\"Empty Shout\" /></a> " : "" );
    $pm = "<span class='date' style=\"color:$dtcolor\"><a target='_blank' href='./sendmessage.php?receiver=$arr[userid]'><img src='{$FMED['pic_base_url']}/shoutbox/button_pm2.gif' border='0' width='13' height='11' alt=\"Pm User\" title=\"Pm User\" /></a></span>\n";
    $date = get_date($arr["date"], 0,1);
    $reply = "<a href='javascript:window.top.SmileIT(\"[b][i]=>&nbsp;[color=#".get_user_class_color($arr['class']) . "]".htmlspecialchars($arr['username'])."[/color]&nbsp;-[/i][/b]\",\"shbox\",\"shbox_text\")'><img height='10' src='{$FMED['pic_base_url']}/shoutbox/reply.gif' border='0' width='13' height='11' title='Reply' alt='Reply' style='border:none;' /></a>";
    $user_stuff = $arr;
    $user_stuff['id'] = $arr['userid'];
    $HTMLOUT .="<tr style='background-color:$bg;'><td><span class='size1' style='color:$fontcolor; '>[$date]</span>\n$del $delall $edit $pm $reply $private ".format_username($user_stuff)."<span class='size2' style='color:$fontcolor;'> " . format_comment( $arr["text"] ) . "\n</span></td></tr>\n";
}
    $HTMLOUT .="</table>";
}
    $HTMLOUT .="</body></html>";
print $HTMLOUT;