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
define('IN_FMED_ADMIN', TRUE);
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
dbconn(false);
loggedinorreturn();
    $lang = array_merge( load_language('global'), load_language('admin') );
if ($CURUSER['class'] < UC_MODERATOR)
    stderr("{$lang['admin_user_error']}", "{$lang['admin_unexpected']}");
    $action = isset($_GET["action"]) ? $_GET["action"] : '';
    $forum_pic_url = $FMED['pic_base_url'] . 'forumicons/';
define( 'F_IMAGES', $FMED['pic_base_url'] . 'forumicons');
define( 'POST_ICONS', F_IMAGES.'/post_icons');
    $ad_actions = array('bans'            => 'bans', 
                        'adduser'         => 'adduser', 
                        'stats'           => 'stats', 
                        'delacct'         => 'delacct', 
                        'testip'          => 'testip', 
                        'usersearch'      => 'usersearch', 
                        'mysql_overview'  => 'mysql_overview', 
                        'mysql_stats'     => 'mysql_stats', 
                        'categories'      => 'categories', 
                        'newusers'        => 'newusers', 
                        'resetpassword'   => 'resetpassword',
                        'docleanup'       => 'docleanup',
                        'log'             => 'log',
                        'news'            => 'news',
                        'forummanage'     => 'forummanage'
                        );
    
if( in_array($action, $ad_actions) AND file_exists( "admin/{$ad_actions[ $action ]}.php" ) ){
require_once "admin/{$ad_actions[ $action ]}.php";
}else{
require_once "admin/index.php";
}
?>