<?php
ini_set('session.dc.max_lifetime', 3600);
session_set_cookie_params(3600);
session_start();
require_once "includes/debug.php";
if (isset($_SESSION['username'])) 
{
  $redir = $_SESSION['resume'] ?? "admin.php";
  unset($_SESSION['resume']);
  header("Location: {$redir}"); 
}
else
{
  if (isset($_REQUEST['login']))
  {
    require_once "includes/dbinfo.php";
    $username = $_REQUEST['login'];
    $link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
    if (!$link) { echo "<h1>Error occurred: {$link->error}</h1>"; debug_show(1, "<p>host: {$db_host}, login {$ro_login}, DB, {$gamedb}, port {$db_port}</p>"); die; }
    $query = "select * from user_login where username='{$username}'";
    $result = $link->query($query);
    if (!$result) { echo "<h1>Error in query: {$link->error}</h1>"; die; }
    $pass_test = $result->fetch_assoc();
    if (MD5($_REQUEST['creds']) == $pass_test['password'])
    {
      foreach ($pass_test as $field_name => $field_value)
      {
        if ($field_name == "username") {$_SESSION[$field_name] = $field_value; }
        elseif ($field_name != "password") {$_SESSION[$field_name] = ($field_value == "Y") ; }
      }
      $_SESSION['failed'] = false;
    }
    else { $_SESSION['failed'] = true; }
    $result->close();
    $link->close();
    unset($link);
    if ($_SESSION['failed'] == false)
    {
      $redir = (empty($_SESSION['resume'])) ? "admin.php" : $_SESSION['resume'];
      unset($_SESSION['resume']);
      header("Location: {$redir}");
    }
  }
}
$verify = "function verify()
{
  if (document.getElementById('username').value == '')
  {
     alert('Username cannot be blank');
     return false;
  }
  if (document.getElementById('password').value == '')
  {
     alert('Password cannot be blank');
     return false;
  }
  return true;
}";
require_once "includes/common.php";
$title = new header_item("title","Admin Login");
$verify_function = new header_item("script", $verify);
start_page(array($title, $main_style, $nav_style, $verify_function));
?>
<div class='container'>
<?php include_once "includes/nav.php"; ?>
<div class='content'>
<h1>Game Library Admin Login</h1>
<br>
<form name='Login' action='<?= $_SERVER['PHP_SELF']?>' method='POST' onSubmit='verify();'>
<table>
<?php
if (isset($_SESSION['failed']) && $_SESSION['failed'])
{
  $_SESSION['failed'] = false;
  echo "<tr>\n  <td colspan='2' class='red'>Invalid Username or Password</td>\n</tr>\n";
}
debug_show(2, var_export($_SESSION, true));
?>
<tr>
  <td>Username</td>
  <td><input name='login' id='username' type='text' size=16 maxlength=16 /></td>
</tr>
<tr>
  <td>Password</td>
  <td><input name='creds' id='password' type='password' size=16 maxlength=16 /></td>
</tr>
<tr>
  <td colspan='2'><input type='submit' value='Login' /></td>
</tr>
</table>
</form>
</div>
</div>
<?php
end_page();
?>
