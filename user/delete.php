<?php

require '../connect.php';

// Extract, validate and sanitize the id.
$id = ($_GET['id'] !== null && (int) $_GET['id'] > 0) ? mysqli_real_escape_string($con, (int) $_GET['id']) : false;

if (!$id) {
  return http_response_code(400);
} 

// Delete.
$sql = "DELETE FROM `users` WHERE `id` ='{$id}' LIMIT 1";

if (mysqli_query($con, $sql)) {
  http_response_code(200);
} else {
  return http_response_code(422);
}
