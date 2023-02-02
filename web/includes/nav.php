<?php
require_once("includes/common.php");
if (preg_match("/\/admin\/[a-zA-Z_]*.php$/", $_SERVER['PHP_SELF']))
{
	$base_path="../";
	$admin_path="";
	$admin_section = true;
}
else
{
	$base_path="";
	$admin_path="admin/";
	$admin_section = false;
}
?>
  <nav class='nav'>
    <nav class='navcontent'>
      <ul>
        <li><a href="<?=$base_path?>players.php">Games by Number of Players</a></li>
        <li><a href="<?=$base_path?>loaned_games.php">Games Currently on Loan</a></li>
        <!-- <li><a href="changepw.php">Change Password</a></li> -->
        <hr />
        <li><a href="<?= ($admin_section ? "admin.php" : "admin/") ?>">Admin Home</a></li>
<?php
if (isset($_SESSION['username']))
{
	echo "        <li><ul>\n";
	if ($_SESSION['add_user'] || $_SESSION['edit_user'])
	{
		echo "          <li><a href='{$admin_path}user_maint.php'>User Maintenance</a></li>\n";
	}
	if ($_SESSION['add_game'] || $_SESSION['edit_game'] || $_SESSION['edit_loan'])
	{
		if ($_SESSION['add_game'])
			echo "          <li><a href='{$admin_path}add_bgg_game.php'>BGG Lookup</a></li>\n";
			echo "          <li><a href='{$admin_path}add_bgg_collection.php'>Import BGG Collection</a></li>\n";
			echo "          <li><a href='{$admin_path}export_bgg.php'>Export Collection to BGG</a></li>\n";
		echo "          <li><a href='{$admin_path}game_maint.php'>Game Maintenance</a></li>\n";
		echo "          <li><a href='{$admin_path}exp_maint.php'>Expansion Maintenance</a></li>\n";
	}
	echo "          <li><a href='{$admin_path}changepw.php'>Change Password</a></li>\n";
	echo "        </ul></li>\n";
?>
	<li><a href='<?= $admin_path ?>logout.php'>Log Out</a></li>
      </ul>
<?php
}
?>
    </nav>
  </nav>

