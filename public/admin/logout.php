<?php
require_once '../app/config/database.php';
require_once '../app/includes/functions.php';

start_session();

if (!is_logged_in()) {
    redirect('/admin/login.php');
}

session_destroy();
redirect('/admin/login.php');
?>
