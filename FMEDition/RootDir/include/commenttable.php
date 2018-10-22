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
function commenttable($rows){
global $CURUSER, $FMED;
	$lang = load_language( 'torrenttable_functions' );
	$htmlout = '';
	$count = 0;
	$htmlout .= begin_main_frame();
	$htmlout .= begin_frame();
	foreach ($rows as $row){
	$htmlout .= "<p class='sub'>#{$row["id"]} {$lang["commenttable_by"]} ";
if (isset($row["username"])){
    $title = $row["title"];
if ($title == "")
    $title = get_user_class_name($row["class"]);
	else
	$title = htmlspecialchars($title);
    $htmlout .= "<a name='comm{$row["id"]}' href='userdetails.php?id={$row["user"]}'><b><font color='#" . get_user_class_color($row['class']) . "'> " . htmlspecialchars($row['username']) . "</font></b></a>" . ($row["donor"] == "yes" ? "<img src='{$FMED['pic_base_url']}star.gif' alt='".$lang["commenttable_donor_alt"]."' />" : "") . ($row["warned"] == "yes" ? "<img src="."'{$FMED['pic_base_url']}warned.gif' alt='".$lang["commenttable_warned_alt"]."' />" : "") . " ($title)\n";
	}else
   	$htmlout .= "<a name='comm{$row["id"]}'><i>(".$lang["commenttable_orphaned"].")</i></a>\n";
	$htmlout .= get_date( $row['added'],'');
	$htmlout .= ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "- [<a href='comment.php?action=edit&amp;cid={$row['id']}'>".$lang["commenttable_edit"]."</a>]" : "") .
	(get_user_class() >= UC_MODERATOR ? "- [<a href='comment.php?action=delete&amp;cid={$row['id']}'>".$lang["commenttable_delete"]."</a>]" : "") .
	($row["editedby"] && get_user_class() >= UC_MODERATOR ? "- [<a href='comment.php?action=vieworiginal&amp;cid={$row['id']}'>".$lang["commenttable_view_original"]."</a>]" : "") . "</p>\n";
	$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
if (!$avatar)
    $avatar = "{$FMED['pic_base_url']}/userimages/default_avatar.gif";
	$text = format_comment($row["text"]);
if ($row["editedby"])
    $text .= "<p><font size='1' class='small'>".$lang["commenttable_last_edited_by"]." <a href='userdetails.php?id={$row['editedby']}'><b><font color='#" . get_user_class_color($row['class']) . "'> " . htmlspecialchars($row['username']) . "</font></b></a> ".$lang["commenttable_last_edited_at"]." ".get_date($row['editedat'],'DATE')."</font></p>\n";
	$htmlout .= begin_table(true);
	$htmlout .= "<tr valign='top'>\n";
	$htmlout .= "<td align='center' width='150' style='padding: 0px'><img width='100' height='100' src='$avatar'></td>\n";
	$htmlout .= "<td class='text'>$text</td>\n";
	$htmlout .= "</tr>\n";
    $htmlout .= end_table();
}
	$htmlout .= end_frame();
	$htmlout .= end_main_frame();
	return $htmlout;
}
?>