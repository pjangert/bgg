<?php
require_once ("includes/verify.php");
require_once ("includes/common.php");
require_once ("includes/dbinfo.php");
$scripts = "function setUpdate(GID)
{
	document.change.game_id.value = GID;
	document.change.act.value = 'edit';
	document.change.submit();
}
function setUpdate2(GID)
{
	document.change.game_id.value = GID;
	document.change.act.value = 'update';
	document.change.submit();
}
function setDelete(GID,game,exp)
{
	if (exp == 'true')
	{
		var result = confirm('\"' + game + '\" has expansions - delete game *and* expansions?');
	}
	else
		var result = confirm('Delete \"' + game + '\"?');
	if (result == false)
		return false;
	document.change.game_id.value = GID;
	document.change.act.value = 'delete';
	document.change.submit();
}
function addGameName()
{
	if (document.add.game_name.value.length > 0 )
		if (document.add.min_players.value > 0)
			if (document.add.max_players.value > 0)
				document.add.submit();
			else
				alert ('Max players is not optional');
		else
			alert ('Max players is not optional');
	else
		alert ('Game name cannot be blank');
}
";
$title = new header_item("title","Game Maintenance");
$script_head = new header_item("script", $scripts);
start_page(array($title, $main_style, $nav_style, $script_head));
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
{
	echo "<h1>Error connecting to database:</h1>\n<p>{$link->error}</p>\n";
	end_page(1);
}
$Edit = "N";

if (isset($_REQUEST['game_name']) || isset($_REQUEST['game_id']))
{
	if ($_REQUEST['act'] == "add" && $_SESSION['add_game'])
		include "game_add.php";
	elseif ($_REQUEST['act'] == "update" && ($_SESSION['edit_game'] || $_SESSION['edit_loan']))
		include "game_upd.php";
	elseif ($_REQUEST['act'] == "edit" && ($_SESSION['edit_game'] || $_SESSION['edit_loan']))
	{
		$Edit = "Y";
		$game_id = $_REQUEST['game_id'];
	}
	elseif ($_REQUEST['act'] == "delete" && ($_SESSION['edit_game']))
		include "game_del.php";
}
echo "<div class='container'>\n";
include_once "includes/nav.php";
?>
  <div class='content'>
    <h1>Game Library Game Admin</h1>
    <hr />
    <p>Logged in as <?=$_SESSION['username']?></p>
<?php
if (isset($ins_error) && $ins_error != "")
	echo ("    <p class='error'>{$ins_error}</p>\n");
if (isset($ins_message) && $ins_message != "")
	echo ("    <p class='error'>{$ins_message}</p>\n");
?>
    <br />
    <table class='fixed-size'>
      <tr>
        <th>&nbsp;</th><th style='max-width: 100px'>&nbsp;</th><th>BGG ID</th><th colspan=2>Players</th><th>&nbsp;</th>
      </tr>
      <tr>
        <th>&nbsp;</th><th>Game Name</th><th>(opt)</th><th>Min</th><th>Max</th><th>Lent To</th>
      </tr>
<?php
if ($Edit == "N")
	//$game_query = "select * from game_info order by game_name";
	$game_query = "select a.*, count(b.exp_id) as child_count from game_info a left join expansion_info b on a.game_id = b.parent_id group by a.game_id order by game_name";
else
	//$game_query = "select * from game_info where game_id != {$game_id} order by game_name";
	$game_query = "select a.*, count(b.exp_id) as child_count from game_info a left join expansion_info b on a.game_id = b.parent_id where game_id != {$game_id} group by a.game_id order by game_name";
$item_list = $link->query($game_query);
if (!$item_list)
{
        echo "<p class='error'>Query failed {$link->error}</p>";
        end_page(1);
}
include "includes/set_fields.php";
if ($_SESSION['add_game'])
{
?>
      <form name='add' method='post' action='<?=$_SERVER['PHP_SELF']?>'>
        <tr>
	  <td class='sans'><input name='act' type='hidden' value='add' /><a href='javascript:addUN()'>Add Game</a></td>
<?php
	foreach ($field_list as $curr_field)
		if ($curr_field->name != "game_id" && $curr_field->name != "child_count")
			echo "        <td>{$curr_field->show()}</td>\n";
?>
        </tr>
      </form>
<?php } ?>
		
      <form name='change' method='post' action='<?=$_SERVER['PHP_SELF']?>'>
      <input type='hidden' name='game_id' />
      <input type='hidden' name='act' />
<?php
if (($_SESSION['edit_game'] || $_SESSION['edit_loan']) && $Edit == 'Y' )
{
	$single_query = "select a.*, count(b.exp_id) as child_count from game_info a left join expansion_info b on a.game_id = b.parent_id where game_id = {$game_id}";
	$single_list = $link->query($single_query);
	if ($single_list)
	{
		$curr_array = $single_list->fetch_assoc();
		echo "      <tr>\n";
		echo "        <td class='sans'><a href='javascript:setUpdate2(\"{$curr_array['game_id']}\");'>Update Game</a></td>\n";
		$curr_rec_edit = true;
		include "includes/show_fields.php";
		$single_list->close();
		echo "      </tr>\n";
		echo "      <tr>\n";
		echo "        <td class='sans'><a href={$_SERVER['PHP_SELF']}>Cancel Update</a></td>\n";
		echo "      </tr>\n";
	}
}
$curr_rec_edit = false;
while ($curr_array = $item_list->fetch_assoc())
{
	if ($item_list->num_rows == 0 && $curr_array['game_name'] == null)
	{
		echo "breaking";
		break;
	}
	echo "      <tr>\n";
	if ($_SESSION['edit_game'] || $_SESSION['edit_loan'])
	{
		echo "        <td class='sans'><a href='javascript:setUpdate(\"{$curr_array['game_id']}\");'>Edit Game</a>\n";
		if ($_SESSION['edit_game'])
			echo "         <br /><a href='javascript:setDelete(\"{$curr_array['game_id']}\",\"" . htmlspecialchars($curr_array['game_name'],ENT_QUOTES) . "\",\"" . ($curr_array['child_count'] > 0 ? "true" : "false") . "\");'>Delete Game</a>\n";
		echo "        </td>\n";
	}
	else
		echo "        <td>&nbsp;</td>\n";
	include "includes/show_fields.php";
	echo "      </tr>\n";
}
$item_list->close();
echo "    </table>\n";
echo "  <br />\n";
echo "  </div>\n";
echo "</div>\n";
end_page();
?>
