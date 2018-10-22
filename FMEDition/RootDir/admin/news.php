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
if ( ! defined( 'IN_FMED_ADMIN' ) ){
    print "<h1>Incorrect access</h1>You cannot access this file directly.";
    exit();
}
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/html_functions.php";
    $lang = array_merge( $lang, load_language('ad_news') );
    $input = array_merge( $_GET, $_POST);
    $mode = isset($input['mode']) ? $input['mode'] : '';
    $warning = '';
    $HTMLOUT = '';
if('update' == $mode){
if(isset($input['news_update']) && count($input['news_update'])){
    foreach($input['news_update'] as $v){
if(!is_valid_id($v)) stderr("Error", "No news ID");
    $newsIDS[] = $v;
}
    }else{
    stderr("Error", "No data!");
}
    $news = join(',', $newsIDS);
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE news set added = ".time()." WHERE id IN ($news)");
if(-1 == mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    stderr("Error", "Update failed");
    header("Location: {$FMED['baseurl']}/admin.php?action=news");
}
if ($mode == 'delete'){
    $newsid = isset($input['newsid']) ? (int)$input["newsid"] : 0;
if (!is_valid_id($newsid))
    stderr($lang['news_error'],sprintf($lang['news_gen_error'],1));
    $returnto = isset($input['returno']) ? htmlentities($input["returnto"]) : '';
    $sure = isset($input["sure"]) ? (int)$input['sure'] : 0;
if (!$sure){
    stderr($lang['news_delete_notice'],sprintf($lang['news_delete_text'],$newsid));
}
    @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);
if ($returnto != "")
    header("Location: {$FMED['baseurl']}/admin.php?action=news");
    else
    $warning = $lang['news_delete_ok'];
}
if ($mode == 'add'){
    $body = isset($input["body"]) ? (string)$input["body"] : 0;
if ( !$body OR strlen($body) < 4 )
    stderr($lang['news_error'],$lang['news_add_body']);
    $body = sqlesc($body);
    $added = isset($input['added']) ? $input['added'] : 0;
    $headline = (isset($input['headline']) AND !empty($input['headline'])) ? sqlesc($input['headline']) : sqlesc('FMED.net News');
if (!$added)
    $added = time();
    @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO news (userid, added, body, headline) VALUES ({$CURUSER['id']}, $added, $body, $headline)") or sqlerr(__FILE__, __LINE__);
if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 1)
    $warning = $lang['news_add_ok'];
    else
    stderr($lang['news_error'],$lang['news_add_err']);
}
if ($mode == 'edit'){
    $newsid = isset($input["newsid"]) ? (int)$input["newsid"] : 0;
if (!is_valid_id($newsid))
    stderr($lang['news_error'], sprintf($lang['news_gen_error'],2));
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) != 1)
    stderr($lang['news_error'], $lang['news_edit_nonewsid']);
    $arr = mysqli_fetch_assoc($res);
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $body = isset($_POST['body']) ? $_POST['body'] : '';
if ($body == "" OR strlen($_POST['body']) < 4)
    stderr($lang['news_error'], $lang['news_add_body']);
    $headline = (isset($input['headline']) AND !empty($input['headline'])) ? sqlesc($input['headline']) : sqlesc('FMED.net News');
    $body = sqlesc($body);
    $editedat = time();
    @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE news SET body=$body, headline=$headline WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);
    $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';
if ($returnto != "")
    header("Location: {$FMED['baseurl']}/index.php");
    else
    $warning = $lang['news_edit_ok'];;
    }else{
    $HTMLOUT .= "<h1>{$lang['news_edit_title']}</h1>
    <form method='post' action='admin.php?action=news'>
    <input type='hidden' name='newsid' value='$newsid' />
    <input type='hidden' name='mode' value='edit' />
    <table width='700px'border='1' cellspacing='0' cellpadding='10px'>
    <tr>
          <td align='center'>
          <input style='width:650px;' type='text' name='headline' size='50' value='".htmlentities($arr['headline'], ENT_QUOTES, 'UTF-8')."' />
    </td>
    </tr>
    <tr>
    <td align='center'>
            <textarea style='width:650px;' name='body' cols='55' rows='10'>" . htmlentities($arr['body'], ENT_QUOTES) . "</textarea>
    </td>
    </tr>
    <tr>
    <td align='center'>
            <input type='submit' value='Okay' class='btn' />
    </td>
    </tr>
    </table>
    </form>\n";
    print  stdhead($lang['news_edit_title']) . $HTMLOUT . stdfoot();
    exit();
    }
}
    $HTMLOUT .= "<h1>{$lang['news_submit_title']}</h1>\n";
if (!empty($warning))
    $HTMLOUT .= "<p><font size='-3'>($warning)</font></p>";
    $HTMLOUT .= "<form method='post' action='admin.php?action=news'>
    <input type='hidden' name='mode' value='add' />
    <table width='750px' border='1' cellspacing='0' cellpadding='10px'>
    <tr>
    <td align='center'>
          <input  style='width:650px;' type='text' name='headline' size='50' value='' />
    </td>
    </tr>
    <tr>
    <td align='center'>
          <textarea style='width:650px;' name='body' cols='55' rows='10'></textarea>
    </td>
    </tr>
    <tr>
        <td align='center'>
          <input type='submit' value='Okay' class='btn' />
    </td>
    </tr>
    </table>
    </form><br /><br />";
    $res = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res) > 0){
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= "<form method='post' action='admin.php?action=news'><input type='hidden' name='mode' value='update' />";
    while ($arr = mysqli_fetch_assoc($res)){
    $newsid = $arr["id"];
    $body = format_comment($arr["body"]);
    $headline = htmlentities($arr['headline'], ENT_QUOTES, 'UTF-8');
    $userid = $arr["userid"];
    $added = get_date( $arr['added'],'');
    $res2 = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
    $arr2 = mysqli_fetch_assoc($res2);
    $postername = $arr2["username"];
if ($postername == "")
    $by = "unknown[$userid]";
    else
    $by = "<a href='userdetails.php?id=$userid'><b>$postername</b></a>" .
    ($arr2["donor"] == "yes" ? "<img src=\"{$FMED['pic_base_url']}star.gif\" alt='Donor' />" : "");
    $HTMLOUT .= begin_frame();
    $HTMLOUT .= begin_table(true);
    $HTMLOUT .= "
    <tr>
          <td class='colhead'>$headline<span style='float:right;'><input type='checkbox' name='news_update[]' value='$newsid' /></span></td>
    </tr>
    <tr>
          <td>{$added}&nbsp;&nbsp;by&nbsp$by
            <div style='float:right;'><a href='admin.php?action=news&amp;mode=edit&amp;newsid=$newsid'><span class='btn'>{$lang['news_act_edit']}</span></a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid'><span class='btn'>{$lang['news_act_delete']}</span></a>
          </div>
    </td>
    </tr>
    <tr valign='top'>
          <td class='comment'>$body</td>
    </tr>\n";
    $HTMLOUT .= end_table();
    $HTMLOUT .= end_frame();
    $HTMLOUT .= '<br />';
}
    $HTMLOUT .= "<div align='right'><input name='submit' type='submit' value='Update' class='btn' /></div></form>";
    $HTMLOUT .= end_main_frame();
    }else
    stdmsg($lang['news_sorry'], $lang['news_nonews']);
    print stdhead($lang['news_window_title']) . $HTMLOUT . stdfoot();
    die;
?> 