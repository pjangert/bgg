<?php
$DEBUG = 0;
require_once "includes/verify.php";
require_once "includes/common.php";
require_once "includes/dbinfo.php";
$scripts = "function setUpdate(UN)
{
	document.change.username.value = UN;
	document.change.act.value = 'edit';
	document.change.submit();
}
function setUpdate2(UN)
{
	document.change.username.value = UN;
	document.change.act.value = 'update';
	document.change.submit();
}
function addUN()
{
	if (document.add.username.value.length > 0 )
		if (document.add.password.value.length > 0)
			document.add.submit();
		else
			alert ('You must enter a password');
	else
		alert ('Username cannot be blank');
}
function verifyPW()
{
	if (document.changepw.password.value.length > 0)
		document.changepw.submit();
	else
		alert ('Password cannot be blank');
}";

$title = new header_item("title","User Maintenance");
$script_head = new header_item("script", $scripts);
start_page(array($title, $main_style, $nav_style, $script_head));
$link = new mysqli('localhost',$rw_login,$rw_pw,$gamedb);
if (!$link)
{
	echo "<h1>Error connecting to database:</h1>\n<p>{$link->error}</p>\n";
	end_page(1);
}
$Edit = "N";

if (isset($_REQUEST['username']))
{
	if ($_REQUEST['username'] == $_SESSION['username'] && !$_SESSION['admin'])
		$ins_error = "Not authorized to update own record!";
	elseif ($_REQUEST['act'] == "add" && $_SESSION['add_user'])
		include "user_add.php";
	elseif ($_REQUEST['act'] == "update" && $_SESSION['edit_user'])
		include "user_upd.php";
	elseif ($_REQUEST['act'] == "edit" && $_SESSION['edit_user'])
	{
		$Edit = "Y";
		$username = $link->escape_string($_REQUEST['username']);
		$username_rec = $_REQUEST['username'];
	}
}
echo "<div class='container'>\n";
include_once "includes/nav.php";
?>
  <div class='content'>
    <h1>Game Library User Admin</h1>
    <hr />
    <p>Logged in as <?=$_SESSION['username']?></p>
<?php
if (isset($ins_error))
	echo ("    <p class='error'>{$ins_error}</p>\n");
if (isset($ins_message))
	echo ("    <p class='error'>{$ins_message}</p>\n");
if ($DEBUG > 0) {my_show(var_dump($_REQUEST));}
$user_query = "select * from user_login";
$item_list = $link->query($user_query);
if (!$item_list)
{
	echo "<p class='error'>Query failed {$link->error}</p>";
	end_page(1);
}
include "includes/set_fields.php";
?>
    <br />
    <table>
      <tr>
        <th>&nbsp;</th><th>&nbsp;</th><th>Password</th><th colspan=2>Games</th><th>Loan</th><th colspan=2>Users</th><th>System</th>
      </tr>
      <tr>
        <th>&nbsp;</th><th>Username</th><th>(blank to not change)</th><th>Add</th><th>Edit</th><th>Edit</th><th>Add</th><th>Edit</th><th>Admin</th>
      </tr>
<?php
if ($_SESSION['add_user'])
{
?>
      <form name='add' method='post' action=<?=$_SERVER['PHP_SELF']?>>
      <tr>
        <td class='sans'><input name='act' type='hidden' value='add' /><a href='javascript:addUN()'>Add User</a></td>
<?php
	foreach ($field_list as $curr_field)
	{
		//my_show(var_dump($curr_field));
		if ($curr_field->name != "admin" || $_SESSION['admin'])
			echo "        <td>{$curr_field->show()}</td>\n";
		else
		{
			//my_show("In admin of add");
			$curr_field->type = "hidden";
			$curr_field->def_value = "N";
			echo "        <td>{$curr_field->show()}N</td>\n";
		}
	}
?>
      </tr>
      </form>
<?php } ?>
      <form name='change' method='post' action=<?=$_SERVER['PHP_SELF']?>>
      <input type='hidden' name='username' />
      <input type='hidden' name='act' />
<?php
while ($curr_array = $item_list->fetch_assoc())
{
	echo "      <tr>\n";
	$curr_rec_edit = ($Edit == "Y" && $curr_array['username'] == $username_rec)? true : false;
	if ($curr_rec_edit)
		echo "        <td class='sans'><a href='javascript:setUpdate2(\"{$username_rec}\");'>Update User</a></td>\n";
	else if ($_SESSION['admin'] || $curr_array['username'] != $_SESSION['username'])
		echo "        <td class='sans'><a href='javascript:setUpdate(\"{$curr_array['username']}\");'>Edit User</a></td>\n";
	else
		echo "        <td>&nbsp;</td>\n";
	include "includes/show_fields.php";
	echo "      </tr>\n";
	if ($curr_rec_edit)
	{
?>
      <tr>
        <td class='sans'><a href='<?=$_SERVER['PHP_SELF']?>'>Cancel Update</a></td>
      </tr>
<?php
	}
}
$item_list->free();
echo "    </table>\n";
echo "  </div>\n";
echo "</div>\n";
end_page();
?>

