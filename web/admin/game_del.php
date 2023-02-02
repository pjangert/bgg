<?php
if (!isset($_SESSION['username'])) { header("Location: ../"); }
if (!$link) { header("Location: {${urlencode($_SERVER['HTTP_REFERER'])}}"); }
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $link->escape_string($rvalue);}	
if ($_SESSION['edit_game'] ) { do
{

	//my_show(var_dump($_REQUEST));

	$check_query = "select * from game_info where game_id = {$game_id}";
	$exist_check = $link->query($check_query);
	if (!$exist_check)
	{
		$ins_error = $link->error;
		$ins_message = $check_query;
	}
	elseif ($exist_check->num_rows == 0) { $ins_error = "Game " . htmlspecialchars_decode($_REQUEST['game_name']) . " (id: {$_REQUEST['game_id']}) does not exist"; $exist_check->close(); }
	else
	{
		$game_info = $exist_check->fetch_assoc();
		$game_name = $game_info['game_name'];
		$child_query = "select exp_id from expansion_info where parent_id = {$game_id}";
		$child_check = $link->query($child_query);
		$child_counter = 0;
		$children = false;
		if (!$child_check)
		{
			$ins_error = "Error checking child entries";
			$ins_message = $link->error();
			break;
		}
		if ($child_check->num_rows > 0)
		{
			$children = true;
			while ($child_row = $child_check->fetch_assoc())
			{
				if (!$link->query("delete from expansion_info where exp_id = {$child_row['exp_id']}"))
				{
					$ins_error = "Error removing child entry {$child_row['exp_id']}";
					$ins_message = $link->error();
					break 2;
				}
				$child_counter++;
			}
			$child_check->close();
		}
		$query = "delete from game_info where game_id = {$game_id}";
		if (!$link->query($query))
		//if (!my_show($query))
		{
			$ins_message = $link->error;
			$ins_error = "Error deleting {$game_id} <br /> {$query}";
		}
		if (!isset($ins_error) || $ins_error = '')
		{
			unset($ins_error);
			$ins_message = "Successfully deleted game {$game_id} ({$game_name})";
			if ($children)
				$ins_message .= " and {$child_counter} related expansion" . ($child_counter > 1 ? "s" : "");
		}
		$exist_check->close();
	}
} while (0); }
else
	$ins_error = 'Not authorized to delete games';
?>
<!-- HTML in game_del.php -->
