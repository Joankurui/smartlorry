<?php
session_start();

function ensure_logged_in() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
}
?>
