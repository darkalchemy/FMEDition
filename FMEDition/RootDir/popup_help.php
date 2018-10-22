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
$htmlout = '';
$htmlout = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
    <meta name='generator' content='' />
	<meta name='MSSmartTagsPreventParsing' content='TRUE' />
	<title>Auto Paypal</title>
    <link rel='stylesheet' href='./templates/".$CURUSER['stylesheet']."/".$CURUSER['stylesheet'].".css' type='text/css' />
    </head>
    <body>
    <table align='center' cellpadding='10'>
    <tr>
    <td class='one'>You can easily pay by Credit Card using <b>{$FMED['site_name']}'s</b> secure and reliable PayPal Payment Portal.<br />
    A Paypal account is <i>not</i> required.To pay by Credit Card, look for this link on the main Paypal screen:<br />
    <img src=\"{$FMED['pic_base_url']}paypal/PayPal-ccCheckout.gif\" alt=\"Checkout\" />
    <br /><br />
    Note: if you are a PayPal member, you can either use your account,
    or use a Credit Card that is not associated with a PayPal account.
    In that case, you would also need to use an email address that is not associated with a PayPal account.
    <br />
    Please contact us if you have any questions or concerns.<br /><br />
  </td>
</tr></table><div align='center'>[ <a href=\"javascript:window.close();\">Close This Window</a> ]</div></body></html>";
echo $htmlout;
?>
