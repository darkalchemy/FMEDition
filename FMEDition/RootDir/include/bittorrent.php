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
require_once("cleanup.php");
require_once("commenttable.php");
require_once("result.class.php");
function validip($ip){
if (!empty($ip) && $ip == long2ip(ip2long($ip))){
    $reserved_ips = array (
    array('0.0.0.0','2.255.255.255'),
    array('10.0.0.0','10.255.255.255'),
    array('127.0.0.0','127.255.255.255'),
    array('169.254.0.0','169.254.255.255'),
    array('172.16.0.0','172.31.255.255'),
    array('192.0.2.0','192.0.2.255'),
    array('192.168.0.0','192.168.255.255'),
    array('255.255.255.0','255.255.255.255'));
    foreach ($reserved_ips as $r){
    $min = ip2long($r[0]);
    $max = ip2long($r[1]);
if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
}
    return true;
    }else
    return false;
}
function getip() {
if (isset($_SERVER)) {
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
    } else {
if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
    $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
    $ip = getenv('HTTP_CLIENT_IP');
    } else {
    $ip = getenv('REMOTE_ADDR');
    }
}
    return $ip;
}
function dbconn($autoclean = false){
global $FMED;
if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($FMED['mysql_host'],  $FMED['mysql_user'],  $FMED['mysql_pass']))){
    switch (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))){
    case 1040:
    case 2002:
if ($_SERVER['REQUEST_METHOD'] == "GET")
    die("<html><head><meta http-equiv='refresh' content=\"5 {$_SERVER['REQUEST_URI']}\"></head><body><table border='0' width='100%' height='100%'><tr><td><h3 align='center'>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
    else
    die("Too many users. Please press the Refresh button in your browser to retry.");
    default:
    die("[" . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . "] dbconn: mysql_connect: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    }
}
    ((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE {$FMED['mysql_db']}")) or die('dbconn: mysql_select_db: ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    userlogin();
if ($autoclean)
    register_shutdown_function("autoclean");
}
function userlogin() {
global $FMED;
    unset($GLOBALS["CURUSER"]);
    $ip = getip();
    $nip = ip2long($ip);
require_once "cache/bans_cache.php";
if(count($bans) > 0){
    foreach($bans as $k) {
if($nip >= $k['first'] && $nip <= $k['last']) {
    header("HTTP/1.0 403 Forbidden");
    print "<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n";
    exit();
    }
}
    unset($bans);
}
if ( !$FMED['site_online'] || !get_mycookie('uid') || !get_mycookie('pass') )
    return;
    $id = 0 + get_mycookie('uid');
if (!$id || strlen( get_mycookie('pass') ) != 32)
    return;
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE id = $id AND enabled='yes' AND status = 'confirmed'");// or die(mysql_error());
    $row = mysqli_fetch_assoc($res);
if (!$row)
    return;
if (get_mycookie('pass') !== $row["passhash"])
    return;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET last_access='" . TIME_NOW . "', ip=".sqlesc($ip)." WHERE id=" . $row["id"]);// or die(mysql_error());
    $row['ip'] = $ip;
    $GLOBALS["CURUSER"] = $row;
get_template();
}
function autoclean() {
global $FMED;
    $now = time();
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
    $row = mysqli_fetch_array($res);
if (!$row) {
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
    return;
}
    $ts = $row[0];
if ($ts + $FMED['autoclean_interval'] > $now)
    return;
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    return;
    docleanup();
}
function unesc($x) {
if (get_magic_quotes_gpc())
    return stripslashes($x);
    return $x;
}
function mksize($bytes){
if ($bytes < 1000 * 1024)
    return number_format($bytes / 1024, 2) . " kB";
    elseif ($bytes < 1000 * 1048576)
    return number_format($bytes / 1048576, 2) . " MB";
    elseif ($bytes < 1000 * 1073741824)
    return number_format($bytes / 1073741824, 2) . " GB";
    else
    return number_format($bytes / 1099511627776, 2) . " TB";
}
function mkprettytime($s) {
if ($s < 0)
    $s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
    $y = explode(":", $x);
if ($y[0] > 1) {
    $v = $s % $y[0];
    $s = floor($s / $y[0]);
    }else
    $v = $s;
    $t[$y[1]] = $v;
}
if ($t["day"])
    return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
if ($t["hour"])
    return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    return sprintf("%d:%02d", $t["min"], $t["sec"]);
}
function mkglobal($vars) {
if (!is_array($vars))
    $vars = explode(":", $vars);
    foreach ($vars as $v) {
if (isset($_GET[$v]))
    $GLOBALS[$v] = unesc($_GET[$v]);
    elseif (isset($_POST[$v]))
    $GLOBALS[$v] = unesc($_POST[$v]);
    else
    return 0;
}
    return 1;
}
function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}
function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}
function sqlesc($x) {
    return "'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $x)."'";
}
function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $x));
}
function get_template(){
global $CURUSER, $FMED;
if(isset($CURUSER)){
if(file_exists("templates/".$CURUSER['stylesheet']."/template.php")){
   require_once("templates/".$CURUSER['stylesheet']."/template.php");
   }else{
if(isset($FMED)){
if(file_exists("templates/".$FMED['stylesheet']."/template.php")){
   require_once("templates/".$FMED['stylesheet']."/template.php");
   }else{
   print("Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.");
}
   }else{
if(file_exists("templates/1/template.php")){
   require_once("templates/1/template.php");
   }else{
print("Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.");
    }
  }
}
    }else{
if(file_exists("templates/".$FMED['stylesheet']."/template.php")){
    require_once("templates/".$FMED['stylesheet']."/template.php");
    }else{
    print("Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.");
    }
}
if(!function_exists("stdhead")){
    print("stdhead function missing");
    function stdhead($title="", $message=true){
    return "<html><head><title>$title</title></head><body>";
    }
}
if(!function_exists("stdfoot")){
    print("stdfoot function missing");
    function stdfoot(){
    return "</body></html>";
    }
}
if(!function_exists("stdmsg")){
    print("stdmgs function missing");
function stdmsg($TITLE, $MSG){
    return "<b>".$TITLE."</b><br />$MSG";
    }
}
if(!function_exists("StatusBar")){
    print("StatusBar function missing");
function StatusBar(){
global $CURUSER, $lang;
    return "{$lang['gl_msg_welcome']}, $CURUSER[username]";
    }
  }
}
function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    print("<h1>Not Found</h1>\n");
    print("<p>Sorry pal :(</p>\n");
    exit();
}
function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff){
    set_mycookie( "uid", $id, $expires );
    set_mycookie( "pass", $passhash, $expires );
if ($updatedb)
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET last_login = ".TIME_NOW." WHERE id = $id");
}
function set_mycookie( $name, $value="", $expires_in=0, $sticky=1 ){
global $FMED;
if ( $sticky == 1 ){
    $expires = time() + 60*60*24*365;
    }else if ( $expires_in ){
    $expires = time() + ( $expires_in * 86400 );
    }else{
    $expires = FALSE;
}
    $FMED['cookie_domain'] = $FMED['cookie_domain'] == "" ? ""  : $FMED['cookie_domain'];
    $FMED['cookie_path']   = $FMED['cookie_path']   == "" ? "/" : $FMED['cookie_path'];
if ( PHP_VERSION < 5.6 ){
if ( $FMED['cookie_domain'] ){
    @setcookie( $FMED['cookie_prefix'].$name, $value, $expires, $FMED['cookie_path'], $FMED['cookie_domain'] . '; HttpOnly' );
    }else{
    @setcookie( $FMED['cookie_prefix'].$name, $value, $expires, $FMED['cookie_path'] );
}
    }else{
    @setcookie( $FMED['cookie_prefix'].$name, $value, $expires, $FMED['cookie_path'], $FMED['cookie_domain'], NULL, TRUE );
    }
}
function get_mycookie($name) {
global $FMED;
if ( isset($_COOKIE[$FMED['cookie_prefix'].$name]) AND !empty($_COOKIE[$FMED['cookie_prefix'].$name]) ){
    return urldecode($_COOKIE[$FMED['cookie_prefix'].$name]);
    }else{
    return FALSE;
    }
}
function logoutcookie() {
    set_mycookie('uid', '-1');
    set_mycookie('pass', '-1');
}
function loggedinorreturn() {
global $CURUSER, $FMED;
if (!$CURUSER) {
    header("Location: {$FMED['baseurl']}/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
    exit();
    }
}
function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}
function genrelist() {
    $ret = array();
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, name, image FROM categories ORDER BY name");
    while ($row = mysqli_fetch_array($res))
    $ret[] = $row;
    return $ret;
}
function get_row_count($table, $suffix = ""){
if ($suffix)
    $suffix = " $suffix";
    ($r = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM $table$suffix")) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    ($a = mysqli_fetch_row($r)) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    return $a[0];
}
function stderr($heading, $text){
    $htmlout = stdhead();
    $htmlout .= stdmsg($heading, $text);
    $htmlout .= stdfoot();
    print $htmlout;
    exit();
}
function sqlerr($file = '', $line = '') {
global $XBLADE, $CURUSER;
    $the_error    = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    $the_error_no = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false));
