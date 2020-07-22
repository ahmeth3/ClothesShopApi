<?php

require '../connect.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

// Extract, validate and sanitize the id.
$id = ($_GET['id'] !== null && (int) $_GET['id'] > 0) ? mysqli_real_escape_string($con, (int) $_GET['id']) : false;
$token = ($_GET['token'] !== null && $_GET['token'] != '') ? mysqli_real_escape_string($con, $_GET['token']) : false;

if (!$id || !$token) {
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

        $product;
        $sql = "SELECT * FROM userproducts WHERE `id` ='{$id}' LIMIT 1";

        if ($result = mysqli_query($con, $sql)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $product['id']    = $row['id'];
                $product['name'] = $row['name'];
                $product['price'] = $row['price'];
                $product['category'] = $row['category'];
                $product['gender'] = $row['gender'];
                $product['color'] = $row['color'];
                $product['size'] = $row['size'];
                $product['caption'] = $row['caption'];
                $product['composition'] = $row['composition'];
                $product['picUrl'] = $row['picName'];
                
            }

            // Delete.
            $sql = "DELETE FROM `userproducts` WHERE `id` ='{$id}' LIMIT 1";

            if (mysqli_query($con, $sql)) {
                $path = 'images/' . $product['picUrl'];
                unlink($path);

                $path = 'product-details-images/' . $product['picUrl'];
                array_map('unlink', glob($path . '/*.*'));
                rmdir($path);

                http_response_code(200);
            } else {
                return http_response_code(422);
            }
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
