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
require_once ROOT_PATH."/cache/timezones.php";
dbconn();
global $CURUSER;
if(!$CURUSER){
get_template();
}
if( isset($CURUSER) ){
    header("Location: {$FMED['baseurl']}/index.php");
    exit();
}
    ini_set('session.use_trans_sid', '0');
    $lang = array_merge( load_language('global'), load_language('signup') );
    session_start();
    $signup = mysqli_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT value FROM openclose WHERE name = 'signup'"),  0);
if ($signup == 0)
    stderr('Sorry', 'Invite only - Signups are closed presently');
    $HTMLOUT = '';
    $HTMLOUT = $year = $month = $day = $gender = '';
    $countries = "<option value=\"75\">{$lang['signup_selected']}</option>n";
    $ct_r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, name FROM countries ORDER BY name") or die;
    while ($ct_a = mysqli_fetch_array($ct_r))
    $countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>n";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_row($res);
if ($arr[0] >= $FMED['maxusers'])
    stderr($lang['stderr_errorhead'], sprintf($lang['stderr_ulimit'], $FMED['maxusers']));
    $offset = (string)$FMED['time_offset'];
    $time_select = "<select name='user_timezone'>";
    foreach( $TZ as $off => $words ){
if ( preg_match("/^time_(-?[\d\.]+)$/", $off, $match)){
    $time_select .= $match[1] == $offset ? "<option value='{$match[1]}' selected='selected'>$words</option>\n" : "<option value='{$match[1]}'>$words</option>\n";
    }
}
    $time_select .= "</select>";
        $time_select .= "</select>";
    $thistime = time();
    $year = $month = $day ='';
    $year .= "<select name=\"year\">";
    $year .= "<option value=\"0000\">Year</option>";
    $i = "2030";
    while($i >= 1950){
    $year .= "<option value=\"".$i."\">".$i."</option>";
    $i--;
}
    $year .= "</select>";
    $month .= "<select name=\"month\">
    <option value=\"00\">Month</option>
    <option value=\"01\">January</option>
    <option value=\"02\">Febuary</option>
    <option value=\"03\">March</option>
    <option value=\"04\">April</option>
    <option value=\"05\">May</option>
    <option value=\"06\">June</option>
    <option value=\"07\">July</option>
    <option value=\"08\">August</option>
    <option value=\"09\">September</option>
    <option value=\"10\">October</option>
    <option value=\"11\">November</option>
    <option value=\"12\">December</option>
    </select>";
    $day .= "<select name=\"day\">";
    $day .= "<option value=\"00\">Day</option>";
    $i = 1;
    while($i <= 31){
if ($i < 10){
    $day .= "<option value=\"0".$i."\">0".$i."</option>";
    }else{
    $day .= "<option value=\"".$i."\">".$i."</option>";
}
    $i++;
}
    $day .= "</select>";
    $gender.= "<select name=\"gender\">
    <option value=\"Male\">{$lang['signup_male']}</option>
    <option value=\"Female\">{$lang['signup_female']}</option>
    </select>";
    $HTMLOUT .= "<script type='text/javascript' src='captcha/captcha.js'></script>
    <p>{$lang['signup_cookies']}</p>
    <form method='post' action='takesignup.php'>
    <table border='1' cellspacing='0' cellpadding='10'>
    <tr>
    <td align='right' class='heading'>{$lang['signup_uname']}</td>
    <td align='left'>
    <input type='text' size='40' name='wantusername' /></td></tr>
    <tr>
    <td align='right' class='heading'>{$lang['signup_pass']}</td>
    <td align='left'>
    <input type='password' size='40' name='wantpassword' /></td>
    </tr>
    <tr>
    <td align='right' class='heading'>{$lang['signup_passa']}</td>
    <td align='left'><input type='password' size='40' name='passagain' /></td>
    </tr>
    <td align='right' class='rowhead'>{$lang['signup_country']}</td>
    <td class='col1' align='left'>
    <select name='country'>\n$countries\n</select></td></tr>
    <tr>
    <td align='right' class='rowhead'>{$lang['signup_birthday']}
    <span style='color:red'>*</span></td>
    <td class='col1' align='left'>" . $year . $month . $day . "</td>
    </tr>
    <tr>
    <td align='right' class='rowhead'>{$lang['signup_gender']}</td>
    <td class='col1'align='left'>$gender</td>
    </tr>
    <tr valign='top'>
    <td align='right' class='heading'>{$lang['signup_email']}</td>
    <td align='left'><input type='text' size='40' name='email' />
    <table width='250' border='0' cellspacing='0' cellpadding='0'>
    <tr>
    <td class='embedded'><font class='small'>{$lang['signup_valemail']}</font></td>
    </tr>
    </table>
    </td></tr>
    <tr><td align='right' class='heading'>{$lang['signup_timez']}</td><td align='left'>{$time_select}</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td>
          <div id='captchaimage'>
          <a href='signup.php' onclick=\"refreshimg(); return false;\" title='{$lang['captcha_refresh']}'>
          <img class='cimage' src='captcha/GD_Security_image.php?$thistime' alt='{$lang['captcha_image_alt']}' />
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
    <tr><td align='right' class='heading'></td><td align='left'><input type='checkbox' name='rulesverify' value='yes' /> {$lang['signup_rules']}<br />
    <input type='checkbox' name='faqverify' value='yes' /> {$lang['signup_faq']}<br />
    <input type='checkbox' name='ageverify' value='yes' /> {$lang['signup_age']}</td></tr>
    <tr><td colspan='2' align='center'><input type='submit' value='{$lang['signup_btn']}' style='height: 25px' /></td></tr>
    </table>
    </form>";
    print stdhead($lang['head_signup']) . $HTMLOUT . stdfoot();
?> 