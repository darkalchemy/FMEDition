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
require_once("bittorrent.php");
function deadtime() {
    global $FMED;
    return time() - floor($FMED['announce_interval'] * 1.3);
}
function docleanup() {
global $FMED;
    set_time_limit(0);
    ignore_user_abort(1);
    do {
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM torrents");
    $ar = array();
    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
    $id = $row[0];
    $ar[$id] = 1;
}
if (!count($ar))
    break;
    $dp = @opendir($FMED['torrent_dir']);
if (!$dp)
    break;
    $ar2 = array();
    while (($file = readdir($dp)) !== false) {
if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
    continue;
    $id = $m[1];
    $ar2[$id] = 1;
if (isset($ar[$id]) && $ar[$id])
    continue;
    $ff = $FMED['torrent_dir'] . "/$file";
    unlink($ff);
}
    closedir($dp);
if (!count($ar2))
    break;
    $delids = array();
    foreach (array_keys($ar) as $k) {
if (isset($ar2[$k]) && $ar2[$k])
    continue;
    $delids[] = $k;
    unset($ar[$k]);
}
if (count($delids))
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT torrent FROM peers GROUP BY torrent");
    $delids = array();
    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
    $id = $row[0];
if (isset($ar[$id]) && $ar[$id])
    continue;
    $delids[] = $id;
}
if (count($delids))
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT torrent FROM files GROUP BY torrent");
    $delids = array();
    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
    $id = $row[0];
