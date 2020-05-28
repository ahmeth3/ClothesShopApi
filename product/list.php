<?php

require '../connect.php';

$products = [];
$sql = "SELECT * FROM products";

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
        $cr++;
    }

    echo json_encode(['data' => $products]);
} else {
    http_response_code(404);
}
