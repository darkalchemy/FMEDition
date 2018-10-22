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
require_once "emoticons.php";
function _strlastpos ($haystack, $needle, $offset = 0){
    $addLen = strlen ($needle);
    $endPos = $offset - $addLen;
    while (true){
if (($newPos = strpos ($haystack, $needle, $endPos + $addLen)) === false) break;
    $endPos = $newPos;
}
    return ($endPos >= 0) ? $endPos : false;
}
function format_urls($s){
    return preg_replace("/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i","\\1<a target='_blank' href='redir.php?url=\\2'>\\2</a>", $s);
}
function validate_imgs($s){
    $start = "(http|https)://";
    $end = "+\.(?:jpe?g|png|gif)";
    preg_match_all("!" . $start . "(.*)" . $end . "!Ui", $s, $result);
    $array = $result[0];
    for ($i = 0; $i < count($array); $i++) {
    $headers = @get_headers($array[$i]);
if (strpos($headers[0], "200") === false) {
    $s = str_replace("[img]" . $array[$i] . "[/img]", "", $s);
    $s = str_replace("[img=" . $array[$i] . "]", "", $s);
    }
}
    return $s;
}
function scale($src){
    $max = 350;
if (!isset($max, $src))
    return;
    $src = str_replace(" ", "%20", $src[1]);
    $info = @getimagesize($src);
    $sw = $info[0];
    $sh = $info[1];
    $addclass = false;
    $max_em = 0.06 * $max;
if ($max < max($sw, $sh)) {
if ($sw > $sh)
    $new = array($max_em . "em", "auto");
if ($sw < $sh)
    $new = array("auto", $max_em . "em");
    $addclass = true;
    } else
    $new = array("auto", "auto");
    $id = mt_rand(0000, 9999);
if ($new[0] == "auto" && $new[1] == "auto")
    $img = "<img src=\"" . $src . "\" border=\"0\" alt=\"\" />";
    else
    $img = "<img id=\"r" . $id . "\" border=\"0\" alt=\"\" src=\"" . $src . "\" " . ($addclass ? "class=\"resized\"" : "") . " style=\"width:" . $new[0] . ";height:" . $new[1] . ";\" />";
    return $img;
}
function format_quotes($s){
    $old_s = '';
    while ($old_s != $s){
    $old_s = $s;
    $close = strpos($s, "[/quote]");
if ($close === false)
    return $s;
    $open = _strlastpos(substr($s,0,$close), "[quote");
if ($open === false)
    return $s;
    $quote = substr($s,$open,$close - $open + 8);
    $quote = preg_replace("/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i","<p class='sub'><b>Quote:</b></p><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>\\1</td></tr></table><br />", $quote);
    $quote = preg_replace("/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i","<p class='sub'><b>\\1 wrote:</b></p><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>\\2</td></tr></table><br />", $quote);
    $s = substr($s,0,$open) . $quote . substr($s,$close + 8);
}
    return $s;
}
function format_comment($text, $strip_html = true){
global $smilies, $FMED;
    $s = $text;
    unset($text);
    $s = str_replace(";)", ":wink:", $s);
if ($strip_html)
    $s = htmlentities($s, ENT_QUOTES, 'UTF-8');
if( preg_match( "#function\s*\((.*?)\|\|#is", $s ) ){
    $s = str_replace( ":"     , "&#58;", $s );
    $s = str_replace( "["     , "&#91;", $s );
    $s = str_replace( "]"     , "&#93;", $s );
    $s = str_replace( ")"     , "&#41;", $s );
    $s = str_replace( "("     , "&#40;", $s );
    $s = str_replace( "{"     , "&#123;", $s );
    $s = str_replace( "}"     , "&#125;", $s );
    $s = str_replace( "$"     , "&#36;", $s );
}
    $s = preg_replace("/\[\*\]/", "<li>", $s);
    $s = preg_replace("/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s);
    $s = preg_replace("/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s);
    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s);
    $s = preg_replace("/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s);
    $s = preg_replace_callback("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/i", "scale", $s);
    $s = preg_replace_callback("/\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]/i", "scale", $s);
    $s = preg_replace("/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i","<font color='\\1'>\\2</font>", $s);
    $s = preg_replace("/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i","<font color='\\1'>\\2</font>", $s);
    $s = preg_replace("/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i","<a target=_blank href=redir.php?url=\\1>\\2</a>", $s);
    $s = preg_replace("/\[url\]([^()<>\s]+?)\[\/url\]/i","<a target=_blank href=redir.php?url=\\1>\\1</a>", $s);
    $s = preg_replace("/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i","<font size='\\1'>\\2</font>", $s);
    $s = preg_replace("/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i","<font face=\"\\1\">\\2</font>", $s);
    $s = format_quotes($s);
    $s = format_urls($s);
    $s = nl2br($s);
    $s = preg_replace("/\[pre\]((\s|.)+?)\[\/pre\]/i", "<tt><span style=\"white-space: nowrap;\">\\1</span></tt>", $s);
    $s = preg_replace("/\[nfo\]((\s|.)+?)\[\/nfo\]/i", "<tt><span style=\"white-space: nowrap;\"><font face='MS Linedraw' size='2' style='font-size: 10pt; line-height: " ."10pt'>\\1</font></span></tt>", $s);
    $s = str_replace("  ", " &nbsp;", $s);
    foreach($smilies as $code => $url) {
    $s = str_replace($code, "<img border='0' src=\"{$FMED['pic_base_url']}smilies/{$url}\" alt=\"" . htmlspecialchars($code) . "\" />", $s);
}
    return $s;
}
?>