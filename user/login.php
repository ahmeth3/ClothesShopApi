<?php

require '../connect.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

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

    $user = null;

    $sql = "SELECT id, email, password FROM users WHERE `email`='{$email}'";

    if ($result = mysqli_query($con, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $user['id'] = $row['id'];
            $user['email'] = $row['email'];
            $user['password'] = $row['password'];
        }
        if ($user == null) {
            http_response_code(400);
            echo json_encode(
                array(
                    "message" => "Email address is wrong.",
                )
            );
        }

        if (password_verify($password, $user['password']) && $user != null) {
            $secret_key = "weryiwehjflmklewury2894732u";
            $issuer_claim = "Ahmet Halilovic"; // this can be the servername
            $audience_claim = "Idk";
            $issuedat_claim = time(); // issued at
            $notbefore_claim = $issuedat_claim + 1; //not before in seconds
            $expire_claim = $issuedat_claim + 3600; // expire time in seconds
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "id" => $user['id'],
                    "email" => $email
                )
            );

            http_response_code(200);

            $jwt = JWT::encode($token, $secret_key);
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "email" => $email,
                    "expireAt" => $expire_claim
                )
            );
        } else if ($user != null) {
            http_response_code(400);
            echo json_encode(
                array(
                    "message" => "Wrong password.",
                )
            );
        }

        // echo json_encode(['data' => $user]);
    } else {
        http_response_code(400);
    }
}