if (isset($ar[$id]) && $ar[$id])
    continue;
    $delids[] = $id;
}
if (count($delids))
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM files WHERE torrent IN (" . join(",", $delids) . ")");
    } while (0);
    $deadtime = deadtime();
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM peers WHERE last_action < $deadtime");
    $deadtime -= $FMED['max_dead_torrent_time'];
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < $deadtime");
    $deadtime = time() - $FMED['signup_timeout'];
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM users WHERE status = 'pending' AND added < $deadtime AND last_login < $deadtime AND last_access < $deadtime");
    $torrents = array();
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder");
    while ($row = mysqli_fetch_assoc($res)) {
if ($row["seeder"] == "yes")
    $key = "seeders";
    else
    $key = "leechers";
    $torrents[$row["torrent"]][$key] = $row["c"];
}
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent");
    while ($row = mysqli_fetch_assoc($res)) {
    $torrents[$row["torrent"]]["comments"] = $row["c"];
}
    $fields = explode(":", "comments:leechers:seeders");
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, seeders, leechers, comments FROM torrents");
    while ($row = mysqli_fetch_assoc($res)) {
    $id = $row["id"];
if(isset($torrents[$id]))
    $torr = $torrents[$id];
    foreach ($fields as $field) {
if (!isset($torr[$field]))
    $torr[$field] = 0;
}
    $update = array();
    foreach ($fields as $field) {
if ($torr[$field] != $row[$field])
    $update[] = "$field = " . $torr[$field];
}
if (count($update))
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id");
}
    $res = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT COUNT(torrent) As tcount, userid FROM peers WHERE seeder =\'yes\' GROUP BY userid') or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0) {
    while ($arr = mysqli_fetch_assoc($res)) {
if ($arr['tcount'] >= 1000)
    $arr['tcount'] = 5;
    $users_buffer[] = '(' . $arr['userid'] . ',1.225 * ' . $arr['tcount'] . ')';
}
if (sizeof($users_buffer) > 0) {
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO users (id,seedbonus) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE seedbonus=seedbonus+values(seedbonus)") or sqlerr(__FILE__, __LINE__);
    $count = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
    write_log("Cleanup - " . $count / 2 . " users received seedbonus");
}
    unset ($users_buffer);
}
    $secs = 42*86400;
    $dt = (time() - $secs);
    $maxclass = UC_POWER_USER;
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM users WHERE status='confirmed' AND class <= $maxclass AND last_access < $dt");
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM users WHERE warned='yes' AND warneduntil < ".time()." AND warneduntil <> 0") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0){
    $dt = time();
    $msg = sqlesc("Your warning has been removed. Please keep in your best behaviour from now on.\n");
    while ($arr = mysqli_fetch_assoc($res)){
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET warned = 'no', warneduntil = 0 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
}
    $secs = 30 * 86400;
    $dt = sqlesc(time() - $secs);
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM funds WHERE added < $dt");
if (is_file("cache/funds.txt"))
    unlink("cache/funds.txt");
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, modcomment, vipclass_before FROM users WHERE donor='yes' AND donoruntil < ".TIME_NOW." AND donoruntil <> '0'") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
if (mysqli_num_rows($res) > 0) {
    $subject = "Donor status removed by system.";
    $msg = "Your Donor status has timed out and has been auto-removed by the system, and your Vip status has been removed. We would like to thank you once again for your support to {$FMED['site_name']}. If you wish to re-new your donation, Visit the site paypal link. Cheers!\n";
    while ($arr = mysqli_fetch_assoc($res)) {
    $modcomment = sqlesc(get_date( time(), 'DATE', 1 ) . " - Donation status Automatically Removed By System\n");
    $msgs_buffer[] = '(0,' . $arr['id'] . ','.time().', ' . sqlesc($msg) . ',' . sqlesc($subject) . ')';
    $users_buffer[] = '(' . $arr['id'] . ','.$arr['vipclass_before'].',\'no\',\'0\', ' . $modcomment . ')';
}
if (sizeof($msgs_buffer) > 0) {
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO users (id, class, donor, donoruntil, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class),donor=values(donor),donoruntil=values(donoruntil),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
    $count = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
    write_log("Cleanup: Donation status expired - " . $count / 2 . " Member(s)");
}
    unset ($users_buffer);
    unset ($msgs_buffer);
}
    $limit = 25*1024*1024*1024;
    $minratio = 1.05;
    $maxdt = (time() - 86400*28);
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0){
    $dt = time();
    $msg = sqlesc("Congratulations, you have been auto-promoted to [b]Power User[/b]. :)\nYou can now download dox over 1 meg and view torrent NFOs.\n");
    while ($arr = mysqli_fetch_assoc($res)){
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET class = 1 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
}
    $minratio = 0.95;
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM users WHERE class = 1 AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0){
    $dt = time();
    $msg = sqlesc("You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below $minratio.\n");
    while ($arr = mysqli_fetch_assoc($res)){
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET class = 0 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
}
    $secs = 3 * 86400;
    $hnr = time() - $secs;
    $res = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT id FROM snatched WHERE hit_and_run <> \'0\' AND hit_and_run < '.sqlesc($hnr).'') or sqlerr(__FILE__, __LINE__);
    while ($arr = mysqli_fetch_assoc($res)){
    mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE snatched SET mark_of_cain = \'yes\' WHERE id='.sqlesc($arr['id'])) or sqlerr(__FILE__, __LINE__);
}
    $res_fuckers = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT count(*) AS poop, snatched.userid, users.username, users.modcomment, users.hit_and_run_total, users.downloadpos FROM snatched LEFT JOIN users ON snatched.userid = users.id WHERE snatched.mark_of_cain = \'yes\' AND users.hnrwarn = \'no\' GROUP BY snatched.userid') or sqlerr(__FILE__, __LINE__);
    while ($arr_fuckers = mysqli_fetch_assoc($res_fuckers)){
if ($arr_fuckers['poop'] > 3 && $arr_fuckers['downloadpos'] == 'yes'){
    $subject = sqlesc('Download disabled by System');
    $msg = sqlesc("Sorry ".$arr_fuckers['username'].",\n Because you have 3 or more torrents that have not been seeded to either a 1:1 ratio, or for the expected seeding time, your downloading rights have been disabled by the Auto system !\nTo get your Downloading rights back is simple,\n just start seeding the torrents in your profile [ click your username, then click your [url=".$FMED['baseurl']."/userdetails.php?id=".$arr_fuckers['userid']."&completed=1]Completed Torrents[/url] link to see what needs seeding ] and your downloading rights will be turned back on by the Auto system after the next clean-time [ updates 4 times per hour ].\n\nDownloads are disabled after a member has three or more torrents that have not been seeded to either a 1 to 1 ratio, OR for the required seed time [ please see the [url=".$FMED['baseurl']."/faq.php]FAQ[/url] or [url=".$FMED['baseurl']."/rules.php]Site Rules[/url] for more info ]\n\nIf this message has been in error, or you feel there is a good reason for it, please feel free to PM a staff member with your concerns.\n\n we will do our best to fix this situation.\n\nBest of luck!\n ".$FMED['site_name']." staff.\n");
    $modcomment = htmlspecialchars($arr_fuckers['modcomment']);
    $modcomment =  get_date( time(), 'DATE', 1 ) . " - Download rights removed for H and R - AutoSystem.\n". $modcomment;
    $modcom =  sqlesc($modcomment);
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, $arr_fuckers[userid], ".sqlesc(time()).", $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
    mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE users SET hit_and_run_total = hit_and_run_total + '.$arr_fuckers['poop'].', downloadpos = \'no\', hnrwarn = \'yes\', modcomment = '.$modcom.'  WHERE downloadpos = \'yes\' AND id='.sqlesc($arr_fuckers['userid'])) or sqlerr(__FILE__, __LINE__);
    }
}
    $res_good_boy = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT id, username, modcomment FROM users WHERE hnrwarn = \'yes\' AND downloadpos = \'no\'') or sqlerr(__FILE__, __LINE__);
    while ($arr_good_boy = mysqli_fetch_assoc($res_good_boy)){
    $res_count = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT count(*) FROM snatched WHERE userid = '.sqlesc($arr_good_boy['id']).' AND mark_of_cain = \'yes\'') or sqlerr(__FILE__, __LINE__);
    $arr_count = mysqli_fetch_row($res_count);
