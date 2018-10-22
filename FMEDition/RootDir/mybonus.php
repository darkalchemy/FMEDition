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
dbconn();
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('mybonus') );
$HTMLOUT ='';
function I_smell_a_rat($var){
if ((0 + $var) == 1)
    $var = 0 + $var;
    else
    stderr("Error", "I smell a rat!");
}
    $bonus = htmlspecialchars($CURUSER['seedbonus'], 1);
    switch (true){
    case (isset($_GET['up_success'])):
    I_smell_a_rat($_GET['up_success']);
    $amt = (int)$_GET['amt'];
    switch ($amt) {
    case $amt == 75.0:
    $amt = '1 GB';
    break;
    case $amt == 150.0:
    $amt = '2.5 GB';
    break;
    default:
    $amt = '5 GB';
}
    $HTMLOUT .= "<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr><tr>
    <td align='left'><img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td>
    <td align='left'><b>Congratulations ! </b>".$CURUSER['username']." you have just increased your upload amount by ".$amt."!
    <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br /><br /><br /><br /> click to go back to your
    <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['class_success'])):
    I_smell_a_rat($_GET['class_success']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr>
    <tr><td align='left'><img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left' '>
    <b>Congratulations! </b>".$CURUSER['username']." you have got yourself VIP Status for one month! <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br />
    <br /> Click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Points</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['smile_success'])):
    I_smell_a_rat($_GET['smile_success']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr>
    <tr><td align='left'><img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left' >
    <b>Congratulations! </b>".$CURUSER['username']." you have got yourself a set of custom smilies for one month! <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br />
    <br /> Click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Points</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['warning_success'])):
    I_smell_a_rat($_GET['warning_success']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr>
    <tr><td align='left'><img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left' >
    <b>Congratulations! </b>".$CURUSER['username']." you have removed your warning for the low price of 1000 points!! <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br />
    <br /> Click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Points</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['invite_success'])):
    I_smell_a_rat($_GET['invite_success']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr><tr><td align='left'>
    <img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left'>
    <b>Congratulations! </b>".$CURUSER['username']." you have got your self 3 new invites! <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br /><br />
    click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['title_success'])):
    I_smell_a_rat($_GET['title_success']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr><tr>
    <td align='left'><img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left'>
    <b>Congratulations! </b>".$CURUSER['username']." you are now known as <b>".$CURUSER['title']."</b>! <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br />
    <br /> click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['ratio_success'])):
    I_smell_a_rat($_GET['ratio_success']);
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Success!</h1></td></tr><tr>
    <td align='left'><img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left' ><b>Congratulations! </b> ".$CURUSER['username']." you
    have gained a 1 to 1 ratio on the selected torrent, and the difference in MB has been added to your total upload! <img src='{$FMED['pic_base_url']}smilies/w00t.gif' alt='w00t' title='w00t' /><br />
    <br /> click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.<br /><br />
    </td></tr></table>";
    echo stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['gift_fail'])):
    I_smell_a_rat($_GET['gift_fail']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td align='left' colspan='2'><h1>Huh?</h1></td></tr><tr><td align='left'>
    <img src='{$FMED['pic_base_url']}smilies/cry.gif' alt='bad_karma' title='Bad karma' /></td><td align='left' class='clearalt6'><b>Not so fast there Mr. fancy pants!</b><br />
    <b>".$CURUSER['username']."...</b> you can not spread the karma to yourself...<br />If you want to spread the love, pick another user! <br />
    <br /> click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['gift_fail_user'])):
    I_smell_a_rat($_GET['gift_fail_user']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='donation' align='left' colspan='2'><h1>Error</h1></td></tr><tr><td align='left'>
    <img src='{$FMED['pic_base_url']}smilies/cry.gif' alt='bad_karma' title='Bad karma' /></td><td align='left'><b>Sorry ".$CURUSER['username']."...</b>
    <br /> No User with that username <br /><br /> click to go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.
    <br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['gift_fail_points'])):
    I_smell_a_rat($_GET['gift_fail_points']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='donation' align='left' colspan='2'><h1>Oops!</h1></td></tr><tr><td align='left'>
    <img src='{$FMED['pic_base_url']}smilies/cry.gif' alt='oups' title='Bad karma' /></td><td align='left' class='clearalt6'><b>Sorry </b>".$CURUSER['username']." you dont have enough Karma points
    <br /> go back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
    case (isset($_GET['gift_success'])):
    I_smell_a_rat($_GET['gift_success']);
    stdhead($CURUSER['username'] . "'s Karma Bonus Page");
    $HTMLOUT  .="<table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'><tr><td class='donation' align='left' colspan='2'><h1>Success!</h1></td></tr><tr><td align='left'>
    <img src='{$FMED['pic_base_url']}smilies/karma.gif' alt='good_karma' title='Good karma' /></td><td align='left' class='clearalt6'><b>Congratulations! ".$CURUSER['username']." </b>
    you have spread the Karma well.<br /><br />Member <b>".htmlspecialchars($_GET['usernamegift'])."</b> will be pleased with your kindness!<br /><br />This is the message that was sent:<br />
    <b>Subject:</b> Someone Loves you!<br /> <p>You have been given a gift of <b>".(0 + $_GET['gift_amount_points'])."</b> Karma points by ".$CURUSER['username']."</p><br />
    You may also <a class='altlink' href='{$FMED['baseurl']}/sendmessage.php?receiver=".(0 + $_GET['gift_id'])."'>send ".htmlspecialchars($_GET['usernamegift'])." a message as well</a>, or go back to your <a class='altlink' href='mybonus.php'>Karma Bonus Point</a> page.<br /><br /></td></tr></table>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
    die;
}
if (isset($_GET['exchange'])){
    I_smell_a_rat($_GET['exchange']);
    $userid = 0 + $CURUSER['id'];
if (!is_valid_id($userid))
    stderr("Error", "That is not your user ID!");
    $option = 0 + $_POST['option'];
    $res_points = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM bonus WHERE id =" . sqlesc($option));
    $arr_points = mysqli_fetch_assoc($res_points);
    $art = $arr_points['art'];
    $points = $arr_points['points'];
if ($points == 0)
    stderr("Error", "I smell a rat!");
    $seedbonus = htmlspecialchars($bonus-$points,1);
    $upload = $CURUSER['uploaded'];
    $bonuscomment = $CURUSER['bonuscomment'];
    $bpoints = $CURUSER['seedbonus'];
if ($bonus < $points)
    stderr("Sorry", "you do not have enough Karma points!");
    switch ($art){
    case 'traffic':
    $up = $upload + $arr_points['menge'];
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for upload bonus.\n " .$bonuscomment;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET uploaded = $upload + $arr_points[menge], seedbonus = '$seedbonus', bonuscomment = '$bonuscomment' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?up_success=1&amt=$points");
    die;
    break;
    case 'ratio':
    $torrent_number = 0 + $_POST['torrent_id'];
    $res_snatched = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT s.uploaded, s.downloaded, t.name FROM snatched AS s LEFT JOIN torrents AS t ON t.id = s.torrentid WHERE s.userid = ".sqlesc($userid)." AND torrentid = ".sqlesc($torrent_number)." LIMIT 1") or sqlerr(__FILE__, __LINE__);
    $arr_snatched = mysqli_fetch_assoc($res_snatched);
if ($arr_snatched['name'] == '')
    stderr("Error", "No torrent with that ID!<br />Back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.");
if ($arr_snatched['uploaded'] >= $arr_snatched['downloaded'])
    stderr("Error", "Your ratio on that torrent is fine, you must have selected the wrong torrent ID.<br />Back to your <a class='altlink' href='{$FMED['baseurl']}/mybonus.php'>Karma Bonus Point</a> page.");
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE snatched SET uploaded = ".sqlesc($arr_snatched['downloaded'])." WHERE userid = ".sqlesc($userid)." AND torrentid = ".sqlesc($torrent_number)) or sqlerr(__FILE__, __LINE__);
    $difference = $arr_snatched['downloaded'] - $arr_snatched['uploaded'];
    $bonuscomment = get_date( time(), 'DATE', 1 ) . " - " .$points. " Points for 1 to 1 ratio on torrent: ".$arr_snatched['name']." ".$torrent_number.", ".$difference." added .\n " .$bonuscomment;
    $upload_to_add = $upload + $difference;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET uploaded = ".sqlesc($upload_to_add).", bonuscomment = ".sqlesc($bonuscomment).", seedbonus = ".sqlesc($seedbonus)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?ratio_success=1");
    die;
    break;
    case 'class':
