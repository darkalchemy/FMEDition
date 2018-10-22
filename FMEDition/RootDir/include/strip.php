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
function trim_ml (&$descr) {
    $lines = array();
    foreach( explode( "\n", $descr ) as  $line ) {
    $lines[] = trim( $line, "\x00..\x1F.,-+=\t ~" );
}
    $descr = implode( "\n", $lines );
}
function trim_regex( $pattern, $replacement, $subject ) {
    trim_ml( $subject );
    return preg_replace( $pattern, $replacement, $subject );
}
function strip(&$desc){
	$desc=preg_replace('`[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f-\xff]`','',$desc);
	return;
} 
?>
