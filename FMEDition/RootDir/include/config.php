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
error_reporting(E_ALL);
define('SQL_DEBUG', 2);
if (version_compare(PHP_VERSION, "5.1.0RC1", ">="))
    date_default_timezone_set('Europe/London');
define('TIME_NOW', time());
    $FMED['time_adjust'] =  0;
    $FMED['time_offset'] = '0';
    $FMED['time_use_relative'] = 1;
    $FMED['time_use_relative_format'] = '{--}, h:i A';
    $FMED['time_joined'] = 'j-F y';
    $FMED['time_short'] = 'jS F Y - h:i A';
    $FMED['time_long'] = 'M j Y, h:i A';
    $FMED['time_tiny'] = '';
    $FMED['time_date'] = '';
    $FMED['mysql_host'] = "localhost";
    $FMED['mysql_user'] = "xblade";
    $FMED['mysql_pass'] = "gDu4zFvR8ZRl186B";
    $FMED['mysql_db']   = "xblade";
    $FMED['cookie_prefix']  = 'FMEDition_'; // This allows you to have multiple trackers, eg for demos, testing etc.
    $FMED['cookie_path']    = ''; // ATTENTION: You should never need this unless the above applies eg: /FMED
    $FMED['cookie_domain']  = ''; // set to eg: .somedomain.com or is subdomain set to: .sub.somedomain.com
    $FMED['site_online'] = 1;
    $FMED['tracker_post_key'] = 'lsdflksfda4545frwe35@kk';
    $FMED['max_torrent_size'] = 1000000;
    $FMED['announce_interval'] = 60 * 30;
    $FMED['signup_timeout'] = 86400 * 3;
    $FMED['minvotes'] = 1;
    $FMED['max_dead_torrent_time'] = 6 * 3600;
    $FMED['maxusers'] = 5000; // LoL Who we kiddin' here?
    $FMED['site_donate_mail']  = 'paypal@FMEDtion.net';
if ( strtoupper( substr(PHP_OS, 0, 3) ) == 'WIN' ){
    $file_path = str_replace( "\\", "/", dirname(__FILE__) );
    $file_path = str_replace( "/include", "", $file_path );
    }else{
    $file_path = dirname(__FILE__);
    $file_path = str_replace( "/include", "", $file_path );
}
define('ROOT_PATH', $file_path);
    $FMED['torrent_dir'] = ROOT_PATH . '/torrents'; # must be writable for httpd user
    $FMED['announce_urls'] = array();
    $FMED['announce_urls'][] = "http://FMEDition.NeT/announce.php";
if ($_SERVER["HTTP_HOST"] == "")
    $_SERVER["HTTP_HOST"] = $_SERVER["SERVER_NAME"];
    $FMED['baseurl'] = "http://" . $_SERVER["HTTP_HOST"]."";
    $FMED['site_email'] = "noreply@FMEDition.NeT";
    $FMED['site_name'] = "FMEDition.NeT";
    $FMED['language'] = 'en';
    $FMED['char_set'] = 'UTF-8'; //also to be used site wide in meta tags
if (ini_get('default_charset') != $FMED['char_set']) {
    ini_set('default_charset',$FMED['char_set']);
}
    $FMED['msg_alert'] = 0; // saves a query when off
    $FMED['autoclean_interval'] = 900;
    $FMED['sql_error_log'] = ROOT_PATH.'/logs/sql_err_'.date("M_D_Y").'.log';
    $FMED['pic_base_url'] = "./pic/";
    $FMED['stylesheet'] = "1";
    $FMED['karma'] = 1;
    $FMED['invites'] = 100000000000000000000000;
    $FMED['bot_id'] = 2;
    $FMED['readpost_expiry'] = 14*86400; // 14 days
    $FMED['av_img_height'] = 100;
    $FMED['av_img_width'] = 100;
    $FMED['allowed_ext'] = array('image/gif', 'image/png', 'image/jpeg');
define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);
define('UC_MIN', 0);
define('UC_MAX', 6);
define('UC_STAFF', 4);
define ('FMED','FMEDition');
?>