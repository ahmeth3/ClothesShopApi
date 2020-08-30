<?php

require '../connect.php';

// Getthe posted data
$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    // Extract the data
    $request = json_decode($postdata);

    // Validate
    if (
        trim($request->data->gender) === '' || trim($request->data->category) === '' || trim($request->data->color) === ''
        || trim($request->data->size) === ''
    ) {
        return http_response_code(400);
    }

    // Sanitize
    $gender = mysqli_real_escape_string($con, trim($request->data->gender));
    $category = mysqli_real_escape_string($con, trim($request->data->category));
    $color = mysqli_real_escape_string($con, trim($request->data->color));
    $size = mysqli_real_escape_string($con, trim($request->data->size));

    $products = [];
    $sql;

    /* 
        This if statements check which filters are active and based on their values
        appropriate sql statements is processed.
    */
    if ($category === 'View All' && $color === 'None' && $size === 'None' && $gender === 'None') //000
        $sql = "SELECT * FROM userproducts";
    elseif ($category === 'View All' && $color === 'None' && $size != 'None' && $gender === 'None') //001
        $sql = "SELECT * FROM userproducts WHERE `size` LIKE '%$size%'";
    elseif ($category === 'View All' && $color != 'None' && $size === 'None' && $gender === 'None') //010
        $sql = "SELECT * FROM userproducts WHERE `color` LIKE '%$color%'";
    else if ($category === 'View All' && $color === 'None' && $size === 'None') //000
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}'";
    elseif ($category === 'View All' && $color === 'None' && $size != 'None') //001
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `size` LIKE '%$size%'";
    elseif ($category === 'View All' && $color != 'None' && $size === 'None') //010
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `color` LIKE '%$color%'";
    elseif ($category === 'View All' && $color != 'None' && $size != 'None') //011
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `color` LIKE '%$color%' AND `size` LIKE '%$size%'";
    elseif ($category != 'View All' && $color === 'None' && $size === 'None') //100
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `category` = '{$category}'";
    elseif ($category != 'View All' && $color === 'None' && $size != 'None') //101
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `category` = '{$category}' AND `size` LIKE '%$size%'";
    elseif ($category != 'View All' && $color != 'None' && $size === 'None') //110
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `category` = '{$category}' AND `color` LIKE '%$color%'";
    else { //111
        $sql = "SELECT * FROM userproducts WHERE `gender` = '{$gender}' AND `category` = '{$category}' AND `color` LIKE '%$color%' AND `size` LIKE '%$size%'";
    }

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
            $products[$cr]['picUrl'] = 'https://ep-web-shop.herokuapp.com/userProduct /images/' . $row['picName'];

            $cr++;
        }

        echo json_encode(['data' => $products]);
    } else {
        http_response_code(404);
    }
}