if ( SQL_DEBUG == 0 ){
    exit();
    }else if ( $XBLADE['sql_error_log'] AND SQL_DEBUG == 1 ){
    $_error_string  = "\n===================================================";
    $_error_string .= "\n Date: ". date( 'r' );
    $_error_string .= "\n Error Number: " . $the_error_no;
    $_error_string .= "\n Error: " . $the_error;
    $_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
    $_error_string .= "\n in file ".$file." on line ".$line;
    $_error_string .= "\n URL:".$_SERVER['REQUEST_URI'];
    $_error_string .= "\n Username: {$CURUSER['username']}[{$CURUSER['id']}]";
if ( $FH = @fopen( $XBLADE['sql_error_log'], 'a' ) ){
    @fwrite( $FH, $_error_string );
    @fclose( $FH );
}
    print "<html><head><title>mysql Error</title>
    <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
    <blockquote><h1>mysql Error</h1><b>There appears to be an error with the database.</b><br />
    You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>
    </body></html>";
    }else{
    $the_error = "\nSQL error: ".$the_error."\n";
    $the_error .= "SQL error code: ".$the_error_no."\n";
    $the_error .= "Date: ".date("l dS \of F Y h:i:s A");
    $out = "<html>\n<head>\n<title>mysql Error</title>\n
    <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style>\n</head>\n<body>\n
    <blockquote>\n<h1>mysql Error</h1><b>There appears to be an error with the database.</b><br />
    You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>.
    <br /><br /><b>Error Returned</b><br />
    <form name='mysql'><textarea rows=\"15\" cols=\"60\">".htmlentities($the_error, ENT_QUOTES)."</textarea></form><br>We apologise for any inconvenience</blockquote></body></html>";
    print $out;
}
    exit();
}
function get_dt_num(){
    return gmdate("YmdHis");
}
function write_log($text){
  $text = sqlesc($text);
  $added = TIME_NOW;
  mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}
