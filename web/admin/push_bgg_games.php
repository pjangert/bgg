<?php
require_once("includes/verify.php");
require_once("includes/common.php");
require_once("includes/dbinfo.php");

$base_site = "https://www.boardgamegeek.com";
$login_site = $base_site . "/login/api/v1";
$cookiefile = "cookie{$_SESSION['username']}.txt";

if (!file_exists($cookiefile) || (isset($_REQUEST['password']) && $_REQUEST['password'] != ""))
{
	//echo "Password '{$_REQUEST['password']}'\n";
	$cred_array=array('credentials'=>array('username'=>$_REQUEST['username'],'password'=>$_REQUEST['password']));
	$curl_conn = curl_init();
	curl_setopt($curl_conn, CURLOPT_URL, $login_site);
	curl_setopt($curl_conn, CURLOPT_POST, true);
	curl_setopt($curl_conn, CURLOPT_POSTFIELDS, json_encode($cred_array));
	curl_setopt($curl_conn, CURLOPT_COOKIEJAR, $cookiefile);
	curl_setopt($curl_conn, CURLOPT_HEADER, true);
	curl_setopt($curl_conn, CURLINFO_HEADER_OUT, true);
	curl_setopt($curl_conn, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl_conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_conn, CURLOPT_FOLLOWLOCATION, 1);
	$return_info = curl_exec($curl_conn);
//var_dump($return_info);
	$http_response=curl_getinfo($curl_conn, CURLINFO_RESPONSE_CODE);
	curl_close($curl_conn);
	//echo "<p class='error'>return: $return_info , response code: $http_response</p>";
}

// Check that we got good information
$file_cont = file($cookiefile);
//var_dump($file_cont);
$found = 0;
foreach ($file_cont as $sub => $line)
{
	if ($found == 1) { break; }
	$line_array = explode("\t",$line);
	if (!strpos($line_array[0],"boardgamegeek.com"))
		continue;
	foreach ($line_array as $sub1 => $curr_value)
	{
		if ($curr_value == "bggpassword")
		{
			$hash_pass = $line_array[$sub1 + 1];
			$found = 1;
			break 2;
		}
	}
}
if ($found == 0)
{
	echo "<p class='error'>Error getting BGG login information</p>\n";
	die;
}

$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
{
	echo "<p class='error'>Error logging into {$gamedb} database</p>\n";
	die;
}
$game_add_url = $base_site . "/geekcollection.php";

$ID_list = $link->query("select bgg_id, game_name as item_name from game_info where bgg_id is not null union select bgg_id, exp_name from expansion_info where bgg_id is not null");
//$ID_list = $link->query("select bgg_id, game_name as item_name from game_info where bgg_id is not null and bgg_id in (68448, 111661, 109451) union select bgg_id, exp_name from expansion_info where bgg_id is not null and bgg_id in (68448, 111661, 109451)");
$error_count = 0;
while ($curr_row = $ID_list->fetch_assoc())
{
	$retry = false;
	//var_dump($curr_row);
	retry:
	$curl_conn = curl_init();
	//curl_setopt($curl_conn, CURLOPT_URL, $game_add_url);
	//curl_setopt($curl_conn, CURLOPT_POST, 1);
	curl_setopt($curl_conn, CURLOPT_POST, true);
	curl_setopt($curl_conn, CURLOPT_COOKIEJAR, $cookiefile);
	curl_setopt($curl_conn, CURLOPT_COOKIEFILE, $cookiefile);
	curl_setopt($curl_conn, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_conn, CURLINFO_HEADER_OUT, true);
	$bgg_id = $curr_row['bgg_id'];
	$item_name = $curr_row['item_name'];
	if ($bgg_id != "")
	{
	curl_setopt($curl_conn, CURLOPT_URL, $game_add_url . "?ajax=1&action=checkowned&objecttype=thing&objectid={$bgg_id}");
		//$post_fields = "ajax=1&action=checkowned&objecttype=thing&objectid={$bgg_id}";
		//$post2 = array('ajax' => 1, 'action' => 'checkowned', 'objecttype' => 'thing', 'objectid' => $bgg_id );
		//curl_setopt($curl_conn, CURLOPT_POSTFIELDS, $post_fields);
		//curl_setopt($curl_conn, CURLOPT_POSTFIELDS, http_build_query($post2));
		$return_value = curl_exec($curl_conn);
			//echo "Debug $item_name results1:";
			//$deb_info=curl_getinfo($curl_conn, CURLINFO_HEADER_OUT);
			//var_dump($deb_info);
			//$deb_info=curl_getinfo($curl_conn, CURLINFO_HTTP_CODE);
			//var_dump($deb_info);
			//$deb_info=curl_getinfo($curl_conn, CURLINFO_EFFECTIVE_URL);
			//var_dump($deb_info);
			//var_dump($return_value);
		if ($return_value === false)
		{
			echo "<p class='error'>Error checking item {$item_name} ({$bgg_id})</p><p>Error:" . curl_error($curl_conn) . "</p>\n";
			echo "<p>{$game_add_url}&{$post_fields}</p>\n";
			$error_count++;
		}
		else
		{
			//echo "Debug $item_name results1 from  ${game_add_url}?ajax=1&action=checkowned&objecttype=thing&objectid={$bgg_id}:";
			//var_dump($return_value);
			if ($return_value == "1")
			{
				//echo "<p>{$item_name} ({$bgg_id}) already listed as owned on BGG</p>\n";
			}
			else if ($return_value == "")
			{
				curl_setopt($curl_conn, CURLOPT_POSTFIELDS, "ajax=1&action=additem&objecttype=thing&objectid={$bgg_id}&addowned=true&force=true");
				$return = curl_exec($curl_conn);
				if (!$return === false)
				{
					echo "<p class='error'>Error adding {$bgg_id}: {$item_name} to collection</p>\n<p class='error'>ajax=1&action=additem&objecttype=thing&objectid={$bgg_id}&addowned=true&force=true</p>";
					$error_count++;
				}
				else
				{
					echo "<p>Added {$item_name} ({$bgg_id}) to collection</p>\n";
					echo "<hr />\n";
				}
			}
			else
			{
				if (!$retry)
				{
					$retry = true;
					curl_close($curl_conn);
					sleep(1);
					goto retry;
				}
				else
				{
					echo "<p>{$item_name} ({$bgg_id})</p><p>{$return_value}</p>\n";
					$error_count++;
					echo "<hr />\n";
				}
			}
		}
	}
	curl_close($curl_conn);
}
echo "<br /><h4>{$error_count} error" . ($error_count != 1 ? "s" : "") . " encountered</h4>\n";

?>
