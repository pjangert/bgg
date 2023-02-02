<?php
$field_info = $item_list->fetch_fields();
$field_ctr = 0;
for ($i=0;$i<$item_list->field_count;$i++)
{
	$field_type = $field_info[$i]->type;
	$field_flags = $field_info[$i]->flags;
	$field_length = $field_info[$i]->max_length;
	$curr_item = new display_item();
	$curr_item->name = $field_info[$i]->name;
	if ($field_type == MYSQLI_TYPE_STRING && $field_length == 1)
	{
		$curr_item->type = "checkbox";
		$curr_item->def_value = "Y";
		$curr_item->value = "N";
	}
	else if ($field_type == MYSQLI_TYPE_VAR_STRING)
	{
		if ($field_info[$i]->orgname == "password")
		{
			$curr_item->type = "password";
			$curr_item->max_val = 16;
		}
		else
		{
			$curr_item->type = "text";
			//my_show(var_dump($field_length));
			$curr_item->length = $field_length;
		}
	}
	else if ($field_type == MYSQLI_TYPE_TINY || $field_type == MYSQLI_TYPE_SHORT || $field_type == MYSQLI_TYPE_INT24 || $field_type == MYSQLI_TYPE_LONG || $field_type == MYSQLI_TYPE_LONGLONG )
	{
		$curr_item->type = "number";
		switch ($field_type)
		{
		case MYSQLI_TYPE_TINY:
			$curr_item->min_val = 0;
			$curr_item->max_val = 255;
			break;
		case MYSQLI_TYPE_SHORT:
			$curr_item->min_val = 0;
			$curr_item->max_val = 65535;
			break;
		case MYSQLI_TYPE_INT24:
			$curr_item->min_val = 0;
			$curr_item->max_val = 16777215;
			break;
		case MYSQLI_TYPE_LONG:
			$curr_item->min_val = 0;
			$curr_item->max_val = 4294967295;
			break;
		case MYSQLI_TYPE_LONGLONG:
			$curr_item->min_val = 0;
			$curr_item->max_val = 18446744073709551615;
			break;
		}
	}
	$field_list[$i] = $curr_item;
}
?>
