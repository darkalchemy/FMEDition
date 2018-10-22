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
function get_reputation($user, $mode = 0, $rep_is_on = TRUE){
global $FMED;
    $member_reputation = "";
if( $rep_is_on ){
@include 'cache/rep_cache.php';
if( ! isset( $reputations ) || ! is_array( $reputations ) || count( $reputations ) < 1){
    return '<span title="Cache doesn\'t exist or zero length">Reputation: Offline</span>';
}
    $user['g_rep_hide'] = isset( $user['g_rep_hide'] ) ? $user['g_rep_hide'] : 0;
    $max_rep = max(array_keys($reputations));
if($user['reputation'] >= $max_rep){
    $user_reputation = $reputations[$max_rep];
    }else
    foreach($reputations as $y => $x){
if( $y > $user['reputation'] ) { $user_reputation = $old; break; }
    $old = $x;
}
    $rep_power = $user['reputation'];
    $posneg = '';
if( $user['reputation'] == 0 ){
    $rep_img   = 'balance';
    $rep_power = $user['reputation'] * -1;
    }elseif( $user['reputation'] < 0 ){
    $rep_img   = 'neg';
    $rep_img_2 = 'highneg';
    $rep_power = $user['reputation'] * -1;
    }else{
    $rep_img   = 'pos';
    $rep_img_2 = 'highpos';
}
if( $rep_power > 500 ){
    $rep_power = ( $rep_power - ($rep_power - 500) ) + ( ($rep_power - 500) / 2 );
}
    $rep_bar = intval($rep_power / 100);
if( $rep_bar > 10 ){
    $rep_bar = 10;
}
if( $user['g_rep_hide'] ){
    $posneg = 'off';
    $rep_level = 'rep_off';
    }else{
    $rep_level = $user_reputation ? $user_reputation : 'rep_undefined';// just incase
    for( $i = 0; $i <= $rep_bar; $i++ ){
if( $i >= 5 ){
    $posneg .= "<img src='pic/rep/reputation_$rep_img_2.gif' border='0' alt=\"Reputation Power $rep_power\n{$user['username']} $rep_level\" title=\"Reputation Power $rep_power {$user['username']} $rep_level\" />";
    }else{
    $posneg .= "<img src='pic/rep/reputation_$rep_img.gif' border='0' alt=\"Reputation Power $rep_power\n{$user['username']} $rep_level\" title=\"Reputation Power $rep_power {$user['username']} $rep_level\" />";
    }
  }
}
if( $mode === 0 )
    return "Rep: ".$posneg . "<br /><a href='javascript:;' onclick=\"PopUp('{$FMED['baseurl']}/reputation.php?pid={$user['id']}','Reputation',400,241,1,1);\"><img src='./pic/plus.gif' border='0' alt='Add reputation:: {$user['username']}' title='Add reputation:: {$user['username']}' /></a>";
    else
    return "Rep: ".$posneg;
}
    return '<span title="Set offline by admin setting">Rep System Offline</span>';
}
function get_user_icons($arr, $big = false){
global $FMED;
if ($big){
    $donorpic = "starbig.gif";
    $warnedpic = "warnedbig.gif";
    $disabledpic = "disabledbig.gif";
    $style = "style='margin-left: 4pt'";
    }else{
    $donorpic = "star.gif";
    $warnedpic = "warned.gif";
    $disabledpic = "disabled.gif";
    $style = "style=\"margin-left: 2pt\"";
}
    $pics = $arr["donor"] == "yes" ? "<img src=\"{$FMED['pic_base_url']}/userimages/{$donorpic}\" alt='Donor' border='0' $style />" : "";
    $pics .= $arr["warned"] == "yes" ? "<img src=\"{$FMED['pic_base_url']}/userimages/{$warnedpic}\" alt=\"Warned\" border='0' $style />" : "";
    return $pics;
}
function get_ratio_color($ratio){
if ($ratio < 0.1) return "#ff0000";
if ($ratio < 0.2) return "#ee0000";
if ($ratio < 0.3) return "#dd0000";
if ($ratio < 0.4) return "#cc0000";
if ($ratio < 0.5) return "#bb0000";
if ($ratio < 0.6) return "#aa0000";
if ($ratio < 0.7) return "#990000";
if ($ratio < 0.8) return "#880000";
if ($ratio < 0.9) return "#770000";
if ($ratio < 1) return "#660000";
    return "#000000";
}
function get_slr_color($ratio){
if ($ratio < 0.025) return "#ff0000";
if ($ratio < 0.05) return "#ee0000";
if ($ratio < 0.075) return "#dd0000";
if ($ratio < 0.1) return "#cc0000";
if ($ratio < 0.125) return "#bb0000";
if ($ratio < 0.15) return "#aa0000";
if ($ratio < 0.175) return "#990000";
if ($ratio < 0.2) return "#880000";
if ($ratio < 0.225) return "#770000";
if ($ratio < 0.25) return "#660000";
if ($ratio < 0.275) return "#550000";
if ($ratio < 0.3) return "#440000";
if ($ratio < 0.325) return "#330000";
if ($ratio < 0.35) return "#220000";
if ($ratio < 0.375) return "#110000";
    return "#000000";
}
function get_user_class(){
global $CURUSER;
    return $CURUSER["class"];
}
$class_names = array(
    UC_USER                 => 'User',
    UC_POWER_USER           => 'Power User',
    UC_VIP                  => 'VIP',
    UC_UPLOADER             => 'Uploader',
    UC_MODERATOR            => 'Moderator',
    UC_ADMINISTRATOR        => 'Administrator',
    UC_SYSOP                => 'SysOp');
