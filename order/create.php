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
            if ($jwt != 'none') {
                $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

                // Access is granted. Add code of the operation here 
                $data = (array) $decoded;

                $userData = (array) $data['data'];

                $id =  $userData['id'];
            } else $id = 'guest';

            // Extract the data.

            // Validate.
            if (
                trim($request->data->price) === '' ||
                trim($request->data->name) === '' || trim($request->data->surname) === '' ||
                trim($request->data->phone) === '' || trim($request->data->address) === '' ||
                trim($request->data->city) === '' || trim($request->data->zipCode) === '' ||
                trim($request->data->country) === ''
            ) {
                return http_response_code(400);
            }

            // Sanitize.
            $status = mysqli_real_escape_string($con, trim($request->data->status));
            $price = mysqli_real_escape_string($con, trim($request->data->price));
            $date = mysqli_real_escape_string($con, trim($request->data->date));
            $name = mysqli_real_escape_string($con, trim($request->data->name));
            $surname = mysqli_real_escape_string($con, trim($request->data->surname));
            $phone = mysqli_real_escape_string($con, trim($request->data->phone));
            $address = mysqli_real_escape_string($con, trim($request->data->address));
            $city = mysqli_real_escape_string($con, trim($request->data->city));
            $zipCode = mysqli_real_escape_string($con, trim($request->data->zipCode));
            $country = mysqli_real_escape_string($con, trim($request->data->country));

            // Store.
            $sql = "INSERT INTO `orders`(`buyerId`,`status`,`price`, `date`, `name`, `surname`, `phone`, `address`, `city`, `zipCode`, `country`) VALUES ('{$id}','{$status}','{$price}','{$date}','{$name}','{$surname}','{$phone}','{$address}','{$city}','{$zipCode}','{$country}')";

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
