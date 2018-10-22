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
function stdhead( $title = "", $js='', $css='') {
global $CURUSER, $FMED, $lang;
if (!$FMED['site_online'])
    die("Site is down for maintenance, please check back again later... thanks<br />");
if ($title == "")
    $title = $FMED['site_name'] .(isset($_GET['FMED'])?" (".FMEDition.")":'');
    else
    $title = $FMED['site_name'].(isset($_GET['FMED'])?" (".FMEDition.")":''). " :: " . htmlspecialchars($title);
if ($FMED['msg_alert'] && $msgalert && $CURUSER){
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " && unread='yes'") or sqlerr(__FILE__,__LINE__);
    $arr = mysqli_fetch_row($res);
    $unread = $arr[0];
}
    $htmlout="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
 	<html xmlns='http://www.w3.org/1999/xhtml'>
 	<head>
 		<meta name='generator' content='' />
 		<meta http-equiv='Content-Language' content='en-us' />
 		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
 		<meta name='MSSmartTagsPreventParsing' content='TRUE' />
    <title>{$title}</title>
	<link rel='stylesheet' href='templates/1/1.css' type='text/css' />
    <link rel='shortcut icon' href='favicon.ico' />
    <link rel='stylesheet' href='image-resize/resize.css' type='text/css' />
    <script type='text/javascript' src='./image-resize/jquery.js'></script>
    <script type='text/javascript' src='./image-resize/core-resize.js'></script>
    <script type='text/javascript' src='./scripts/jquery.js'></script>
    <script type='text/javascript' src='./scripts/java_klappe.js'></script>
    ".$css."\n
	".$js."\n
	</head>
    <body>
    <div class='half'>
	<table class='maincont'>
	<tr><td class='haus' colspan='2'>
	<div class='play'>
	<div class='play1'></div>
	<div class='play3'></div>
	<div class='play2'>{$FMED['site_name']}</div>
	</div>
	</td></tr>";
	$htmlout.=StatusBar();
    $htmlout .="<tr valign='top'><td class='hlid'>";
if ($CURUSER){
    $htmlout .="<ul class='nav'>
	<li><a href='index.php'>{$lang['gl_home']}</a></li>
	<li><a href='browse.php'>{$lang['gl_browse']}</a></li>
	<li><a href='upload.php'>{$lang['gl_upload']}</a></li>
	".(isset($CURUSER)?"<li><a href='usercp.php'>{$lang['gl_profile']}</a></li>":"<li><a href='login.php'>{$lang['gl_login']}</a></li>
    <li><a href='signup.php'>{$lang['gl_signup']}</a></li>")."
	<li><a href='forums.php'>{$lang['gl_forums']}</a></li>
	<li><a href='staff.php'>{$lang['gl_staff']}</a></li>
	<li><a href='donate.php'>Donate</a></li>
    <li><a href='rules.php'><span class='nav'>{$lang['gl_rules']}</span></a></li>
    <li><a href='faq.php'><span class='nav'>{$lang['gl_faq']}</span></a></li>
    <li><a href='formats.php'><span class='nav'>{$lang['gl_formats']}</span></a></li>
    <li><a href='friends.php'><span class='nav'>{$lang['gl_friends']}</span></a></li>
	<li><a href='topten.php'>{$lang['gl_top_10']}</a></li>
	".(isset($CURUSER) && $CURUSER['class']>=UC_MODERATOR ? "<li><a href='admin.php'>{$lang['gl_admin']}</a></li>":"")."
	</ul>";
}
    $htmlout .="</td><td class='sida' align='center'>";
if ($FMED['msg_alert'] && isset($unread) && !empty($unread)){
    $htmlout .= "<table border='0' cellspacing='0' cellpadding='10' bgcolor='red'>
    <tr><td style='padding: 10px; background: red'>\n
    <b><a href='./messages.php'><font color='white'>".sprintf($lang['gl_msg_alert'], $unread) . ($unread > 1 ? "s" : "") . "!</font></a></b>
    </td></tr></table><br />\n";
}
	return $htmlout;
}
    $htmlfoot='';
    $htmlfoot .="";
