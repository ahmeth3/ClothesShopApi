<?php
require '../connect.php';

// Get the posted data.
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Extract the data.
    $request = json_decode($postdata);


    // Validate.
    if (trim($request->data->email) === '' || trim($request->data->password) === '') {
        return http_response_code(400);
    }

    // Sanitize.
    $email = mysqli_real_escape_string($con, trim($request->data->email));
    $password = mysqli_real_escape_string($con, trim($request->data->password));


    // Store.
    $sql = "INSERT INTO `users`(`id`,`email`,`password`) VALUES (null,'{$email}','{$password}')";

    if (mysqli_query($con, $sql)) {
        http_response_code(201);
        $user = [
            'email' => $email,
            'password' => $password,
            'id'    => mysqli_insert_id($con)
        ];
        echo json_encode(['data' => $user]);
    } else {
        http_response_code(422);
    }
}
