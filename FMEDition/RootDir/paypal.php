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
dbconn(true);
loggedinorreturn();
    $req = 'cmd=_notify-validate';
    foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}
    $header .= "GET /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_type = $_POST['payment_type'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $id = (int)$_POST['custom'];
if(!is_valid_id($id))
    stderr("Error", "No user with that ID.");
if (!$fp)
    stderr("Error", "Please contact Sysop.");
if ($payment_type == "echeck" && $payment_status == "Pending"){
    header("Location: {$FMED['baseurl']}/paypal_success.php?echeck=1");
    die;
}
if ($payment_type == 'instant' && $payment_status == 'Completed' && $payment_amount > '0'){
if ($receiver_email != "{$FMED['site_donate_mail']}")
    stderr("Error", "Please contact Sysop.");
    settype($payment_amount, "float");
if ($payment_amount > 1)
    settype($payment_amount, "string");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE id = $id") or sqlerr();
    $user = mysqli_fetch_array($res) or stderr("Error", "No user with that ID!");
    $modcomment = $user['modcomment'];
if (isset($_POST['mc_gross'])){
    $donated = 0 + $_POST['mc_gross'];
    $added = sqlesc(get_date_time());
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO funds (cash, user, added) VALUES ($donated, $user[id], $added)") or sqlerr(__FILE__, __LINE__);
    $updateset[] = "donated = $donated";
    $updateset[] = "total_donated = $user[total_donated] + $donated";
}
    $curuploaded = $user['uploaded'];
    $uploaded = $donated * 6442450944;
    $upadded = mksize($uploaded);
    $total = $uploaded + $curuploaded;
    $updateset[] = "uploaded = " . sqlesc($total);
    $curinvites = $user['invites'];
    $invites_added = $donated / 5 * 2;
    $updateset[] = "invites = $curinvites + $invites_added";
    $donorlength = $donated / 2.5;
if ($CURUSER['donor'] == 'no'){
    $donoruntil = get_date_time(gmtime() + $donorlength * 4838400);
    $donoruntil_val = get_date_time(gmtime() + $donorlength * 2419200);
    $dur = $donorlength . " week" . ($donorlength > 1 ? "s" : "");
    $subject = sqlesc("Thank You for Your Donation!");
$msg = sqlesc("Dear " . $user['username'] ."
:wave:
Thanks for your support to ".$SITENAME."!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards server upgrades.
As a donor, you are given ".$upadded." bonus added to your uploaded amount, ".$invites_added." new invites added, the status of VIP, as well as the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
".$SITENAME." staff.

PS. Your donator status will last for ".$dur."  and can be found on your user details page. It can only be seen by you.");
    $modcomment = gmdate("Y-m-d") . " - Donator status set for $dur -- $upadded GB bonus -- $invites_added new invites. added by system.\n".$modcomment;
    $updateset[] = "donoruntil = ".sqlesc($donoruntil_val);
    $updateset[] = "donor = 'yes'";
    }elseif ($CURUSER['donor'] == 'yes'){
    $donorlengthadd = $donated / 2.5;
    $dur = $donorlengthadd . " week" . ($donorlengthadd > 1 ? "s" : "");
    $subject = sqlesc("Thank You for Your Donation... Again!");
$msg = sqlesc("Dear " . $user['username'] ."
:wave:
Thanks for your continued support to ".$SITENAME."!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards next months costs!
As a donor, you are given ".$upadded." bonus added to your uploaded amount, ".$invites_added." new invites added, the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
".$SITENAME." Staff

PS. Your donator status will last for an extra ".$dur." on top of your current donation status, and can be found on your user details page. It can only be seen by you.");
    $modcomment = gmdate("Y-m-d") . " - Donator status set for another $dur -- $upadded GB bonus added -- $invites_added new invites. added by system.\n".$modcomment;
    $donorlengthadd = $donorlengthadd * 7;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET donoruntil = IF(donoruntil='0000-00-00 00:00:00', ADDDATE(NOW(), INTERVAL $donorlengthadd DAY ), ADDDATE( donoruntil, INTERVAL $donorlengthadd DAY)) WHERE id = $id") or sqlerr(__FILE__, __LINE__);
}
    $added = sqlesc(get_date_time());
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $id, $msg, $added)") or sqlerr(__FILE__, __LINE__);
if ($CURUSER['class'] < UC_UPLOADER)
    $updateset[] = "class = '".UC_VIP."'";
    $updateset[] = "modcomment = " . sqlesc($modcomment);
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
    fclose ($fp);
    header("Location: {$FMED['baseurl']}/paypal_success.php");
    } else
    $HTMLOUT.= stdmsg("Thanks for your support", "Please pm the sysops with the transaction details.");
echo $HTMLOUT;
die();
?>