if ($arr_count[0] < 3){
    $subject = sqlesc('Download restored by System');
    $msg = sqlesc("Hi ".$arr_good_boy['username'].",\n Congratulations ! Because you have seeded the torrents that needed seeding, your downloading rights have been restored by the Auto System !\n\nhave fun !\n ".$FMED['site_name']." staff.\n");
    $modcomment = htmlspecialchars($arr_good_boy['modcomment']);
    $modcomment =  get_date( time(), 'DATE', 1 ) . " - Download rights restored from H and R - AutoSystem.\n". $modcomment;
    $modcom =  sqlesc($modcomment);
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, ".sqlesc($arr_good_boy['id']).", ".sqlesc(time()).", $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
    mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE users SET downloadpos = \'yes\', hnrwarn = \'no\', modcomment = '.$modcom.'  WHERE id = '.sqlesc($arr_good_boy['id'])) or sqlerr(__FILE__, __LINE__);
    }
}
    $seeders = get_row_count("peers", "WHERE seeder='yes'");
    $leechers = get_row_count("peers", "WHERE seeder='no'");
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE avps SET value_u=$seeders WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE avps SET value_u=$leechers WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
    $forums = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT f.id, count( DISTINCT t.id ) AS topics, count( * ) AS posts FROM forums f LEFT JOIN topics t ON f.id = t.forumid LEFT JOIN posts p ON t.id = p.topicid GROUP BY f.id");
    while ($forum = mysqli_fetch_assoc($forums)){
    $forum['posts'] = $forum['topics'] > 0 ? $forum['posts'] : 0;
    @mysqli_query($GLOBALS["___mysqli_ston"], "update forums set postcount={$forum['posts']}, topiccount={$forum['topics']} where id={$forum['id']}");
}
    $days = 28;
    $dt = (time() - ($days * 86400));
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, name FROM torrents WHERE added < $dt");
    while ($arr = mysqli_fetch_assoc($res)){
    @unlink("{$FMED['torrent_dir']}/{$arr['id']}.torrent");
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM torrents WHERE id={$arr['id']}");
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM peers WHERE torrent={$arr['id']}");
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM comments WHERE torrent={$arr['id']}");
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM files WHERE torrent={$arr['id']}");
    write_log("Torrent {$arr['id']} ({$arr['name']}) was deleted by system (older than $days days)");
}
    $dt = (time() - $FMED['readpost_expiry']);
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE readposts FROM readposts "."LEFT JOIN posts ON readposts.lastpostread = posts.id "."WHERE posts.added < $dt") or sqlerr(__FILE__,__LINE__);
}
?>