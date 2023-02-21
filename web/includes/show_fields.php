<?php
//global $field_list;
for ($i=0;$i<count($field_list);$i++)
{
	$curr_field = new display_item();
	$curr_field = $field_list[$i];
	$curr_field->value = is_null($curr_array[$curr_field->name]) ? "" : $curr_array[$curr_field->name];
	$check_class = $curr_field->type == "checkbox" ? "" : " class='sans'";
	switch ($curr_field->name)
	{
	case "username":
		echo "        <td class='sans'>{$curr_field->value}</td>\n";
			/*echo "        <td>" . $curr_field->show() . "</td>\n";*/
		break;
	case "password":
		$curr_field->value = "";
		if ($curr_rec_edit)
			echo "        <td>{$curr_field->show()}</td>\n";
		else
			echo "        <td>&nbsp;</td>\n";
		break;
	case "admin":
		if ($curr_rec_edit && $_SESSION['admin'])
			echo "        <td>{$curr_field->show()}</td>\n";
		elseif ($curr_rec_edit)
		{
			$curr_field->type = "hidden";
			echo "        <td>{$curr_field->show()}{$curr_field->value}</td>\n";
		}
		else
			echo "        <td>{$curr_field->value}</td>\n";

		break;
	case "game_id":
	case "exp_id":
	case "parent_id":
	case "child_count":
		break;
	case "lent_to":
		if ($curr_rec_edit && ($_SESSION['edit_game'] || $_SESSION['edit_loan']))
			echo "        <td>{$curr_field->show()}</td>\n";
		else
			echo "        <td class='sans'>{$curr_field->value}</td>\n";
		break;
	case "parent_game":
		if ($curr_rec_edit && $_SESSION['edit_game'])
		{
			echo "        <td class='sans'><a href='javascript:selectParent(\"{$curr_array['exp_id']}\",\"{$curr_array['parent_id']}\")'>{$curr_field->value}</a>";
			echo "<input type='hidden' value={$curr_array['parent_id']} name='parent_id' />";
			echo "</td>\n";
		}
		else
			echo "        <td class='sans'>{$curr_field->value}</td>\n";
		break;
	default:
		if ($curr_rec_edit)
			if (($field_list[0]->name != "game_id" && $field_list[0]->name != "exp_id") || $_SESSION['edit_game'])
				echo "        <td>{$curr_field->show()}</td>\n";
			else
				echo "        <td{$check_class}>{$curr_field->value}</td>\n";
		else
			echo "        <td{$check_class}>{$curr_field->value}</td>\n";
		break;

	}
}
?>
