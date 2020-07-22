<?php

require_once './uploadImage.php';
require '../connect.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

$response = array();
$imageUpload = 1; //imageUpload error handler
$image_name;

$jwt = null;
$secret_key = "weryiwehjflmklewury2894732u";
$userId;

if (isset($_POST['token']) && !empty($_POST['token'])) {
    $jwt = $_POST['token'];
} else {
    return http_response_code(400);
}

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 
        $data = (array) $decoded;

        $userData = (array) $data['data'];

        $userId = $userData['id'];

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

                echo $price;
                echo $category;
                echo $gender;
                echo $color;
                echo $size;
                echo $caption;
                echo $composition;
                echo $imageUrl;

                // Store.
                $sql = "INSERT INTO `userproducts`(`sellerId`,`name`,`price`,`category`,`gender`,`color`,`size`,`caption`,`composition`,`picName`) VALUES ('{$userId}','{$name}','{$price}', '{$category}','{$gender}', '{$color}','{$size}', '{$caption}','{$composition}','{$imageUrl}')";

                if (mysqli_query($con, $sql)) {
                    http_response_code(201);
                    $product = [
                        'sellerId' => $userId,
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
                    echo 'Muhamed';
                }
            } else {
                echo 'Nema fajlova';
            }
        } else {
            echo json_encode($response);
        }
    } catch (Exception $e) {
        http_response_code(401);

        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
