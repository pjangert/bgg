<?php
if (!isset($_SESSION['username'])) { header("Location: ../"); }
if (!$link) { header("Location: {${urlencode($_SERVER['HTTP_REFERER'])}}"); }
if ($_SESSION['edit_user'])
{
	//my_show(var_dump($_REQUEST));
	foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $link->escape_string($rvalue);}	
	$password = MD5($_REQUEST['password']);

	$check_query = "select * from user_login where username = '{$username}'";
	$exist_check = $link->query($check_query);
	if (!$exist_check)
	{
		$ins_error = $link->error;
		$ins_message = $check_query;
	}
	elseif ($exist_check->num_rows == 0) { $ins_error = "User {$username} does not exist"; $exist_check->close(); }
	else
	{
		$query = "update user_login set";
		$all_fields = $exist_check->fetch_fields();
		$field_count = $exist_check->field_count;
		$counter = 1;
		foreach($all_fields as $field_info)
		{
			//my_show(var_dump($field_info));
			$ffield = $field_info->name;
			if ($ffield != "username")
			{
				if ($ffield != "password" || ($ffield == "password" && isset($_REQUEST['password']) && (strlen($_REQUEST['password'])) > 0 ))
				{
					if ($$ffield == null)
						$$ffield = "N";
					$query .= " {$ffield} = '{$$ffield}'";
					if ($counter < $field_count)
						$query .= ",";
				}
			}
			$counter++;
		}
		$query .= " where username = '{$username}'";
		if (!$link->real_query($query))
		//if (!my_show($query))
		{
			$ins_error = $link->error;
			$ins_message = "Error updating {$username}";
		}
		if (!isset($ins_error) || $ins_error = '')
		{
			unset($ins_error);
			$ins_message = "Successfully updated user {$username}";
		}
		$exist_check->close();
	}
}
else
	$ins_error = 'Not authorized to update users';
?>
<!-- HTML in user_upd.php -->
