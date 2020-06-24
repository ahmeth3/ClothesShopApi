<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");

require_once './uploadImage.php';
require '../connect.php';

$response = array();
$imageUpload = 1; //imageUpload error handler
$image_name;

if ($_FILES['avatar']) {
    $upload = new UploadImage($_FILES['avatar']);

    if ($upload->uploadImage()) {
        $imageUpload = 1;
        $image_name = $upload->image_name;
        $response = array(
            "status" => "success",
            "error" => false,
            "message" => "Image uploaded successfully",
        );
    } else {
        $imageUpload = 2;
        $response = array(
            "status" => "error",
            "error" => true,
            "message" => "Error uploading the image!"
        );
    }
} else {
    $imageUpload = 3;
    $response = array(
        "status" => "error",
        "error" => true,
        "message" => "No file was sent!"
    );
}

//If image was uploaded succesfully sql query to create the product will execute
if ($imageUpload === 1) {
    if (isset($_POST['data']) && !empty($_POST['data'])) {
        // Extract the data.
        $data = json_decode($_POST['data']);

        // Validate.
        if (
            trim($data->name) === '' || !ctype_digit($data->price) ||
            trim($data->category) === '' || trim($data->gender) === ''
            || trim($data->color) === '' || trim($data->size) === ''
            || trim($data->caption) === '' || trim($data->composition) === ''
        ) {
            return http_response_code(400);
        }

        // Sanitize.
        $name = mysqli_real_escape_string($con, trim($data->name));
        $price = mysqli_real_escape_string($con, (int) ($data->price));
        $category = mysqli_real_escape_string($con, trim($data->category));
        $gender = mysqli_real_escape_string($con, trim($data->gender));
        $color = mysqli_real_escape_string($con, trim($data->color));
        $size = mysqli_real_escape_string($con, trim($data->size));
        $caption = mysqli_real_escape_string($con, trim($data->caption));
        $composition = mysqli_real_escape_string($con, trim($data->composition));
        $imageUrl = mysqli_real_escape_string($con, trim($image_name));


        // Store.
        $sql = "INSERT INTO `products`(`name`,`price`,`category`,`gender`,`color`,`size`, `caption`, `composition`,`picName`) VALUES ('{$name}','{$price}', '{$category}','{$gender}', '{$color}','{$size}', '{$caption}','{$composition}','{$imageUrl}')";

        if (mysqli_query($con, $sql)) {
            http_response_code(201);
            $product = [
                'name' => $name,
                'price' => $price,
                'category' => $category,
                'gender' => $gender,
                'color' => $color,
                'size' => $size,
                'caption' => $caption,
                'composition' => $composition,
                'id'    => mysqli_insert_id($con)
            ];
            echo json_encode(['data' => $product]);
        } else {
            http_response_code(422);
        }
    }
} else {
    echo json_encode($response);
}