if ($CURUSER['class'] > UC_VIP)
    stderr("Error", "Now why would you want to lower yourself to VIP?<br />go back to your <a class=altlink href=mybonus.php>Karma Bonus Point</a> page and think that one over.");
    $vip_until = get_date_time(gmtime() + 28*86400);
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for 1 month VIP Status.\n " .$bonuscomment;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET class = ".UC_VIP.", vip_added = 'yes', vip_until = '$vip_until', seedbonus = '$seedbonus' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?class_success=1");
    die;
    break;
    case 'warning':
if ($CURUSER['warned'] == 'no')
    stderr("Error", "How can we remove a warning that isn't there?<br />go back to your <a class=altlink href=mybonus.php>Karma Bonus Point</a> page and think that one over.");
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for removing warning.\n " .$bonuscomment;
    $res_warning = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT modcomment FROM users WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    $modcomment = htmlspecialchars($arr['modcomment']);
    $modcomment = gmdate("Y-m-d") . " - warning removed by -Bribe with Karma.\n". $modcomment;
    $modcom = sqlesc($modcomment);
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET warned = 'no', warneduntil = '0000-00-00 00:00:00', seedbonus = '$seedbonus', bonuscomment = '$bonuscomment', modcomment = $modcom WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    $dt = sqlesc(get_date_time());
    $subject = sqlesc("Warning removed by Karma.");
    $msg = sqlesc("Your warning has been removed by the big Karma payoff... Please keep on your best behaviour from now on.\n");
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $userid, $dt, $msg, $subject)") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?warning_success=1");
    die;
    break;
    case 'smile':
    $smile_until = get_date_time(gmtime() + 28*86400);
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for 1 month of custom smilies.\n " .$bonuscomment;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET smile_until = '$smile_until', seedbonus = '$seedbonus' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?smile_success=1");
    die;
    break;
    case 'invite':
    $invites = $CURUSER['invites'];
    $inv = $invites+$arr_points['menge'];
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for invites.\n " .$bonuscomment;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET invites = '$inv', seedbonus = '$seedbonus' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?invite_success=1");
    die;
    break;
    case 'title':
    $title = sqlesc(htmlentities($_POST['title']));
    $words = array('fuck', 'shit', 'Moderator', 'Administrator', 'Admin', 'pussy', 'Sysop', 'cunt', 'nigger', 'VIP', 'Super User', 'Power User', 'ADMIN', 'SYSOP', 'MODERATOR', 'ADMINISTRATOR');
    $title = str_replace($words, "I just wasted my karma", $title);
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for custom title. old title was $CURUSER[title] new title is $title\n " .$bonuscomment;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET title = $title, seedbonus = '$seedbonus' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?title_success=1");
    die;
    break;
    case 'gift_1':
    $points = 0 + $_POST['bonusgift'];
    $usernamegift = htmlentities(trim($_POST['username']));
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,seedbonus,bonuscomment,username FROM users WHERE username=" . sqlesc($usernamegift));
    $arr = mysqli_fetch_assoc($res);
    $useridgift = $arr['id'];
    $userseedbonus = $arr['seedbonus'];
    $bonuscomment_gift = $arr['bonuscomment'];
    $usernamegift = $arr['username'];
    $check_me = array(100,200,300,400,500,5000);
