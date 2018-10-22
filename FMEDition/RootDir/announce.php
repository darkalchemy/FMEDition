<?php
error_reporting(0);
$FMED['baseurl'] = 'http://FMEDition.NeT/';
    $FMED['announce_interval'] = 60 * 30;
    $FMED['user_ratios'] = 0;
    $FMED['connectable_check'] = 0;
    define ('UC_USER', 0);
    define ('UC_POWER_USER', 1);
    define ('UC_VIP', 2);
    define ('UC_UPLOADER', 3);
    define ('UC_MODERATOR', 4);
    define ('UC_ADMINISTRATOR', 5);
    define ('UC_SYSOP', 6);
$FMED['mysql_user'] = "xblade";
$FMED['mysql_pass'] = "gDu4zFvR8ZRl186B";
$FMED['mysql_db']   = "xblade";
    $agent = $_SERVER["HTTP_USER_AGENT"];
if (ereg("^Mozilla\\/", $agent) ||
    ereg("^Opera\\/", $agent) ||
    ereg("^Links ", $agent) ||
    ereg("^Lynx\\/", $agent) ||
    isset($_SERVER['HTTP_COOKIE']) ||
    isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ||
    isset($_SERVER['HTTP_ACCEPT_CHARSET'])
)
    err("torrent not registered with this tracker CODE 1");

function dbconn(){
global $FMED;
if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($FMED['mysql_host'],  $FMED['mysql_user'],  $FMED['mysql_pass']))){
    err('Please call back later');
}
    mysqli_select_db($GLOBALS["___mysqli_ston"], $FMED['mysql_db']) or err('Please call back later');
}

function err($msg){
    benc_resp(array('failure reason' => array('type' => 'string', 'value' => $msg)));
    exit();
}

function benc_resp($d){
    benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
}

function benc_resp_raw($x){
    header( "Content-Type: text/plain" );
    header( "Pragma: no-cache" );
if ( $_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip' ){
   header( "Content-Encoding: gzip" );
   echo gzencode( $x, 9, FORCE_GZIP );
   }else
   echo $x ;
}

function benc($obj) {
if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
    return;
    $c = $obj["value"];
    switch ($obj["type"]) {
    case "string":
    return benc_str($c);
    case "integer":
    return benc_int($c);
    case "list":
    return benc_list($c);
    case "dictionary":
    return benc_dict($c);
    default:
    return;
    }
}

function benc_str($s) {
    return strlen($s) . ":$s";
}

function benc_int($i) {
    return "i" . $i . "e";
}

function benc_list($a) {
    $s = "l";
    foreach ($a as $e) {
    $s .= benc($e);
}
    $s .= "e";
    return $s;
}

function benc_dict($d) {
    $s = "d";
    $keys = array_keys($d);
    sort($keys);
    foreach ($keys as $k) {
    $v = $d[$k];
    $s .= benc_str($k);
    $s .= benc($v);
}
    $s .= "e";
    return $s;
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

function sqlesc($x) {
    return "'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $x)."'";
}

function portblacklisted($port){
if ($port >= 411 && $port <= 413) return true;
if ($port >= 6881 && $port <= 6889) return true;
if ($port == 1214) return true;
if ($port >= 6346 && $port <= 6347) return true;
if ($port == 4662) return true;
if ($port == 6699) return true;
    return false;
}
    $parts = array();
    $pattern = '[0-9a-fA-F]{32}';
if( !isset($_GET['passkey']) OR !ereg($pattern, $_GET['passkey'], $parts) )
    err("Invalid Passkey");
    else
    $GLOBALS['passkey'] = $parts[0];
    foreach (array("info_hash","peer_id","event","ip","localip") as $x) {
if(isset($_GET["$x"]))
    $GLOBALS[$x] = "" . $_GET[$x];
}
    foreach (array("port","downloaded","uploaded","left") as $x){
    $GLOBALS[$x] = 0 + $_GET[$x];
}
foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)
if (!isset($x)) err("Missing key: $x");
    foreach (array("info_hash","peer_id") as $x)
if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");
    unset($x);
    $info_hash = bin2hex($info_hash);
    $ip = $_SERVER['REMOTE_ADDR'];
    $port = 0 + $port;
    $downloaded = 0 + $downloaded;
    $uploaded = 0 + $uploaded;
    $left = 0 + $left;
    $rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k){
if (isset($_GET[$k])){
    $rsize = 0 + $_GET[$k];
    break;
    }
}
if (!$port || $port > 0xffff)
    err("invalid port");
if (!isset($event))
    $event = "";
    $seeder = ($left == 0) ? "yes" : "no";
dbconn();
    $user_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, uploaded, downloaded, class, downloadpos, enabled FROM users WHERE passkey=".sqlesc($passkey)) or err("Tracker error 2");
if ( mysqli_num_rows($user_query) != 1 )
    err("Unknown passkey. Please redownload the torrent from {$FMED['baseurl']}.");
    $user = mysqli_fetch_assoc($user_query);