function sql_timestamp_to_unix_timestamp($s){
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}
function unixstamp_to_human( $unix=0 ){
    $offset = get_time_offset();
    $tmp    = gmdate( 'j,n,Y,G,i', $unix + $offset );
    list( $day, $month, $year, $hour, $min ) = explode( ',', $tmp );
    return array( 'day'    => $day,
                    'month'  => $month,
                    'year'   => $year,
                    'hour'   => $hour,
                    'minute' => $min );
}
function get_time_offset() {
global $CURUSER, $FMED;
    $r = 0;
    $r = ( ($CURUSER['time_offset'] != "") ? $CURUSER['time_offset'] : $FMED['time_offset'] ) * 3600;
if ( $FMED['time_adjust'] ){
    $r += ($FMED['time_adjust'] * 60);
}
if ( $CURUSER['dst_in_use'] ){
    $r += 3600;
}
    return $r;
}
function get_date($date, $method, $norelative=0, $full_relative=0){
global $FMED;
    static $offset_set = 0;
    static $today_time = 0;
    static $yesterday_time = 0;
    $time_options = array(
    'JOINED' => $FMED['time_joined'],
    'SHORT'  => $FMED['time_short'],
    'LONG'   => $FMED['time_long'],
    'TINY'   => $FMED['time_tiny'] ? $FMED['time_tiny'] : 'j M Y - G:i',
    'DATE'   => $FMED['time_date'] ? $FMED['time_date'] : 'j M Y');
if ( ! $date ){
    return '--';
}
if ( empty($method) ){
    $method = 'LONG';
}
if ($offset_set == 0){
    $GLOBALS['offset'] = get_time_offset();
if ( $FMED['time_use_relative'] ){
    $today_time     = gmdate('d,m,Y', ( time() + $GLOBALS['offset']) );
    $yesterday_time = gmdate('d,m,Y', ( (time() - 86400) + $GLOBALS['offset']) );
}
    $offset_set = 1;
}
if ( $FMED['time_use_relative'] == 3 ){
    $full_relative = 1;
}
if ( $full_relative and ( $norelative != 1 ) ){
    $diff = time() - $date;
if ( $diff < 3600 ){
if ( $diff < 120 ){
    return '< 1 minute ago';
    }else{
    return sprintf( '%s minutes ago', intval($diff / 60) );
}
    }else if ( $diff < 7200 ){
    return '< 1 hour ago';
    }else if ( $diff < 86400 ){
    return sprintf( '%s hours ago', intval($diff / 3600) );
    }else if ( $diff < 172800 ){
    return '< 1 day ago';
    }else if ( $diff < 604800 ){
    return sprintf( '%s days ago', intval($diff / 86400) );
    }else if ( $diff < 1209600 ){
    return '< 1 week ago';
    }else if ( $diff < 3024000 ){
    return sprintf( '%s weeks ago', intval($diff / 604900) );
    }else{
    return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
}
    }else if ( $FMED['time_use_relative'] and ( $norelative != 1 ) ){
    $this_time = gmdate('d,m,Y', ($date + $GLOBALS['offset']) );
if ( $FMED['time_use_relative'] == 2 ){
    $diff = time() - $date;
if ( $diff < 3600 ){
if ( $diff < 120 ){
    return '< 1 minute ago';
    }else{
    return sprintf( '%s minutes ago', intval($diff / 60) );
    }
  }
}
if ( $this_time == $today_time ){
    return str_replace( '{--}', 'Today', gmdate($FMED['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
    }else if  ( $this_time == $yesterday_time ){
    return str_replace( '{--}', 'Yesterday', gmdate($FMED['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
    }else{
    return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
}
    }else{
    return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
    }
}
function hash_pad($hash) {
    return str_pad($hash, 20);
}
function load_language($file='') {
global $FMED;
if( !isset($GLOBALS['CURUSER']) OR empty($GLOBALS['CURUSER']['language']) ){
if( !file_exists(ROOT_PATH."/lang/{$FMED['language']}/lang_{$file}.php") ){
    stderr('SYSTEM ERROR', 'Can\'t find language files');
}
require_once ROOT_PATH."/lang/{$FMED['language']}/lang_{$file}.php";
    return $lang;
}
if( !file_exists(ROOT_PATH."/lang/{$GLOBALS['CURUSER']['language']}/lang_{$file}.php") ){
    stderr('SYSTEM ERROR', 'Can\'t find language files');
    }else{
    require_once ROOT_PATH."/lang/{$GLOBALS['CURUSER']['language']}/lang_{$file}.php";
}
    return $lang;
}
?>