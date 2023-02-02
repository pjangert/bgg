<?php
if (!isset($_SESSION['username'])) { header("Location: ../"); }
if (!$link) { header("Location: {${urlencode($_SERVER['HTTP_REFERER'])}}"); }
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $link->escape_string($rvalue);}	
if ($_SESSION['edit_game'] )
{
	//my_show(var_dump($_REQUEST));

	$check_query = "select * from expansion_info where exp_id = {$exp_id}";
	$exist_check = $link->query($check_query);
	if (!$exist_check)
	{
		$ins_error = $link->error;
		$ins_message = $check_query;
	}
	elseif ($exist_check->num_rows == 0) { $ins_error = "Expansion {$_REQUEST['exp_name']} (id: {$_REQUEST['exp_id']}) does not exist"; $exist_check->close(); }
	else
	{
		$query = "update expansion_info set ";
		$all_fields = $exist_check->fetch_fields();
		$field_count = $exist_check->field_count;
		$counter = 0;
		$game = $exist_check->fetch_assoc();
		foreach($all_fields as $field_info)
		{
			//my_show(var_dump($field_info));
			$ffield = $field_info->name;
			if ($ffield != "exp_id")
			{
				if ($$ffield != $game[$ffield])
				{
					$counter++;
					//my_show($$ffield . " - " . $game[$ffield]);
					if ($$ffield == null)
						$query .= "{$ffield} = NULL";
					else
						$query .= " {$ffield} = '{$$ffield}'";
					$query .= ",";
				}
			}
		}
		if ($counter > 0)
		{
			$query = substr($query, 0, strlen($query)-1);
			$query .= " where exp_id = {$exp_id}";
			if (!$link->query($query))
			//if (!my_show($query))
			{
				$ins_error = $link->error;
				$ins_message = "Error updating {$exp_id} <br /> {$query}";
			}
		}
		if (!isset($ins_error) || $ins_error = '')
		{
			unset($ins_error);
			if ($counter > 0)
				$ins_message = "Successfully updated expansion {$exp_id} ({$_REQUEST['exp_name']})";
			else
				$ins_message = "No updates needed for expansion {$exp_id}";
		}
		$exist_check->close();
	}
}
elseif ($_SESSION['edit_loan'])
{
	$check_query = "select * from expansion_info where exp_id = {$exp_id}";
	$exist_check = $link->query($check_query);
	if (!$exist_check)
	{
		$ins_error = $link->error;
		$ins_message = $check_query . "  " . my_show(var_dump($_REQUEST));
		//my_show(var_dump($_REQUEST));
	}
	elseif ($exist_check->num_rows == 0) { $game = $exist_check->fetch_assoc(); $ins_error = "Expansion {$game['exp_name']} (id: {$_REQUEST['exp_id']}) does not exist"; $exist_check->close(); }
	else
	{
		$game = $exist_check->fetch_assoc();
		if ($game['lent_to'] != $loan_to)
		{
			$query = "update expansion_info set lent_to = " . ($loan_to == null ? "NULL" : '{$loan_to}') . " where exp_id = {$exp_id}";
			//$query = "update game_info set lent_to = '{$loan_to}' where exp_id = {$exp_id}";
			if (!$link->query($query))
			{
				$ins_error = $link->error;
				$ins_message = "Error updating {$exp_id} <br /> {$query}";
			}
			if (!isset($ins_error) || $ins_error = '')
			{
				unset($ins_error);
				$ins_message = "Successfully updated expansion {$exp_id} ({$game['exp_name']})";
			}
		}
		else
			$ins_message = "No updates needed for expansion {$exp_id}";
		$exist_check->close();
	}
}
else
	$ins_error = 'Not authorized to update games';
?>
<!-- HTML in exp_upd.php -->