if( $user['enabled'] == 'no' ) err('Permission denied, you\'re not enabled');
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, banned, free, vip, seeders + leechers AS numpeers, added AS ts FROM torrents WHERE info_hash = " .sqlesc($info_hash));//" . hash_where("info_hash", $info_hash));
    $torrent = mysqli_fetch_assoc($res);
if (!$torrent)
    err("torrent not registered with this tracker CODE 2");
    $torrentid = $torrent["id"];
    $fields = 'seeder, peer_id, ip, port, uploaded, downloaded, userid, ('.time().' - last_action) AS announcetime';
    $numpeers = $torrent["numpeers"];
    $limit = "";
if ($numpeers > $rsize)
    $limit = "ORDER BY RAND() LIMIT $rsize";
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT $fields FROM peers WHERE torrent = $torrentid AND connectable = 'yes' $limit");
if($_GET['compact'] != 1){
    $resp = "d" . benc_str("interval") . "i" . $FMED['announce_interval'] . "e" . benc_str("private") . 'i1e' . benc_str("peers") . "l";
    }else{
    $resp = "d" . benc_str("interval") . "i" . $FMED['announce_interval'] ."e" . benc_str("min interval") . "i" . 300 ."e5:"."peers" ;
}
    $peer = array();
    $peer_num = 0;
    while ($row = mysqli_fetch_assoc($res)){
if($_GET['compact'] != 1){
    $row["peer_id"] = str_pad($row["peer_id"], 20);
if ($row["peer_id"] === $peer_id){
    $self = $row;
    continue;
}
    $resp .= "d" .
    benc_str("ip") . benc_str($row["ip"]);
if (!$_GET['no_peer_id']) {
    $resp .= benc_str("peer id") . benc_str($row["peer_id"]);
}
    $resp .= benc_str("port") . "i" . $row["port"] . "e" . "e";
    }else{
    $peer_ip = explode('.', $row["ip"]);
    $peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
    $peer_port = pack("n*", (int)$row["port"]);
    $time = intval((time() % 7680) / 60);
if($_GET['left'] == 0){
    $time += 128;
}
    $time = pack("C", $time);
    $peer[] = $time . $peer_ip . $peer_port;
    $peer_num++;
    }

}
if ($_GET['compact']!=1)
    $resp .= "ee";
    else{
    $o = "";
    for($i=0;$i<$peer_num;$i++){
    $o .= substr($peer[$i], 1, 6);
}
    $resp .= strlen($o) . ':' . $o . 'e';
}
    $selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);
if (!isset($self)){
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT $fields FROM peers WHERE $selfwhere");
    $row = mysqli_fetch_assoc($res);
if ($row){
    $userid = $row["userid"];
    $self = $row;
    }
}
if (!isset($self)){
    $valid = @mysqli_fetch_row(@mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey)));
if ($valid[0] >= 2 && $seeder == 'no') err("Connection limit exceeded! You may only leech from one location at a time.");
if ($valid[0] >= 3 && $seeder == 'yes') err("Connection limit exceeded!");
if ($left > 0 && $user['class'] < UC_VIP && $FMED['user_ratios']){
    $gigs = $user["uploaded"] / (1024*1024*1024);
    $elapsed = floor((time() - $torrent["ts"]) / 3600);
    $ratio = (($user["downloaded"] > 0) ? ($user["uploaded"] / $user["downloaded"]) : 1);
if ($ratio < 0.5 || $gigs < 5) $wait = 0;
    elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 0;
    elseif ($ratio < 0.8 || $gigs < 8) $wait = 0;
    elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 0;
    else $wait = 0;
if ($elapsed < $wait)
    err("Not authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ!");
}
    }else{
    $free = $torrent["free"];
    $vip = $torrent["vip"];
    $upthis = max(0, $uploaded - $self["uploaded"]);
    $downthis = max(0, $downloaded - $self["downloaded"]);
    $upspeed = ($upthis > 0 ? $upthis / $self["announcetime"] : 0);
    $downspeed = ($downthis > 0 ? $downthis / $self["announcetime"] : 0);
    $announcetime = ($self["seeder"] == "yes" ? "seedtime = seedtime + $self[announcetime]" : "leechtime = leechtime + $self[announcetime]");
if ($free == 'yes') $downthis = 0;
if ($vip  == 'yes') $downthis = 0;
if ($upthis > 0 || $downthis > 0)
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET uploaded = uploaded + $upthis". ($torrent['free']=='no'?", downloaded = downloaded + $downthis ":' '). "WHERE id=".$user['id']) or err("Tracker error 3");
}
if (portblacklisted($port)){
    err("Port $port is blacklisted.");
    }elseif ( $FMED['connectable_check'] ){
    $sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
if (!$sockres)
    $connectable = "no";
    else{
    $connectable = "yes";
    @fclose($sockres);
}
    }else{
    $connectable = 'yes';
}
    $finished = $finished1 = '';
    $updateset = array();
