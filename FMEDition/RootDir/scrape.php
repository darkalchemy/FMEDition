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
require_once("include/config.php");
if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($FMED['mysql_host'],  $FMED['mysql_user'],  $FMED['mysql_pass']))){
   exit();
}
    @((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE $FMED['mysql_db']")) or exit();
if ( !isset($_GET['info_hash']) OR (strlen($_GET['info_hash']) != 20) )
    error('Invalid hash');
    $res = @mysqli_query($GLOBALS["___mysqli_ston"],  "SELECT info_hash, seeders, leechers, times_completed FROM torrents WHERE " . hash_where( $_GET['info_hash']) );
if( !mysqli_num_rows($res) )
    error('No torrent with that hash found');
    $benc = 'd5:files';
    while ($row = mysqli_fetch_assoc($res)){
    $benc .= 'd20:'.pack('H*', $row['info_hash'])."d8:completei{$row['seeders']}e10:downloadedi{$row['times_completed']}e10:incompletei{$row['leechers']}eee";
}
    $benc .= 'ed5:flagsd20:min_request_intervali1800eee';
    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    print($benc);
function error($err){
    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    exit("d14:failure reason".strlen($err).":{$err}ed5:flagsd20:min_request_intervali1800eeee");
}
function hash_where($hash) {
    return "info_hash = '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  bin2hex($hash) ) . "'";
}
?>