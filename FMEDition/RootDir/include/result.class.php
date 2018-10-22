<?php
 /**
**************************
** FMEdition v1.x PHP Version: 5.6.32 **
**************************
** FMEdition
** A bittorrent tracker source.
** Project Leaders: xblade
**/
function mysqli_result($res,$row=0,$col=0){
    $numrows = mysqli_num_rows($res);
if ($numrows && $row <= ($numrows-1) && $row >=0){
    mysqli_data_seek($res,$row);
    $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
if (isset($resrow[$col])){
    return $resrow[$col];
    }
}
    return false;
}
?>