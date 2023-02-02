<?php
if (!isset($_SESSION['username'])) { header("Location: ../"); }
if (!$link) { header("Location: {${urlencode($_SERVER['HTTP_REFERER'])}}"); }
if ($_SESSION['add_user'])
{
	foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $rvalue; } // my_show($rfield . ":\n" . $$rfield);}
	$password = MD5($_REQUEST['password']);
	$db_username = $link->escape_string($username);

	$check_query = "select * from user_login where username = '{$db_username}'";
	$exist_check = $link->query($check_query);
	if (!$exist_check)
	{
		$ins_error = $link->error;
		$ins_message = $check_query;
	}
	elseif ($exist_check->num_rows >0) { $ins_error = "User {$username} already exists"; $exist_check->close(); }
	else
	{
		$query = "insert into user_login values (";
		$all_fields = $exist_check->fetch_fields();
		$field_count = $link->field_count;
		$counter = 1;
		foreach($all_fields as $field_info)
		{
			$ffield = $field_info->name;
			$$ffield = $link->escape_string($$ffield);
			if ($$ffield == null)
				$$ffield = "N";
			//my_show($ffield . " - " . var_dump($$ffield));
			$query .= "'{$$ffield}'";
			if ($counter++ < $field_count)
				$query .= ",";
		}
		$query .= ")";
		if (!$link->real_query($query))
		{
			$ins_error = $link->error;
			$ins_message = "Error adding {$username}";
			$ins_message .= " using {$query}";
		}
		if ($ins_error = '')
		{
			unset($ins_error);
			$ins_message = "Successfully added user {$username}";
		}
		$exist_check->close();
	}
}
else
	$ins_error = 'Not authorized to add users';
