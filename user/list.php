<?php

require '../connect.php';

$users = [];
$sql = "SELECT id, email, password FROM users";

if ($result = mysqli_query($con, $sql)) {
    $cr = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $users[$cr]['id']    = $row['id'];
        $users[$cr]['email'] = $row['email'];
        $users[$cr]['password'] = $row['password'];
        $cr++;
    }

    echo json_encode(['data' => $users]);
} else {
    http_response_code(404);
}