if (isset($self) && $event == "stopped") {
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM peers WHERE $selfwhere") or err("D Err");
    $res_snatch = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT seedtime, uploaded, downloaded, finished, start_date AS start_snatch FROM snatched WHERE torrentid = $torrentid AND userid = $userid") or err('Snatch Error 1');
    $a = mysqli_fetch_array($res_snatch);
if( ($a['uploaded'] + $upthis) < ($a['downloaded'] + $downthis) && $a['finished'] == 'yes'){
    $HnR_time_seeded = ($a['seedtime'] + $self['announcetime']);
    switch (true){
    case ($user['class'] < UC_POWER_USER):
    $days_3 = 3*86400; //== 3 days
    $days_14 = 2*86400; //== 2 days
    $days_over_14 = 86400; //== 1 day
    break;
    case ($user['class'] == UC_POWER_USER):
    $days_3 = 2*86400; //== 2 days
    $days_14 = 129600; //== 36 hours
    $days_over_14 = 64800; //== 18 hours
    break;
    case ($user['class'] == UC_VIP):
    $days_3 = 129600; //== 36 hours
    $days_14 = 86400; //== 24 hours
    $days_over_14 = 43200; //== 12 hours
    break;
    case ($user['class'] >= UC_UPLOADER):
    $days_3 = 86400; //== 24 hours
    $days_14 = 43200; //== 12 hours
    $days_over_14 = 21600; //== 6 hours
    break;
}
    switch(true){
    case (($a['start_snatch'] - $torrent['ts']) < 7*86400):
    $minus_ratio = ($days_3 - $HnR_time_seeded);
    break;
    case (($a['start_snatch'] - $torrent['ts']) < 21*86400):
    $minus_ratio = ($days_14 - $HnR_time_seeded);
    break;
    case (($a['start_snatch'] - $torrent['ts']) >= 21*86400):
    $minus_ratio = ($days_over_14 - $HnR_time_seeded);
    break;
}
    $hit_and_run = (($minus_ratio > 0 && ($a['uploaded'] + $upthis) < ($a['downloaded'] + $downthis)) ? ", seeder='no', hit_and_run= '".time()."'" : ", hit_and_run = '0'");
    }else
    $hit_and_run = ", hit_and_run = '0'";
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
    $updateset[] = ($self["seeder"] == "yes" ? "seeders = seeders - 1" : "leechers = leechers - 1");
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = ".TIME().", seeder = '$seeder', agent = ".sqlesc($agent)." $hit_and_run WHERE torrentid = $torrentid AND userid = {$user['id']}") OR err("SL Err 1");
}
    }elseif (isset($self)) {
if ($event == "completed") {
    $updateset[] = "times_completed = times_completed + 1";
    $finished = ", finishedat = ".time()."";
    $finished1 = ", complete_date = ".time().", finished = 'yes'";
}
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE peers SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = " . time() . ", seeder = '$seeder', agent = ".sqlesc($agent)." $finished WHERE $selfwhere") or err("PL Err 1");
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
if ($seeder <> $self["seeder"])
    $updateset[] = ($seeder == "yes" ? "seeders = seeders + 1, leechers = leechers - 1" : "seeders = seeders - 1, leechers = leechers + 1");
    $anntime = "timesann = timesann + 1";
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = ".time().", seeder = '$seeder', agent = ".sqlesc($agent)." $finished1, $anntime WHERE torrentid = $torrentid AND userid = {$user['id']}") or err("SL Err 2");
}
    } else {
if ($az["downloadpos"] == "no")
    err("Your downloading priviledges have been disabled! (Read the rules)");
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO peers (torrent, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, started, last_action, seeder, agent, downloadoffset, uploadoffset, passkey) VALUES ($torrentid, {$user['id']}, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, ".time().", ".time().", '$seeder', ".sqlesc($agent).", $downloaded, $uploaded, ".sqlesc($passkey).")") or err("PL Err 2");
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
    $updateset[] = ($seeder == "yes" ? "seeders = seeders + 1" : "leechers = leechers + 1");
    $anntime = "timesann = timesann + 1";
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', to_go = $left, last_action = ".time().", seeder = '$seeder', agent = ".sqlesc($agent).", $anntime, hit_and_run = '0', mark_of_cain = 'no' WHERE torrentid = $torrentid AND userid = {$user['id']}") or err("SL Err 3");
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]) && $seeder == "no")
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO snatched (torrentid, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, start_date, last_action, seeder, agent) VALUES ($torrentid, {$user['id']}, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, ".time().", ".time().", '$seeder', ".sqlesc($agent).")") or err("SL Err 4");
    }
}
if ($seeder == "yes"){
if ($torrent["banned"] != "yes")
    $updateset[] = "visible = 'yes'";
    $updateset[] = "last_action = ".time();
}
if (count($updateset))
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");
    benc_resp_raw($resp);
?>