<?php

require '../connect.php';

// Getthe posted data
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Extract the data
    $request = json_decode($postdata);

    // Validate
    if (trim($request->data->gender) === '' || trim($request->data->category) === '') {
        return http_response_code(400);
    }

    // Sanitize
    $gender = mysqli_real_escape_string($con, trim($request->data->gender));
    $category = mysqli_real_escape_string($con, trim($request->data->category));

    $products = [];
    $sql = "SELECT * FROM products WHERE `gender` = '{$gender}' AND `category` = '{$category}'";

    if ($result = mysqli_query($con, $sql)) {
        $cr = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $products[$cr]['id']    = $row['id'];
            $products[$cr]['name'] = $row['name'];
            $products[$cr]['price'] = $row['price'];
            $products[$cr]['category'] = $row['category'];
            $products[$cr]['gender'] = $row['gender'];
            $products[$cr]['color'] = $row['color'];
            $products[$cr]['size'] = $row['size'];
            $products[$cr]['picUrl'] = 'http://localhost/ClothesShopApi/product/images/' . $row['picName'];

            $cr++;
        }

        echo json_encode(['data' => $products]);
    } else {
        http_response_code(404);
    }
}
