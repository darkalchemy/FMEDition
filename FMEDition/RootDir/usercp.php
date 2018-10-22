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
require_once "include/html_functions.php";
require_once "include/user_functions.php";
require_once ROOT_PATH."/cache/timezones.php";
dbconn(false);
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('usercp') );
    $HTMLOUT = '';
    $countries = "<option value='0'>---- {$lang['usercp_none']} ----</option>\n";
    $ct_r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,name FROM countries ORDER BY name") or sqlerr(__FILE__,__LINE__);
    while ($ct_a = mysqli_fetch_assoc($ct_r)){
    $countries .= "<option value='{$ct_a['id']}'" . ($CURUSER["country"] == $ct_a['id'] ? " selected='selected'" : "") . ">{$ct_a['name']}</option>\n";
}
    $offset = ($CURUSER['time_offset'] != "") ? (string)$CURUSER['time_offset'] : (string)$FMED['time_offset'];
    $time_select = "<select name='user_timezone'>";
    foreach( $TZ as $off => $words ){
if ( preg_match("/^time_(-?[\d\.]+)$/", $off, $match)){
    $time_select .= $match[1] == $offset ? "<option value='{$match[1]}' selected='selected'>$words</option>\n" : "<option value='{$match[1]}'>$words</option>\n";
    }
}
    $time_select .= "</select>";
if ($CURUSER['dst_in_use']){
    $dst_check = 'checked="checked"';
    }else{
    $dst_check = '';
}
if ($CURUSER['auto_correct_dst']){
    $dst_correction = 'checked="checked"';
    }else{
    $dst_correction = '';
}
    $HTMLOUT .= "<script type='text/javascript'>
    /*<![CDATA[*/
    function daylight_show(){
    if ( document.getElementById( 'tz-checkdst' ).checked ){
    document.getElementById( 'tz-checkmanual' ).style.display = 'none';
    }else{
    document.getElementById( 'tz-checkmanual' ).style.display = 'block';
    }
}
    /*]]>*/
    </script>";
    $action = isset($_GET["action"]) ? htmlspecialchars(trim($_GET["action"])) : '';
if (isset($_GET["edited"])) {
    $HTMLOUT .="<div class='roundedCorners' align='center' style='width:80%; background:#bcffbf; border:1px solid #49c24f; color:#333333;padding:5px;font-weight:bold;'>{$lang['usercp_updated']}!</div>";
if (isset($_GET["mailsent"]))
    $HTMLOUT .= "<h2>{$lang['usercp_mail_sent']}!</h2>\n";
    }elseif (isset($_GET["emailch"])){
    $HTMLOUT .= "<h1>{$lang['usercp_emailch']}!</h1>\n";
}
    $HTMLOUT .="<h1>Welcome <a href='userdetails.php?id={$CURUSER['id']}'>".format_username($CURUSER)."</a> !</h1>\n
    <form method='post' action='takeeditcp.php'>
    <table align='center' border='1' cellpadding='6' cellspacing='1' width='98%'><tr><td valign='top'>";
