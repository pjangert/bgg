<?php
if (!isset($_SESSION['username'])) { header("Location: ../"); }
if (!$link) { header("Location: {${urlencode($_SERVER['HTTP_REFERER'])}}"); }
global $ins_error, $ins_message;
if ($_SESSION['add_game'])
{
	foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $link->escape_string($rvalue); if ($DEBUG>2) { my_show($rfield . ":\n" . $$rfield) ;}}
	$game_name = trim($game_name);

	$check_query = "select * from game_info where game_name = '{$game_name}'";
	$exist_check = $link->query($check_query);
	if (!$exist_check)
	{
		$ins_error = $link->error;
		$ins_message = $check_query;
	}
	elseif ($exist_check->num_rows >0) { $ins_error = "Game {$game_name} already exists"; $exist_check->close(); }
	else
	{
		$query = "insert into game_info values (";
		$all_fields = $exist_check->fetch_fields();
		$field_count = $link->field_count;
		$counter = 1;
		foreach($all_fields as $field_info)
		{
			$ffield = $field_info->name;
			if ($$ffield == null)
				$query .= "null";
			//my_show($ffield . " - " . var_dump($$ffield));
			else
				$query .= "'{$$ffield}'";
			if ($counter++ < $field_count)
				$query .= ",";
		}
		$query .= ")";
		if (!$link->real_query($query))
		{
			$ins_error = $link->error;
			$ins_message = "Error adding {$game_name}";
			$ins_message .= " using {$query}";
		}
		if ($ins_error = '')
		{
			unset($ins_error);
			$ins_message = "Successfully added game {$game_name}";
		}
		$exist_check->close();
	}
}
else
	$ins_error = 'Not authorized to add games';
