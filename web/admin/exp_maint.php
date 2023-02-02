<?php
require_once ("includes/verify.php");
require_once ("includes/common.php");
require_once ("includes/dbinfo.php");
$scripts = "function setUpdate(GID)
{
	document.change.exp_id.value = GID;
	document.change.act.value = 'edit';
	document.change.submit();
}
function setDelete(GID,game)
{
	var result = confirm('Delete \"' + game + '\"?');
	if (result == false) {return false;}
	document.change.exp_id.value = GID;
	document.change.act.value = 'delete';
	document.change.submit();
}
function setUpdate2(GID)
{
	document.change.exp_id.value = GID;
	document.change.act.value = 'update';
	document.change.submit();
}
function addGameName()
{
	if (document.add.exp_name.value.length > 0 )
		document.add.submit();
	else
		alert ('Game name cannot be blank');
}
function selectParent(EID,PID)
{
	document.change.action='select_parent.php';
	document.change.exp_id.value = EID;
	document.change.act.value='update';
	document.change.submit();
}
";
$title = new header_item("title","Expansion Maintenance");
$script_head = new header_item("script", $scripts);
start_page(array($title, $main_style, $nav_style, $script_head));
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
{
	echo "<h1>Error connecting to database:</h1>\n<p>{$link->error}</p>\n";
	end_page(1);
}
$Edit = "N";

if (isset($_REQUEST['exp_name']) || isset($_REQUEST['exp_id']))
{
	if ($_REQUEST['act'] == "add" && $_SESSION['add_game'])
		include "exp_add.php";
	elseif ($_REQUEST['act'] == "update" && ($_SESSION['edit_game'] || $_SESSION['edit_loan']))
		include "exp_upd.php";
	elseif ($_REQUEST['act'] == "delete" && $_SESSION['edit_game'] )
		include "exp_del.php";
	elseif ($_REQUEST['act'] == "edit" && ($_SESSION['edit_game'] || $_SESSION['edit_loan']))
	{
		$Edit = "Y";
		$exp_id = $_REQUEST['exp_id'];
	}
}
echo "<div class='container'>\n";
include_once "includes/nav.php";
?>
  <div class='content'>
    <h1>Game Library Expansion Admin</h1>
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
        <th>&nbsp;</th><th style='max-width: 100px'>&nbsp;</th><th>BGG ID</th><th colspan=2>Players Override</th><th>&nbsp;</th><th>Parent Game</th>
      </tr>
      <tr>
        <th>&nbsp;</th><th>Expansion Name</th><th>(opt)</th><th>Min</th><th>Max</th><th>Lent To</th><th>(click to change)</th>
      </tr>
<?php
if ($Edit == "N")
	$game_query = "select a.*,b.game_name as parent_game from expansion_info a, game_info b where a.parent_id = b.game_id order by exp_name";
else
	$game_query = "select a.*,b.game_name as parent_game from expansion_info a, game_info b where exp_id != {$exp_id} and a.parent_id = b.game_id order by exp_name";
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
	  <td class='sans'><input name='act' type='hidden' value='add' /><a href='javascript:addUN()'>Add Expansion</a></td>
<?php
	foreach ($field_list as $curr_field)
		if ($curr_field->name != "exp_id" && $curr_field->name != "parent_id")
			if ($curr_field->name != "parent_game")
				echo "        <td>{$curr_field->show()}</td>\n";
			else
				echo "        <td class='sans'>Selected on next page</td>\n";
?>
        </tr>
      </form>
<?php } ?>
		
      <form name='change' method='post' action='<?=$_SERVER['PHP_SELF']?>'>
      <input type='hidden' name='exp_id' />
      <input type='hidden' name='act' />
<?php
if (($_SESSION['edit_game'] || $_SESSION['edit_loan']) && $Edit == 'Y' )
{
	$single_query = "select a.*,b.game_name as parent_game from expansion_info a, game_info b where exp_id = {$exp_id} and a.parent_id = b.game_id";
	$single_list = $link->query($single_query);
	if ($single_list)
	{
		$curr_array = $single_list->fetch_assoc();
		echo "      <tr>\n";
		echo "        <td class='sans'><a href='javascript:setUpdate2(\"{$curr_array['exp_id']}\");'>Update Expansion</a></td>\n";
		$curr_rec_edit = true;
		include "includes/show_fields.php";
		$single_list->close();
		echo "        <input type='hidden' name='parent_id' value='{$curr_array['parent_id']}' />\n";
		echo "      </tr>\n";
		echo "      <tr>\n";
		echo "        <td class='sans'><a href={$_SERVER['PHP_SELF']}>Cancel Update</a></td>\n";
		echo "      </tr>\n";
	}
}
$curr_rec_edit = false;
while ($curr_array = $item_list->fetch_assoc())
{
	echo "      <tr>\n";
	if ($_SESSION['edit_game'] || $_SESSION['edit_loan'])
	{
		echo "        <td class='sans'><a href='javascript:setUpdate(\"{$curr_array['exp_id']}\");'>Edit Expansion</a>\n";
		if ($_SESSION['edit_game'])
			echo "        <br /><a href='javascript:setDelete(\"{$curr_array['exp_id']}\",\"{$curr_array['exp_name']}\");'>Delete Expansion</a>\n";
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
