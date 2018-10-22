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
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/pager_functions.php";
require_once "include/torrenttable_functions.php";
dbconn(false);
     
$lang = array_merge( load_language('global'));
loggedinorreturn();
    $htmlout = '';
    $htmlout = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <meta name='generator' content='' />
    <meta name='MSSmartTagsPreventParsing' content='TRUE' />
    <title>More Smilies</title>
    <link rel='stylesheet' href='./templates/1/1.css' type='text/css' />
    </head>
    <body>
    <script type='text/javascript'>
    function SmileIT(smile,form,text){
    window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value+' '+smile+' ';
    window.opener.document.forms[form].elements[text].focus();
    window.close();
}
</script>
    <table class='list' width='100%' cellpadding='1' cellspacing='1'>";
    $count='';
    while ((list($code, $url) = each($smilies))) {
if ($count % 3 == 0)
    $htmlout .= " \n<tr>";
    $htmlout .= "\n\t<td class=\"col1\" align=\"center\"><a href=\"javascript: SmileIT('" . str_replace("'", "\'", $code) . "','" . htmlspecialchars($_GET["form"]) . "','" . htmlspecialchars($_GET["text"]) . "')\"><img border='0' src='{$FMED['pic_base_url']}smilies/" . $url . "' alt='' /></a></td>";
    $count++;
if ($count % 3 == 0)
    $htmlout .= "\n</tr>";
}
    $htmlout .= "</tr></table><div align='center'><a href='javascript: window.close()'>[ Close Window ]</a></div></body></html>";
    print $htmlout;