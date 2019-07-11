<?php
session_start();
if (!$_SESSION['signed_in']) {
    header("Location: /index.php");
    exit;
}
?>