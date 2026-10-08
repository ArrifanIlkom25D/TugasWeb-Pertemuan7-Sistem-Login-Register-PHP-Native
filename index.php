<?php
require_once __DIR__ . '/config.php';
redirect(isLoggedIn() ? 'dashboard.php' : 'login.php');
