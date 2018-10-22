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
function tag_info() {
    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT searchedfor, howmuch FROM searchcloud ORDER BY id DESC LIMIT 50");
    while($row = mysqli_fetch_assoc($result)) {
    $arr[$row['searchedfor']] = $row['howmuch'];
}
if (isset($arr)) {
    ksort($arr);
    return $arr;
    }
}
function cloud() {
    $small = 10;
    $big = 35;
    $tags = tag_info();
if (isset($tags)) {
    $minimum_count = min(array_values($tags));
    $maximum_count = max(array_values($tags));
    $spread = $maximum_count - $minimum_count;
if($spread == 0) {$spread = 1;}
    $cloud_html = '';
    $cloud_tags = array();
    foreach ($tags as $tag => $count) {
    $size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
    $colour_array = array('yellow', 'green', 'blue', 'purple', 'orange', '#0099FF');
    $cloud_tags[] = '<a style="color:'.$colour_array[mt_rand(0, 5)].'; font-size: '. floor($size) . 'px'
    . '" class="tag_cloud" href="browse.php?search=' . urlencode($tag) . '&amp;cat=0&amp;incldead=1'
    . '" title="\'' . htmlentities($tag)  . '\' returned a count of ' . $count . '">'
    . htmlentities(stripslashes($tag)) . '</a>';
}
    $cloud_html = join("\n", $cloud_tags) . "\n";
    return $cloud_html;
    }
}
?>