$class_colors = array(
    UC_USER                 => '8E35EF',
    UC_POWER_USER           => 'f9a200',
    UC_VIP                  => '009F00',
    UC_UPLOADER             => '0000FF',
    UC_MODERATOR            => 'FE2E2E',
    UC_ADMINISTRATOR        => 'B000B0',
    UC_SYSOP                => '4080B0');
$class_images = array(
    UC_USER                 => $FMED['pic_base_url'].'class/user.png',
    UC_POWER_USER           => $FMED['pic_base_url'].'class/power_user.png',
    UC_VIP                  => $FMED['pic_base_url'].'class/vip.png',
    UC_UPLOADER             => $FMED['pic_base_url'].'class/uploader.png',
    UC_MODERATOR            => $FMED['pic_base_url'].'class/moderator.png',
    UC_ADMINISTRATOR        => $FMED['pic_base_url'].'class/admin.png',
    UC_SYSOP                => $FMED['pic_base_url'].'class/sysop.png');
function get_user_class_name($class) {
global $class_names;
    $class = (int)$class;
if (!valid_class($class))
    return '';
if (isset($class_names[$class]))
    return $class_names[$class];
    else
    return '';
}
function get_user_class_color($class) {
global $class_colors;
    $class = (int)$class;
if (!valid_class($class))
    return '';
if (isset($class_colors[$class]))
    return $class_colors[$class];
    else
    return '';
}
function get_user_class_image($class) {
global $class_images;
    $class = (int)$class;
if (!valid_class($class))
    return '';
if (isset($class_images[$class]))
    return $class_images[$class];
    else
    return '';
}
function valid_class($class) {
    $class = (int)$class;
    return (bool)($class >= UC_MIN && $class <= UC_MAX);
}
function min_class($min = UC_MIN, $max = UC_MAX) {
global $CURUSER;
    $minclass = (int)$min;
    $maxclass = (int)$max;
if (!isset($CURUSER))
    return false;
if (!valid_class($minclass) || !valid_class($maxclass))
    return false;
if ($maxclass < $minclass)
    return false;
    return (bool)($CURUSER['class'] >= $minclass && $CURUSER['class'] <= $maxclass);
}
function autoshout($msg) {
global $FMED;
require_once "include/bbcode_functions.php";
    mysqli_query($GLOBALS["___mysqli_ston"], 'INSERT INTO shoutbox(userid, date, text, text_parsed)VALUES ('.$FMED['bot_id'].','.time().','.sqlesc($msg).','.sqlesc(format_comment($msg)).')');
}
function format_username($user, $icons = true) {
global $FMED;
    $user['id'] = (int)$user['id'];
    $user['class'] = (int)$user['class'];
if ($user['id'] == 0)
    return 'System';
    elseif ($user['username'] == '')
    return 'unknown['.$user['id'].']';
    $username = '<span style="color:#'.get_user_class_color($user['class']).';"><b>'.$user['username'].'</b></span>';
    $str = '<span style="white-space: nowrap;"><a class="user_'.$user['id'].'" href="'.$FMED['baseurl'].'/userdetails.php?id='.$user['id'].'"target="_blank">'.$username.'</a>';
if ($icons != false) {
    $str .= ($user['donor'] == 'yes' ? '<img src="'.$FMED['pic_base_url'].'/userimages/star.gif" alt="Donor" title="Donor" />' : '');
    $str .= ($user['warned'] >= 1 ? '<img src="'.$FMED['pic_base_url'].'/userimages/warned.png" alt="Warned" title="Warned" />' : '');
    $str .= ($user['enabled'] != 'yes' ? '<img src="'.$FMED['pic_base_url'].'/userimages/disabled.gif" alt="Disabled" title="Disabled" />' : '');
}
    $str .= "</span>\n";
    return $str;
}
function is_valid_user_class($class){
  return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_SYSOP;
}
function is_valid_id($id){
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}
?>