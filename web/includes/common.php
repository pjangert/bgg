<?php
require_once("debug.php");
class header_item
{
	public $type;
	public $source;
	function header_item($type, $source)
	{
		$this->type = $type;
		$this->source = $source;
	}
}
class display_item
{
	private $type;
	private $name;
	private $length;
	private $min_val;
	private $max_val;
	private $value;
	private $def_value;
	function __get($member)
	{
		return $this->$member;
	}
	function __set($member, $value)
	{
		switch ($member)
		{
			case "type":
				switch($value)
				{
					case "number":
					case "text":
					case "checkbox":
					case "password":
					case "hidden":
						$this->type = $value;
						break;
					default:
						trigger_error("Attempt to access non-existant property", E_USER_WARNING);
						break;
				}
				break;
			case "name":
				if (!is_string($value))
					trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
				else
					$this->$member = $value;
				break;
			case "length":
				if (!is_numeric($value))
					trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
				else
					$this->$member = $value;
				break;
			case "min_val":
				if (!is_numeric($value))
					trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
				if (isset($this->max_val))
					if ($member > $this->max_val)
					trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
				$this->$member = $value;
				break;
			case "max_val":
				if (!is_numeric($value))
					trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
				if (isset($this->min_val))
					if ($member < $this->min_val)
						trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
				$this->$member = $value;
				break;
			case "value":
				if (isset($this->type))
					if ($this->type == "number")
						if (!is_numeric($value) && $value != null)
							trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
						else
							$this->$member = $value;
					else
						if (!is_string($value) && $value != null)
							trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
						else
							$this->$member = $value;
				else
					$this->$member = $value;
				break;
			case "def_value":
				if (isset($this->type))
					if ($this->type == "number")
						if (!is_numeric($value))
							trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
						else
							$this->$member = $value;
					else
						if (!is_string($value))
							trigger_error(var_dump(debug_backtrace()), E_USER_ERROR);
						else
							$this->$member = $value;
				else
					$this->$member = $value;
				break;
			default:
				trigger_error("Attempt to access undefined property: {$member}", E_USER_WARNING);
				break;
		}
	}

	public function show()
	{
		switch ($this->type)
		{
		case "checkbox":
			return "<input type='checkbox' name='{$this->name}' value='{$this->def_value}' " . ($this->value == "Y" ? "checked" : "") . " />";
			break;
		case "text":
			if (!isset($this->def_value))
				if (!isset($this->value) || $this->value == "")
					$this->value = $this->def_value;
			return "<input type='text' name='{$this->name}' maxlength='{$this->length}' " . (isset($this->value) && ($this->value != "") ? "value='{$this->value}'" : "" ) . " />";
			break;
		case "password":
			return "<input type='password' name='{$this->name}' maxlength='{$this->max_val}' />";
			break;
		case "number":
			if (isset($this->def_value))
				if (!isset($this->value) || $this->value == "")
					$this->value = $this->def_value;
			return "<input type='number' name='{$this->name}' min={$this->min_val} max={$this->max_val} " . (isset($this->value) && $this->value != "" ? "value='{$this->value}'" : "" ) . "/>";
			break;
		case "hidden":
			if (!isset($this->value))
				if(isset($this->def_value) && $this->def_value != "")
					$this->value = $this->def_value;
			return "<input type='hidden' name='{$this->name}' value='{$this->value}' />";
			break;
		default:
			return "<p>show not yet implemented for type {$this->type}</p>";
			break;
		}
	}
}
$main_style = new header_item("stylesheet", "styles/main.css");
$nav_style = new header_item("stylesheet", "styles/nav.css");
function start_page($header_info = NULL) 
{ 
	//echo "<html>\n<head>\n<link rel='stylesheet' href='styles/main.css'>\n";
	echo "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='UTF-8'>\n";
	if (isset($header_info))
	{
		if ($DEBUG >= 1) 
			my_show(var_dump($header_info));
		$head_count = 0;
		foreach ($header_info as $header)
		{
			$head_count++;
			if (!isset($header->type))
				echo "Unset header type (count: {$head_count}): " . my_show(var_dump($header));
			elseif ($header->type == "stylesheet")
				echo "<link rel='{$header->type}' href='{$header->source}' />\n";
			elseif ($header->type == "title")
				echo "<title>{$header->source}</title>\n";
			elseif (preg_match('/javascript/', $header->type))
				echo "<script type='{$header->type}' src='{$header->source}'></script>\n";
			elseif (preg_match('/script/', $header->type))
				echo "<script>\n{$header->source}\n</script>\n";
			else
				echo "{$header->source}\n";
		}
	}
	echo "</head>\n<body>\n<div class='page'>\n"; 
}
function end_page ($code = 0)
{
	global $DEBUG, $link;
        if (isset($link)) { ($DEBUG > 2) && my_show(var_dump($link)); $link->close(); }
        echo "</div>\n</body>\n</html>";
        if (isset($code) && $code > 0) { die; }
}
?>
