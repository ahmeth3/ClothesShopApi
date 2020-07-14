<?php
require '../../connect.php';
require "../../vendor/autoload.php";

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
            $data = (array) $decoded;

            $userData = (array) $data['data'];

            $id =  $userData['id'];

            // Extract the data.

            // Validate.
            if (
                trim($request->data->name) === '' || trim($request->data->surname) === '' ||
                trim($request->data->phone) === '' || trim($request->data->address) === '' ||
                trim($request->data->city) === '' || trim($request->data->zipCode) === '' ||
                trim($request->data->country) === ''
            ) {
                return http_response_code(400);
            }

            // Sanitize.
            $name = mysqli_real_escape_string($con, trim($request->data->name));
            $surname = mysqli_real_escape_string($con, trim($request->data->surname));
            $phone = mysqli_real_escape_string($con, trim($request->data->phone));
            $address = mysqli_real_escape_string($con, trim($request->data->address));
            $city = mysqli_real_escape_string($con, trim($request->data->city));
            $zipCode = mysqli_real_escape_string($con, trim($request->data->zipCode));
            $country = mysqli_real_escape_string($con, trim($request->data->country));

            echo $name . $surname;

            // Store.
            $sql = "UPDATE `users` SET `name`='$name',`surname`='$surname', `phone`='$phone', `address`='$address', `city`='$city', `zipCode`='$zipCode', `country`='$country' WHERE `id` = '{$id}' LIMIT 1";

            if (mysqli_query($con, $sql)) {
                http_response_code(201);
            } else {
                http_response_code(422);
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
