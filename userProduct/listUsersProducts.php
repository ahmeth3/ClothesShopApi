<?php

require '../connect.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

// Extract, validate and sanitize the id.
$token = ($_GET['token'] !== null && $_GET['token'] != '') ? mysqli_real_escape_string($con, $_GET['token']) : false;

if (!$token) {
    return http_response_code(400);
}

$secret_key = "weryiwehjflmklewury2894732u";

$jwt = $token;

if ($jwt) {

    try {

        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 
        $data = (array) $decoded;

        $userData = (array) $data['data'];

        $userId = $userData['id'];

        $products = [];
        $sql = "SELECT * FROM userproducts WHERE `sellerid` ='{$userId}'";

        if ($result = mysqli_query($con, $sql)) {
            $cr = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $products[$cr]['id']    = $row['id'];
                $products[$cr]['sellerId'] = $row['sellerId'];
                $products[$cr]['name'] = $row['name'];
                $products[$cr]['price'] = $row['price'];
                $products[$cr]['category'] = $row['category'];
                $products[$cr]['gender'] = $row['gender'];
                $products[$cr]['color'] = $row['color'];
                $products[$cr]['size'] = $row['size'];
                $products[$cr]['caption'] = $row['caption'];
                $products[$cr]['composition'] = $row['composition'];
                $products[$cr]['picUrl'] = 'http://localhost/ClothesShopApi/userProduct /images/' . $row['picName'];

                $cr++;
            }
            echo json_encode(['data' => $products]);
        } else {
            http_response_code(404);
        }
    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
