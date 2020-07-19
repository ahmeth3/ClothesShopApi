<?php

require '../connect.php';

// Extract, validate and sanitize the id.
$id = ($_GET['id'] !== null && (int) $_GET['id'] > 0) ? mysqli_real_escape_string($con, (int) $_GET['id']) : false;

if (!$id) {
    return http_response_code(400);
}

$product;
$sql = "SELECT * FROM products WHERE `id` ='{$id}' LIMIT 1";

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
    $sql = "DELETE FROM `products` WHERE `id` ='{$id}' LIMIT 1";

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
