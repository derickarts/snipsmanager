<?php
/**
 * Copyright (c) 2010-2011 SnipsManager (http://www.snipsmanager.com/), All Rights Reserved
 * A CodeHill Creation (http://codehill.com/)
 * 
 * IMPORTANT: 
 * - You may not redistribute, sell or otherwise share this software in whole or in part without
 *   the consent of SnipsManager's owners. Please contact the author for more information.
 * 
 * - Link to snipsmanager.com may not be removed from the software pages without permission of SnipsManager's
 *   owners. This copyright notice may not be removed from the source code in any case.
 *
 * - This file can be used, modified and distributed under the terms of the License Agreement. You
 *   may edit this file on a licensed Web site and/or for private development. You must adhere to
 *   the Source License Agreement. The latest copy can be found online at:
 * 
 *   http://www.snipsmanager.com/license/
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR 
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @link        http://www.snipsmanager.com/
 * @copyright   2010-2011 CodeHill LLC (http://codehill.com/)
 * @license     http://www.snipsmanager.com/license/
 * @author      Amgad Suliman, CodeHill LLC <amgadhs@codehill.com>
 * @version     2.2
 *
 * Encodes and inserts the snippet and it's details in the database table. Also echos the ID when finished.
 *
 */
 
session_start(); 

include('../config.php');
include('cryptor.php');
include('functions.php');

$drop = $_POST['drop'];
connect();

$code = ch_formatCodeForDatabase($_POST['code']);

$password = mysql_real_escape_string(htmlspecialchars(strip_tags($_POST['password'])));
$codetitle = htmlspecialchars($_POST['codetitle']);
$usecaptcha = ($_POST['captcha'] == 'on' ? 1 : 0);

$cryptor = new Cryptor();

$sqlInsert = "INSERT INTO codes (code, type, password, codetitle, captcha) VALUES ('" . $code . "', '" .
     $drop . "', '" . $cryptor->encrypt($password) . "', '" . $codetitle . "', '" . $usecaptcha . "')";

$affected_rows = mysql_query($sqlInsert);

$id = mysql_insert_id();	
$result = mysql_query("SELECT * FROM codes WHERE id='".$id."'") or die(mysql_error());
$row = mysql_fetch_array($result);	
$link = $sitename . "show.php?id=" . $row['id'];

include_once 'geshi.php';
$type = ch_gettype(ch_getcodetype($id), false);
$source = ch_formatCodeForDisplaying(ch_getcode($id));

$geshi = new GeSHi($source, $type);
$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
$geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');
$geshi->set_header_type(GESHI_HEADER_DIV);
$geshi->set_tab_width(5);
$geshi->enable_classes();
$geshi->set_overall_id('mycode');
?>

<style type="text/css"><?=$geshi->get_stylesheet()?></style>
<div class="top3"></div>
<center>
	<div class="textbox1">Share URL: <a href="<?=$link?>"><?=$link?></a></div>
</center>
<div class="bottom3"></div>
<br />

<div class='top'></div>   
<center><div class='textbox2' id="snippet"><?=$geshi->parse_code()?></div></center>   
<div class='bottom'></div>

