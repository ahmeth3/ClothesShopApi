<?php

// db credentials
define('DB_HOST', 'nr84dudlpkazpylz.chr7pe7iynqr.eu-west-1.rds.amazonaws.com');
define('DB_USER', 'nbajl4e8ci1lzk1u');
define('DB_PASS', 'khqhw7to4mppoycm');
define('DB_NAME', 'm7lxkixjjjeifscr');

// Connect with the database.
function connect()
{
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (mysqli_connect_errno($connect)) {
        die("Failed to connect:" . mysqli_connect_error());
    }

    mysqli_set_charset($connect, "utf8");

    return $connect;
}

$con = connect();
