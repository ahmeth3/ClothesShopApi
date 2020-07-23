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
            $data = (array) $decoded;

            $userData = (array) $data['data'];

            $id =  $userData['id'];

            $orders = [];
            $sql = "SELECT * FROM userorders WHERE `buyerId` = '{$id}'";

            if ($result = mysqli_query($con, $sql)) {
                $cr = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $orders[$cr]['id'] = $row['id'];
                    $orders[$cr]['buyerId'] = $row['buyerId'];
                    $orders[$cr]['status']  = $row['status'];
                    $orders[$cr]['price'] = $row['price'];
                    $orders[$cr]['date'] = $row['date'];
                    $orders[$cr]['orderedProducts'] = $row['orderedProducts'];
                    $orders[$cr]['name'] = $row['name'];
                    $orders[$cr]['surname'] = $row['surname'];
                    $orders[$cr]['phone'] = $row['phone'];
                    $orders[$cr]['address'] = $row['address'];
                    $orders[$cr]['city'] = $row['city'];
                    $orders[$cr]['zipCode'] = $row['zipCode'];
                    $orders[$cr]['country'] = $row['country'];



                    $products = [];

                    $str = explode(',', $row['orderedProducts']);
                    array_pop($str);

                    $i = 0;
                    foreach ($str as $value) {
                        $prodId = $str[$i];

                        $sqll = "SELECT * FROM userproducts WHERE `id` = '{$prodId}'";

                        if ($resultt = mysqli_query($con, $sqll)) {
                            while ($roww = mysqli_fetch_assoc($resultt)) {
                                $products[$i]['id']    = $roww['id'];
                                $products[$i]['sellerId']    = $roww['sellerId'];
                                $products[$i]['name'] = $roww['name'];
                                $products[$i]['price'] = $roww['price'];
                                $products[$i]['category'] = $roww['category'];
                                $products[$i]['gender'] = $roww['gender'];
                                $products[$i]['color'] = $roww['color'];
                                $products[$i]['size'] = $roww['size'];
                                $products[$i]['caption'] = $roww['caption'];
                                $products[$i]['composition'] = $roww['composition'];
                                $products[$i]['picUrl'] = 'http://localhost/ClothesShopApi/userProduct/images/' . $roww['picName'];
                            }
                        } else {
                            http_response_code(404);
                        }

                        $i++;
                    }
                    $orders[$cr]['products'] = $products;
                    $cr++;
                }

                echo json_encode(['data' => $orders]);
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
}
