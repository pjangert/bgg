<?php
  session_start();
  if (!isset($_SESSION['username']))
  {
    $_SESSION['resume'] = $_SERVER['REQUEST_URI'];
    header('Location: index.php');
  }
?>