function stdfoot($stdfoot = false) {
global $CURUSER, $FMED, $lang;
}
    $htmlfoot .= "</div></body></html>\n";
    return $htmlfoot;
function stdmsg($heading, $text){
    $htmlout = "<table class='main' width='750' border='0' cellpadding='0' cellspacing='0'><tr><td class='embedded'>\n";
if ($heading)
    $htmlout .= "<h2>$heading</h2>\n";
    $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n";
    $htmlout .= "{$text}</td></tr></table></td></tr></table>\n";
    return $htmlout;
}
function StatusBar() {
global $CURUSER, $FMED, $lang;
if (!$CURUSER)
    return "";
	$upped = mksize($CURUSER['uploaded']);
	$downed = mksize($CURUSER['downloaded']);
    $ratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded'] / $CURUSER['downloaded'] : 0;
    $ratio = number_format($ratio, 2);
    $color = get_ratio_color($ratio);
if ($color)
    $ratio = "<font color='$color'>$ratio</font>";
    $res1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT count(id) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND unread='yes'") or sqlerr(__LINE__,__FILE__);
	$arr1 = mysqli_fetch_row($res1);
	$unread = $arr1[0];
	$inbox = ($unread == 1 ? "$unread&nbsp;{$lang['gl_msg_singular']}" : "$unread&nbsp;{$lang['gl_msg_plural']}");
    $res2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT seeder, count(*) AS pCount FROM peers WHERE userid=".$CURUSER['id']." GROUP BY seeder") or sqlerr(__LINE__,__FILE__);
	$seedleech = array('yes' => '0', 'no' => '0');
	while( $row = mysqli_fetch_assoc($res2) ) {
if($row['seeder'] == 'yes')
    $seedleech['yes'] = $row['pCount'];
	else
	$seedleech['no'] = $row['pCount'];
}
    $member_reputation = get_reputation($CURUSER);
    $usrclass="";
	$StatusBar = '';
	$StatusBar = "<tr>
    "."
    <td colspan='2' style='padding: 2px;'>
    "."
    <div id='statusbar'>
    "."
    <div style='float:left;color:black;'>{$lang['gl_msg_welcome']},
	".format_username($CURUSER)."&nbsp;{$usrclass}
	"."
    &nbsp;$member_reputation
    "."
    &nbsp;|&nbsp;Invites:&nbsp;<a href='{$FMED['baseurl']}/invite.php'>{$CURUSER['invites']}</a>&nbsp;|"."
	&nbsp;Bonus:&nbsp;<a href='{$FMED['baseurl']}/mybonus.php'>{$CURUSER['seedbonus']}</a>&nbsp;|&nbsp;<a href='logout.php'>[{$lang['gl_logout']}]</a>";
	$StatusBar .= "
	<br />{$lang['gl_ratio']}:$ratio
    "."
    &nbsp;|&nbsp;{$lang['gl_uploaded']}:$upped
    "."
    &nbsp;|&nbsp;{$lang['gl_downloaded']}:$downed";
	$StatusBar.="&nbsp;|&nbsp;{$lang['gl_act_torrents']}:&nbsp;<img alt='{$lang['gl_seed_torrents']}' title='{$lang['gl_seed_torrents']}' src='{$FMED['pic_base_url']}up.png' />&nbsp;{$seedleech['yes']}".
	"&nbsp;&nbsp;<img alt='{$lang['gl_leech_torrents']}' title='{$lang['gl_leech_torrents']}' src='{$FMED['pic_base_url']}dl.png' />&nbsp;</div>
    "."
    <div><p style='text-align:right;'>".date(DATE_RFC822)."<br />
    "."
    <a href='./messages.php'>$inbox</a></p></div>
    "."
    </div></td></tr>";
	return $StatusBar;
}
?>