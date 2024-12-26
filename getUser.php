<?php
session_start();
echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
?>