if (!in_array($points, $check_me))
    stderr("Error", "I smell a rat!");
if($bonus >= $points){
    $points= htmlspecialchars($points,1);
    $bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points as gift to $usernamegift .\n " .$bonuscomment;
    $bonuscomment_gift = gmdate("Y-m-d") . " - recieved " .$points. " Points as gift from $CURUSER[username] .\n " .$bonuscomment_gift;
    $seedbonus = $bonus-$points;
    $giftbonus1 = $userseedbonus+$points;
if ($userid==$useridgift){
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?gift_fail=1");
    die;
}
if (!$useridgift){
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?gift_fail_user=1");
    die;
}
    mysqli_query($GLOBALS["___mysqli_ston"], "SELECT bonuscomment,id FROM users WHERE id = '$useridgift'") or sqlerr(__FILE__, __LINE__);
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET seedbonus = '$seedbonus', bonuscomment = '$bonuscomment' WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET seedbonus = '$giftbonus1', bonuscomment = '$bonuscomment_gift' WHERE id = '$useridgift'");
    $subject = sqlesc("Someone Loves you");
    $added = sqlesc(get_date_time());
    $msg = sqlesc("You have been given a gift of $points Karma points by ".$CURUSER['username']);
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, subject, receiver, msg, added) VALUES(0, $subject, $useridgift, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?gift_success=1&gift_amount_points=$points&usernamegift=$usernamegift&gift_id=$useridgift");
    die;
    }else{
    header("Refresh: 0; url={$FMED['baseurl']}/mybonus.php?gift_fail_points=1");
    die;
}
    break;
    }
}
    $HTMLOUT .='<table align="center" border="1" cellpadding="6" cellspacing="1" width="98%">
    <tr>
    <td class="donation" colspan="4">
    '.'
    <h1>
    [' . $FMED['site_name'] . '] Karma Bonus Point system:
    </h1>
    </td>
    </tr>
    <tr>
    <td align="center" colspan="4"  class="donation">
    '.'
    Exchange your <a class=altlink href=mybonus.php>Karma Bonus Points</a> [ current '.$bonus.' ] for goodies!
    '.'
    <br />
    <br />
    [ If no buttons appear, you have not earned enough bonus points to trade. ]
    <br />
    <br />
    <tr>
    '.'
    <td class="donation" align="left">Description</td>
    '.'
    <td class="donation" align="center">Points</td>
    <td class="donation" align="center">Trade</td></tr>';
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM bonus ORDER BY id ASC");
    while ($gets = mysqli_fetch_assoc($res)){
    $count1='';
    $count1= (++$count1)%2;
    $class = 'clearalt'.($count1==0?'6':'7');
    $otheroption = "<table width='100%'>
    <tr>
    <td class=$class><b>Username:</b>
    <input type=text name=username size=20 maxlength=24></td>
    <td class=$class> <b>to be given: </b>
    <select name=bonusgift>
    <option value=100.0> 100.0</option>
    <option value=200.0> 200.0</option>
    <option value=300.0> 300.0</option>
    <option value=400.0> 400.0</option>
    <option value=500.0> 500.0</option>
    <option value=5000.0> 5000.0</option>
    </select> Karma points!</td></tr></table>";
    $otheroption_title = "<input type=text name=title size=30 maxlength=30>";
    $HTMLOUT .='<form action=mybonus.php?exchange=1 method=post>';
    switch (true){
    case ($gets['id'] == 5):
    $HTMLOUT .='<tr>
    <td class="col" align="left" class='.$class.'><h1><font color="#2682ff">'.$gets['bonusname'].'</font></h1>'.$gets['description'].'<br /><br />Enter the <b>Special Title</b> you would like to have '.$otheroption_title.' click Exchange! </td><td class="donation" align="center" class='.$class.'>'.$gets['points'].'</td>';
    break;
    case ($gets['id'] == 7):
    $HTMLOUT .='<tr>
    <td class="donation" align="left" class='.$class.'><h1><font color="#2682ff">'.$gets['bonusname'].'</font></h1>'.$gets['description'].'<br /><br />Enter the <b>username</b> of the person you would like to send karma to, and select how many points you want to send and click Exchange!<br />'.$otheroption.'</td><td class="donation" align="center" class='.$class.'>min.<br />'.$gets['points'].'<br />max.<br />5000</td>';
    break;
    case ($gets['id'] == 9):
    $HTMLOUT .='<tr>
    <td class="donation" align="left" class='.$class.'><h1><font color="#2682ff">'.$gets['bonusname'].'</font></h1>'.$gets['description'].'</td><td class="donation" align="center" class='.$class.'>min.<br />'.$gets['points'].'</td>';
    break;
    case ($gets['id'] == 10):
    $HTMLOUT .='<tr><td class="donation" align="left" class='.$class.'><h1><font color="#2682ff">'.$gets['bonusname'].'</font></h1>'.$gets['description'].'<br /><br />Enter the <b>ID number of the Torrent:</b> <input type=text name=torrent_id size=4 maxlength=8> you would like to buy a 1 to 1 ratio on.</td><td class="donation" align="center" class='.$class.'>min.<br />'.$gets['points'].'</td>';
    break;
    default:
    $HTMLOUT .='<tr>
    <td class="donation" align="left" class='.$class.'><h1><font color="#2682ff">'.$gets['bonusname'].'</font></h1>'.$gets['description'].'</td><td class="donation" align="center" class='.$class.'>'.$gets['points'].'</td>';
}
    $HTMLOUT .='<input type="hidden" name="option" value='.$gets['id'].'> <input type="hidden" name="art" value='.$gets['art'].'>';
    if($bonus >= $gets['points']) {
    switch (true){
    case ($gets['id'] == 7):
    $HTMLOUT .='<td class="donation" class='.$class.'><input class="button" type="submit" name="submit" value="Karma Gift!"></form></td>';
    break;
    default:
    $HTMLOUT .='<td class="donation" class='.$class.'><input class="button" type="submit" name="submit" value="Exchange!"></form></td>';
}
    }else
    $HTMLOUT .='<td class="donation" class='.$class.' align="center"><b>more points needed</b></form></td>';
}
    $HTMLOUT .="</tr></table>
    <div style='background:transparent;height:25px;'>
    <span style='font-weight:bold;font-size:12pt;'>What the hell are these Karma Bonus points,
    and how do I get them?</span></div>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='98%'>
    <tr>
    <td class='clearalt6'>For every hour that you seed a torrent, you are awarded with 1 Karma Bonus Point... <br />
    If you save up enough of them, you can trade them in for goodies like bonus GB(s) to increase your upload stats,<br />
    also to get more invites, or doing the real Karma booster... give them to another user !<br />
    This is awarded on a per torrent basis (max of 1000) even if there are no leechers on the Torrent you are seeding! <br />
    <h1>Other things that will get you karma points : </h1>
    &#186;&nbsp;Uploading a new torrent = 15 points
    <br />&#186;&nbsp;Filling a request = 10 points
    <br />&#186;&nbsp;Comment on torrent = 3 points
    <br />&#186;&nbsp;Saying thanks = 2 points
    <br />&#186;&nbsp;Rating a torrent = 2 points
    <br />&#186;&nbsp;Making a post = 1 point
    <br />&#186;&nbsp;Starting a topic = 2 points
    <br />
    <h1>Some things that will cost you karma points:</h1>
    <br />
    &#186;&nbsp;Upload credit
    <br />&#186;&nbsp;Custom title
    <br />&#186;&nbsp;One month VIP status
    <br />&#186;&nbsp;A 1:1 ratio on a torrent
    <br />&#186;&nbsp;Buying off your warning
    <br />&#186;&nbsp;One month custom smilies for the forums and comments
    <br />&#186;&nbsp;Getting extra invites
    <br />&#186;&nbsp;Giving a gift of karma points to another user
    <br />&#186;&nbsp;Download reduction
    <br />&#186;&nbsp;But keep in mind that everything that can get you karma can also be lost...<br /><br />
    Ie : if you up a torrent then delete it, you will gain and then lose 15 points, making a post and having it deleted will do the same... and there are other hidden bonus karma points all
    over the site which is another way to help out your ratio !
    <br /><br />&#186;&nbsp;*Please note, staff can give or take away points for breaking the rules, or doing good for the community.
    <br />
    <div align='center'><br />
    <a class='altlink' href='{$FMED['baseurl']}/index.php'><b>Back to homepage</b></a></div>
    </td></tr></table></div>";
    print stdhead($CURUSER['username'] . "'s Karma Bonus Points Page") . $HTMLOUT . stdfoot();
?>