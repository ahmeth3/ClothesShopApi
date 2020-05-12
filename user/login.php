<?php

require '../connect.php';

$email = ($_GET['email'] !== null && $_GET['email'] != '') ? mysqli_real_escape_string($con, $_GET['email']) : false;

if (!$email) {
    return http_response_code(400);
}

$user = null;

$sql = "SELECT id, email, password FROM users WHERE `email`='{$email}'";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $user['id'] = $row['id'];
        $user['email'] = $row['email'];
        $user['password'] = $row['password'];
        $cr++;
    }

    echo json_encode(['data' => $user]);
} else {
    http_response_code(404);
}
