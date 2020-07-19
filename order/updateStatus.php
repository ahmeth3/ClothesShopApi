<?php

require '../connect.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

// Get the posted data.
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $secret_key = "weryiwehjflmklewury2894732u";

    if (
        trim($request->token) === ''
    ) {
        return http_response_code(400);
    }

    $token = mysqli_real_escape_string($con, trim($request->token));

    $jwt = $token;

    if ($jwt) {

        try {
            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

            // Access is granted. Add code of the operation here 

            if (
                trim($request->data->id) === '' || trim($request->data->status) === ''

            ) {
                return http_response_code(400);
            }

            // Sanitize.
            $id = mysqli_real_escape_string($con, trim($request->data->id));
            $status = mysqli_real_escape_string($con, trim($request->data->status));

            // Update.
            $sql = "UPDATE `orders` SET `status`='$status' WHERE `id` = '{$id}' LIMIT 1";

            if (mysqli_query($con, $sql)) {
                http_response_code(200);
            } else {
                return http_response_code(422);
            }
        } catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    }
}
