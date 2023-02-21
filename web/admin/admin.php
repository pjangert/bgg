<?php
require_once ("includes/verify.php");
require_once ("includes/common.php");
include_once ("includes/dbinfo.php");
$title = new header_item("title","Game Library Admin");
start_page(array($title, $main_style, $nav_style));
if (isset($_SESSION['ins_error'])) {$ins_error = $_SESSION['ins_error']; unset($_SESSION['ins_error']); }
if ($ins_error == "") { unset($ins_error); }
if (isset($_SESSION['ins_message'])) {$ins_message = $_SESSION['ins_message']; unset($_SESSION['ins_message']); }
if ($ins_message == "") { unset($ins_message); }
echo "<div class='page'>\n<div class='container'>\n";
include_once "includes/nav.php";
echo "<div class='content'>
<h1>Game Library Admin for {$gamedb}</h1>
<hr />\n";
if (isset($ins_error) && $ins_error != "") {echo "<p class='error'>{$ins_error}</p>\n";}
if (isset($ins_message) && $ins_message != "") {echo "<p class='error'>{$ins_message}</p>\n";}
echo "<h3>Database Options for {$_SESSION['username']}</h3>
";
debug_show(1, var_export($_SESSION, true));
$add_game = $_SESSION['add_game'];
$edit_game = $_SESSION['edit_game'];
$edit_loan = $_SESSION['edit_loan'];
$add_user = $_SESSION['add_user'];
$edit_user = $_SESSION['edit_user'];
if ($add_game || $edit_game || $edit_loan)
{
	if ($add_user) { echo "  <p><a href='add_bgg_game.php'>Add Games from BGG Lookup</a></p>\n"; }
	echo "  <p><a href='game_maint.php'>" . ($add_game ? "Add" : "");
	if ($edit_game || $edit_loan) { echo ($add_game ? ' / ' : '')." Update"; }
	echo " Games</p>\n";
	echo "  <p><a href='exp_maint.php'>" . ($add_game ? "Add" : "");
	if ($edit_game || $edit_loan) { echo ($add_game ? ' / ' : '')." Update"; }
	echo " Expansions</p>\n";
}
if ($add_user || $edit_user)
{
	echo "  <p><a href='user_maint.php'>" . ($add_user ? 'Add' : "");
	if ($edit_user) { echo ($add_user ? " / " : "") . "Update"; }
	echo " Users</p>\n";
}
echo "  <p><a href='logout.php'>Logout</a></p>\n";
echo "</div>
</div>
</div>\n";
end_page();
