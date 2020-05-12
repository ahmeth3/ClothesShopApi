<?php
require '../connect.php';

// Get the posted data.
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);

    // Validate.
    if ((int) $request->data->id < 1 || trim($request->data->email) == '' || trim($request->data->password) == '') {
        return http_response_code(400);
    }

    // Sanitize.
    $id    = mysqli_real_escape_string($con, (int) $request->data->id);
    $email = mysqli_real_escape_string($con, trim($request->data->email));
    $password = mysqli_real_escape_string($con, trim($request->data->password));

    // Update.
    $sql = "UPDATE `users` SET `email`='$email',`password`='$password' WHERE `id` = '{$id}' LIMIT 1";

    if (mysqli_query($con, $sql)) {
        http_response_code(200);
    } else {
        return http_response_code(422);
    }
}
