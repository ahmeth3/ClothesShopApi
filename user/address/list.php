<?php
require '../../connect.php';
require "../../vendor/autoload.php";

use \Firebase\JWT\JWT;

$token = ($_GET['token'] !== null && $_GET['token'] !== '') ? mysqli_real_escape_string($con, $_GET['token']) : false;

if (!$token) {
    return http_response_code(400);
}

$secret_key = "weryiwehjflmklewury2894732u";
$jwt = null;

$data = json_decode(file_get_contents("php://input"));

$jwt = $token;

if ($jwt) {

    try {

        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 
        $data = (array) $decoded;

        $userData = (array) $data['data'];

        $id =  $userData['id'];

        $address = null;
        $sql = "SELECT * FROM users WHERE `id`='{$id}' LIMIT 1";

        if ($result = mysqli_query($con, $sql)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $address['name'] = $row['name'];
                $address['surname'] = $row['surname'];
                $address['phone'] = $row['phone'];
                $address['address'] = $row['address'];
                $address['city'] = $row['city'];
                $address['zipCode'] = $row['zipCode'];
                $address['country'] = $row['country'];
            }

            echo json_encode(['data' => $address]);
        } else {
            http_response_code(404);
        }

        // echo json_encode(array(
        //     "message" => "Access granted:"
        // ));
    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
