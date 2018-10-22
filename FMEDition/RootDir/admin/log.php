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
if ( ! defined( 'IN_FMED_ADMIN' ) ){
    print "<h1>{$lang['text_incorrect']}</h1>{$lang['text_cannot']}";
    exit();
}
require_once "include/user_functions.php";
    $lang = array_merge( $lang, load_language('ad_log') );
    $secs = 24 * 60 * 60;
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM sitelog WHERE " . time() . " - added > $secs") or sqlerr(__FILE__, __LINE__);
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT added, txt FROM sitelog ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT = "<h1>{$lang['text_sitelog']}</h1>\n";
if (mysqli_num_rows($res) == 0){
    $HTMLOUT .= "<b>{$lang['text_logempty']}</b>\n";
    }else{
    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <td class='colhead' align='left'>{$lang['header_date']}</td>
        <td class='colhead' align='left'>{$lang['header_time']}</td>
        <td class='colhead' align='left'>{$lang['header_event']}</td>
    </tr>\n";
    while ($arr = mysqli_fetch_assoc($res)){
    $date = explode( ',', get_date( $arr['added'], 'LONG' ) );
    $HTMLOUT .= "<tr><td>{$date[0]}</td>
    <td>{$date[1]}</td>
    <td align='left'>".htmlentities($arr['txt'], ENT_QUOTES)."</td>
    </tr>\n";
}
    $HTMLOUT .= "</table>\n";
}
    $HTMLOUT .= "<p>{$lang['text_times']}</p>\n";
    print stdhead("{$lang['stdhead_log']}") . $HTMLOUT . stdfoot();
?> 