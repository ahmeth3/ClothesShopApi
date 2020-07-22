<?php

require '../connect.php';

// Extract, validate and sanitize the id.
$id = ($_GET['id'] !== null && (int) $_GET['id'] > 0) ? mysqli_real_escape_string($con, (int) $_GET['id']) : false;

if (!$id) {
    return http_response_code(400);
}

// Delete.
$sql = "SELECT email FROM `users` WHERE `id` ='{$id}' LIMIT 1";

$email;

if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $email = $row['email'];
    }

    echo json_encode(['data' => $email]);
} else {
    http_response_code(404);
}
