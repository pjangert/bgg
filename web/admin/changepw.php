<?php
require_once "includes/verify.php";
require_once "includes/common.php";
require_once "includes/dbinfo.php";
$verify = " function verifyPW()
{
	if (document.change.creds.value.length > 0)
		document.change.submit();
	else
		alert ('Password may not be blank');
}
";
$title = new header_item("title","Password Change");
$verify_function = new header_item("script",$verify);
start_page(array($title, $main_style, $nav_style, $verify_function));
if (isset($_REQUEST['creds']))
{
	$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
	if (!$link)
	{
		echo "<h1>Could not connect to database</h1>\n<p class='error'>{$link->error}</p>\n";
		//end_page(1);
	}
	$password = MD5($_REQUEST['creds']);
	$upd_user = $link->escape_string($_SESSION['username']);
	$query = "update user_login set password = '{$password}' where username = '{$upd_user}'";
	if (!$link->real_query($query))
		$ins_error = "Error: {$link->error}";
	else
	{
		$ins_message = "Password successfully changed";
		header("Location: admin.php");
	}
	$link->close();
}
echo "  <div class='container'>\n";
include_once "includes/nav.php";
?>
    <div class='content'>
    <h1>Password change for user <?= $_SESSION['username']?></h1>
<?php
if (isset($ins_error))
	echo "      <p class='error'>{$ins_error}</p>\n";
if (isset($ins_message))
	echo "      <p class='error'>{$ins_message}</p>\n";
?>
      <hr />
      <table>
        <tr>
          <th>New Password</th>
        </tr>
        <tr>
          <td>
            <form name='change' method='post' action='<?= $_SERVER['PHP_SELF'] ?>'>
              <input name='creds' type='password' maxlength=16>
              <input type='button' value='Submit' onClick='verifyPW();'>
            </form>
          </td>
        </tr>
      </table>
    </div>
  </div>
<?php
end_page();
?>
