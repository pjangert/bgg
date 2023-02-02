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
		$exp_info = $exist_check->fetch_assoc();
		$exp_name = $exp_info['exp_name'];
		$query = "delete from expansion_info where exp_id = {$exp_id}";
		if (!$link->query($query))
		//if (!my_show($query))
		{
			$ins_message = $link->error;
			$ins_error = "Error deleting {$exp_id} <br /> {$query}";
		}
		if (!isset($ins_error) || $ins_error = '')
		{
			unset($ins_error);
			$ins_message = "Successfully deleted expansion {$exp_id} ({$exp_name})";
		}
		$exist_check->close();
	}
}
else
	$ins_error = 'Not authorized to delete games';
?>
<!-- HTML in game_del.php -->
