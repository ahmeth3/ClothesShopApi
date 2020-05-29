<?php

require '../connect.php';

// Getthe posted data
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Extract the data
    $request = json_decode($postdata);

    // Validate
    if (trim($request->data->gender) === '' || trim($request->data->category) === '' || trim($request->data->color) === '') {
        return http_response_code(400);
    }

    // Sanitize
    $gender = mysqli_real_escape_string($con, trim($request->data->gender));
    $category = mysqli_real_escape_string($con, trim($request->data->category));
    $color = mysqli_real_escape_string($con, trim($request->data->color));

    $products = [];
    $sql;
    if ($category === "View All") {
        $sql = "SELECT * FROM products WHERE `gender` = '{$gender}' AND `color` LIKE '%$color%'";
    } else {
        $sql = "SELECT * FROM products WHERE `gender` = '{$gender}' AND `category` = '{$category}' AND `color` LIKE '%$color%'";
    }

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