if ($action == "avatar") {
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .="<tr><td align='left' class='colhead' style='height:25px;' colspan='2'><input type='hidden' name='action' value='avatar' />Avatar Options</td></tr>";
    $HTMLOUT .= tr($lang['usercp_avatar'], "<input name='avatar' size='50' value='" . htmlspecialchars($CURUSER["avatar"]) ."' /><br />\n{$lang['usercp_avatar_info']}",1);
    $HTMLOUT .= tr($lang['usercp_view_avatars'], "<input type='checkbox' name='avatars'" . ($CURUSER["avatars"] == "yes" ? " checked='checked'" : "") . " /> {$lang['usercp_low_bw']}",1);
    $HTMLOUT .="<tr><td class='colhead' align='center' colspan='2'><input type='submit' value='Submit changes!' style='height: 25px' /></td></tr>";
    $HTMLOUT .= end_table();
    }elseif ($action == "signature") {
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .="<tr><td align='left' class='colhead' style='height:25px;' colspan='2'><input type='hidden' name='action' value='signature' />Signature Options</td></tr>";
    $HTMLOUT .= tr($lang['usercp_signature'], "<input name='signature' size='50' value='" . htmlspecialchars($CURUSER["signature"]) ."' /><br />\n{$lang['usercp_signature_info']}",1);
    $HTMLOUT .= tr($lang['usercp_view_signatures'], "<input type='checkbox' name='signatures'" . ($CURUSER["signatures"] == "yes" ? " checked='checked'" : "") . " /> {$lang['usercp_low_bw']}",1);
    $HTMLOUT .= tr($lang['usercp_info'], "<textarea name='info' cols='50' rows='4'>" . htmlentities($CURUSER["info"], ENT_QUOTES) . "</textarea><br />{$lang['usercp_tags']}", 1);
    $HTMLOUT .="<tr ><td class='colhead' align='center' colspan='2'><input type='submit' value='Submit changes!' style='height: 25px' /></td></tr>";
    $HTMLOUT .= end_table();
    }elseif ($action == "security") {
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .="<tr><td class='colhead' colspan='2' style='height:25px;'><input type='hidden' name='action' value='security' />Security Options</td></tr>";
    $HTMLOUT .= tr($lang['usercp_email'], "<input type='text' name='email' size='50' value='" . htmlspecialchars($CURUSER["email"]) . "' /><br />{$lang['usercp_email_pass']}<br /><input type='password' name='chmailpass' size='50' />", 1);
    $HTMLOUT .= "<tr><td class='colhead' colspan='2' align='left'>{$lang['usercp_note']}</td></tr>\n";
    $HTMLOUT .= tr($lang['usercp_chpass'], "<input type='password' name='chpassword' size='50' />", 1);
    $HTMLOUT .= tr($lang['usercp_pass_again'], "<input type='password' name='passagain' size='50' />", 1);
    $HTMLOUT .="<tr ><td class='colhead' align='center' colspan='2'><input type='submit' value='Submit changes!' style='height: 25px' /></td></tr>";
    $HTMLOUT .= end_table();
    }elseif ($action == "torrents") {
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .="<tr><td class='colhead' colspan='2'  style='height:25px;' ><input type='hidden' name='action' value='torrents' />Torrent Options</td></tr>";
    $categories = '';
    $r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,name FROM categories ORDER BY name") or sqlerr();
if (mysqli_num_rows($r) > 0){
    $categories .= "<table><tr>\n";
    $i = 0;
    while ($a = mysqli_fetch_assoc($r)){
    $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
    $categories .= "<td class='colhead' class='bottom' style='padding-right: 5px'><input name='cat{$a['id']}' type='checkbox' " . (strpos($CURUSER['notifs'], "[cat{$a['id']}]") !== false ? " checked='checked'" : "") . " value='yes' /> " . htmlspecialchars($a["name"]) . "</td>\n";
    ++$i;
}
    $categories .= "</tr></table>\n";
}
    $HTMLOUT .= tr($lang['usercp_email_notif'], "<input type='checkbox' name='pmnotif'" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked='checked'" : "") . " value='yes' /> {$lang['usercp_notify_pm']}<br />\n" . "<input type='checkbox' name='emailnotif'" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked='checked'" : "") . " value='yes' /> {$lang['usercp_notify_torrent']}\n", 1);
    $HTMLOUT .= tr($lang['usercp_browse'],$categories,1);
    $HTMLOUT .="<tr><td class='colhead' align='center' colspan='2'><input type='submit' value='Submit changes!' style='height: 25px' /></td></tr>";
    $HTMLOUT .= end_table();
    }elseif ($action == "personal") {
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .="<tr><td class='colhead' colspan='2'  style='height:25px;' ><input type='hidden' name='action' value='personal' />Personal Options</td></tr>";
    $HTMLOUT .= tr($lang['usercp_tor_perpage'], "<input type='text' size='10' name='torrentsperpage' value='$CURUSER[torrentsperpage]' /> {$lang['usercp_default']}",1);
    $HTMLOUT .= tr($lang['usercp_top_perpage'], "<input type='text' size='10' name='topicsperpage' value='$CURUSER[topicsperpage]' /> {$lang['usercp_default']}",1);
    $HTMLOUT .= tr($lang['usercp_post_perpage'], "<input type='text' size='10' name='postsperpage' value='$CURUSER[postsperpage]' /> {$lang['usercp_default']}",1);
    $HTMLOUT .= tr($lang['usercp_tz'], $time_select ,1);
    $HTMLOUT .= tr($lang['usercp_checkdst'], "<input type='checkbox' name='checkdst' id='tz-checkdst' onclick='daylight_show()' value='1' $dst_correction /> {$lang['usercp_auto_dst']}<br /> <div id='tz-checkmanual' style='display: none;'><input type='checkbox' name='manualdst' value='1' $dst_check /> {$lang['usercp_is_dst']}</div>",1);
    $HTMLOUT .= tr($lang['usercp_language'], "English",1);
    $HTMLOUT .= tr($lang['usercp_country'], "<select name='country'>\n$countries\n</select>",1);
    $HTMLOUT .= tr($lang['usercp_gender'],
    "<input type='radio' name='gender'" . ($CURUSER["gender"] == "Male" ? " checked='checked'" : "") . " value='Male' />{$lang['usercp_male']}
    <input type='radio' name='gender'" .  ($CURUSER["gender"] == "Female" ? " checked='checked'" : "") . " value='Female' />{$lang['usercp_female']}
    <input type='radio' name='gender'" .  ($CURUSER["gender"] == "N/A" ? " checked='checked'" : "") . " value='N/A' />{$lang['usercp_na']}",1);
    $HTMLOUT .= tr($lang['usercp_shoutback'], "<input type='radio' name='shoutboxbg'" . ($CURUSER["shoutboxbg"] == "1" ? " checked='checked'" : "") . " value='1' />{$lang['usercp_shoutback_white']}
    <input type='radio' name='shoutboxbg'" . ($CURUSER["shoutboxbg"] == "2" ? " checked='checked'" : "") . " value='2' />{$lang['usercp_shoutback_grey']}<input type='radio' name='shoutboxbg'" . ($CURUSER["shoutboxbg"] == "3" ? " checked='checked'" : "") . " value='3' />{$lang['usercp_shoutback_black']}", 1);
    $day = $month = $year = '';
    $birthday = $CURUSER["birthday"];
    $birthday = date("Y-m-d", strtotime($birthday));
    list($year1, $month1, $day1) = explode('-', $birthday);
if ($CURUSER['birthday'] == "0000-00-00") {
    $year .= "<select name=\"year\"><option value=\"0000\">--</option>\n";
    $i = "1950";
    while ($i <= (date('Y', time())-13)) {
    $year .= "<option value=\"".$i."\">" . $i . "</option>\n";
    $i++;
}
    $year .= "</select>\n";
    $birthmonths = array("01" => "January","02" => "Febuary","03" => "March","04" => "April","05" => "May","06" => "June","07" => "July","08" => "August","09" => "September","10" => "October","11" => "November","12" => "December",);
    $month = "<select name=\"month\"><option value=\"00\">--</option>\n";
    foreach ($birthmonths as $month_no => $show_month) {
    $month .= "<option value=\"$month_no\">$show_month</option>\n";
}
    $month .= "</select>\n";
    $day .= "<select name=\"day\"><option value=\"00\">--</option>\n";
    $i = 1;
    while ($i <= 31) {
if ($i < 10) {
    $day .= "<option value=\"0".$i."\">0" . $i . "</option>\n";
    } else {
    $day .= "<option value=\"".$i."\">" . $i . "</option>\n";
}
    $i++;
}
    $day .= "</select>\n";
    $HTMLOUT .= tr($lang['my_dfb'], "$year . $month . $day",1);
}
    $HTMLOUT .="<tr><td class='colhead' align='center' colspan='2'><input type='submit' value='Submit changes!' style='height: 25px' /></td></tr>";
    $HTMLOUT .= end_table();
    } else {
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .= "<tr><td class='colhead' colspan='2'  style='height:25px;' ><input type='hidden' name='action' value='pm' />Pm options</td></tr>";
    $HTMLOUT .= tr($lang['usercp_accept_pm'],
    "<input type='radio' name='acceptpms'" . ($CURUSER["acceptpms"] == "yes" ? " checked='checked'" : "") . " value='yes' />{$lang['usercp_except_blocks']}
    <input type='radio' name='acceptpms'" .  ($CURUSER["acceptpms"] == "friends" ? " checked='checked'" : "") . " value='friends' />{$lang['usercp_only_friends']}
    <input type='radio' name='acceptpms'" .  ($CURUSER["acceptpms"] == "no" ? " checked='checked'" : "") . " value='no' />{$lang['usercp_only_staff']}",1);
    $HTMLOUT .= tr($lang['usercp_delete_pms'], "<input type='checkbox' name='deletepms'" . ($CURUSER["deletepms"] == "yes" ? " checked='checked'" : "") . " /> {$lang['usercp_default_delete']}",1);
    $HTMLOUT .= tr($lang['usercp_save_pms'], "<input type='checkbox' name='savepms'" . ($CURUSER["savepms"] == "yes" ? " checked='checked'" : "") . " /> {$lang['usercp_default_save']}",1);
    $HTMLOUT .= "<tr><td class='colhead' align='center' colspan='2'><input type='submit' value='Submit changes!' style='height: 25px' /></td></tr>";
    $HTMLOUT .= end_table();
}
    $HTMLOUT .="</td><td width='95' valign='top' ><table border='1'>";
    $HTMLOUT .="<tr><td width='200'  style='height:25px;' >".htmlentities($CURUSER["username"], ENT_QUOTES) . "'s Avatar</td></tr>";
if(!empty($CURUSER['avatar']) && $CURUSER['av_w'] > 5 && $CURUSER['av_h'] > 5)
    $HTMLOUT .="<tr><td><img src='{$CURUSER['avatar']}' width='{$CURUSER['av_w']}' height='{$CURUSER['av_h']}' alt='' />
    <a href='friends.php'>{$lang['usercp_edit_friends']}</a><br />
    <a href='users.php'>{$lang['usercp_search']}</a></td></tr>";
    else
    $HTMLOUT .="<tr><td><img src='{$FMED['pic_base_url']}forumicons/default_avatar.gif' alt='' /><br>
    <a href='friends.php'>{$lang['usercp_edit_friends']}</a><br />
    <a href='users.php'>{$lang['usercp_search']}</a>
    </td></tr>";
    $HTMLOUT .="<tr><td width='200' style='height:18px;'><a href='userdetails.php?id={$CURUSER['id']}'>".format_username($CURUSER)."</a> Menu</td></tr>";
    $HTMLOUT .="<tr><td align='left'><a href='usercp.php?action=avatar'>Avatar</a></td></tr>
    <tr><td align='left'><a href='usercp.php?action=signature'>Signature</a></td></tr>
    <tr><td align='left'><a href='usercp.php'>Pm's</a></td></tr>
    <tr><td align='left'><a  href='usercp.php?action=security'>Security</a></td></tr>
    <tr><td align='left'><a href='usercp.php?action=torrents'>Torrents</a></td></tr>
    <tr><td align='left'><a href='usercp.php?action=personal'>Personal</a></td></tr>
    <tr><td align='left'><a href='invite.php'>Invites</a></td></tr>";
    $HTMLOUT .="</table></td></tr></table></form>";
    print stdhead(htmlentities($CURUSER["username"], ENT_QUOTES) . "{$lang['usercp_stdhead']}", false) . $HTMLOUT . stdfoot();
?>