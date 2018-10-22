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
dbconn(false);
loggedinorreturn();

    $lang = array_merge(load_language('global'));
    $HTMLOUT = $amount = "";
    $HTMLOUT.= begin_main_frame();
    $nick = ($CURUSER ? $CURUSER["username"] : ("Guest".rand(1000, 9999)));
    $amount.= "<select name=\"amount\"><option value=\"0\">Please select donation amount</option>";
    $i = "5";
    while ($i <= 200) {
    $amount.= "<option value=\"".$i."\">Donation of &#163;".$i.".00 GBP</option>";
    $i = ($i < 100 ? $i = $i + 5 : $i = $i + 10);
}
    $amount.= "</select>";
    $HTMLOUT.= "<script type='text/javascript'>
    function popup(URL) {
    day = new Date();
    id = day.getTime();
    eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=380,left = 340,top = 280');\");
}
    </script>";
    $HTMLOUT.= "<table width='100%' border='0' align='center'>
	<tr><td align='center' valign='middle' class='donation'><h1>{$FMED['site_name']}</h1></td></tr>
	<tr><td class='donation' align='center' valign='middle' class='embedded'>
	<br /><br />
	<p align='center'><b>Select Donation amount, and click the PayPal button to donate !</b><br />
	</p>
    <!-- form goes here -->
    <form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
    <input type='hidden' name='cmd' value='_xclick' />
    <input type='hidden' name='business' value='{$FMED['site_donate_mail']}' />
    <input type='hidden' name='item_name' value='( {$nick} donation )' />
    <input type='hidden' name='item_number' value='1' />
    <input type='hidden' name='no_note' value='1' />
    <p align='center'>
    <b>Donate:</b>{$amount}<br /><br />
    <input type='hidden' name='currency_code' value='GBP' /> <!--Use the right currency//Might fail if the user is using diff to u-->
    <input type='hidden' name='tax' value='0' />
    <input type='hidden' name='no_shipping' value='1' />
    <input type='hidden' name='custom' value='".$CURUSER['id']."' />
    <input type='hidden' name='notify_url' value='{$FMED['baseurl']}/paypal.php' /> 
    <input type='hidden' name='return' value='{$FMED['baseurl']}/paypal.php' />
    <input type='image' align='middle' src='{$FMED['pic_base_url']}/paypal/donate1.gif' name='submit' alt='Make payments with PayPal - its fast, free and secure!' />
    </p>
    <!-- form ends here -->
    </form>
    <br /><p><b><u>The donation process is fully automated</u></b>:<br />
    However, once you have completed your donation at the PayPal site, you <b>MUST</b> click the <b>return to merchant button,</b></p>
    <p align='center'><u><b>Please note</b></u> - all donations go towards running the site. Remember, we run this site out of love for the community on a volenteer basis. The actual costs include:
    <br />
    <br />
    Domain Name registration. [yearly]
    <br />
    Server . [ram - cpu - HD etc]
    <br />
    Site Seedbox
    <br />
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <tr><td align='center' valign='middle' class='donation'><h1>{$FMED['site_name']}</h1></td></tr>
    </tr><tr><td width='100%' align='center' class='tablea' style='text-align: center'>
    <table align='center' border='0' cellpadding='6' cellspacing='1' width='100%'>
    <tr>
    <td class='donation' align='top' width='33%'>
    <br /><table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <b><font color='#000'>One Week Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 10GB</b></div>
    <div align='left'><b>One Weeks Vip Status</b></div>
    <div align='left'><b>3 invites</b></div>
    <div align='left'><b>25 karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' <img src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;20</div></td></tr></table><br>
    </form></td>
    <td  class='donation' align='top' width='33%'>
    <table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'><b>
    <b><font color='#000'>Two Week Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 25GB</b></div>
    <div align='left'><b>Two Weeks Vip Status</b></div>
    <div align='left'><b>5 invites</b></div>
    <div align='left'><b>50 karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;25</div></td></tr></table><br>
    </form></td>
    <td class='donation' align='top' width='33%'>
    <br /><table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <b><font color='#000'>Four Week Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 35GB</b></div>
    <div align='left'><b>Four Weeks Vip Status</b></div>
    <div align='left'><b>15 invites</b></div>
    <div align='left'><b>100, karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;30</div></td></tr></table><br>
    </form></td></tr>
    <tr>
    <td class='donation' align='top' width='33%'>
    <br />
    <table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <b><font color='#000'>Six Week Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 45GB</b></div>
    <div align='left'><b>Six Weeks Vip Status</b></div>
    <div align='left'><b>20 invites</b></div>
    <div align='left'><b>200, karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;35</div></td></tr></table><br>
    </form></td>
    <td class='donation' align='top' width='33%'>
    <br />
    <table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <b><font color='#000'>Three Month Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 65GB</b></div>
    <div align='left'><b>Three Months Vip Status</b></div>
    <div align='left'><b>25 invites</b></div>
    <div align='left'><b>300, karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;40</div></td></tr></table><br>
    </form></td>
    <td class='donation' align='top' width='33%'>
    <br />
    <table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <b><font color='#000'>Six Month Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 150GB</b></div>
    <div align='left'><b>Six Months Vip Status</b></div>
    <div align='left'><b>50 invites</b></div>
    <div align='left'><b>400, karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' <img src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;45</div></td></tr></table><br>
    </form></td></tr>
    <tr>
    <td class='donation' align='center' width='33%'>
    <img style='vertical-align: left;' width='215' height='125' src='{$FMED['pic_base_url']}paypal/paypal_1.gif' border='0'/>
    </td>
    <td class='donation' align='top' width='33%'>
    <br />
    <table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' colspan='10' width='100%' style='text-align: center'>
    <b><font color='#000'>1 Year Subscription</b></font></td>
    </tr><tr><td bgcolor='#111111' width='100%' align='center' style='text-align: center'>
    <div align='left'><b>Upload Credit: 250GB</b></div>
    <div align='left'><b>One Year Vip Status</b></div>
    <div align='left'><b>50 invites</b></div>
    <div align='left'><b>500, karma Bonus points</b></div>
    <div align='left'><b>User Status: <font color='#09741F'>VIP</font></b></div>
    <div align='left'><b>Donor Star: <img align='top' src='{$FMED['pic_base_url']}star.gif'></div>
    <div style='float: left;'>Reap the Benefits of v-i-p&nbsp;<font color='#09741F'>&euro;</font>&nbsp;50</div></td></tr></table><br>
    </form></td>
    <td class='donation' align='center' width='33%'>
    <img style='vertical-align: left;' width='215' height='125' src='{$FMED['pic_base_url']}paypal/paypal_1.gif' border='0'/>
    </td></tr></table></td></tr></table>
    <br /><br />
    <table align='center' cellpadding='4' cellspacing='1' border='0' style='width:90%'>
    <tr>
    <td class='donation' align='center' width='33%'>
    <b> Thank you for your support!</b>
    </p>
    <p  align='center'>Processed through {$FMED['site_name']}'s Secure & Reliable Paypal Payment Portal<br />
    <img src='{$FMED['pic_base_url']}paypal/visa.gif' alt='visa' /> <img src='{$FMED['pic_base_url']}paypal/mastercard.gif' alt='mastercard' /> <img src='{$FMED['pic_base_url']}paypal/amex.gif' alt='amex' /> <img src='{$FMED['pic_base_url']}paypal/discover.gif' alt='discover' /> <img src='{$FMED['pic_base_url']}paypal/echeck.gif' alt='echeck' /> or  <img src='{$FMED['pic_base_url']}paypal/paypal.gif' alt='paypal' /><br />
    A PayPal account is not required for Credit Card payments.  [ <a href=\"javascript:popup('popup_help.php')\">more info</a> ]<br /><br /></p>
    </td></tr></table><br>";
$HTMLOUT.= end_main_frame();
echo stdhead('Donate').$HTMLOUT.stdfoot();
die();
?>