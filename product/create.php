<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");

require_once './uploadImage.php';
require '../connect.php';

$response = array();
$imageUpload = 1; //imageUpload error handler
$imageUrl;

if ($_FILES['avatar']) {
    $upload = new UploadImage($_FILES['avatar']);

    if ($upload->uploadImage()) {
        $imageUpload = 1;
        $imageUrl = $upload->imageUrl;
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
        $imageUrl = mysqli_real_escape_string($con, trim($imageUrl));


        // Store.
        $sql = "INSERT INTO `products`(`name`,`price`,`category`,`gender`,`color`,`size`, `picUrl`) VALUES ('{$name}','{$price}', '{$category}','{$gender}', '{$color}','{$size}', '{$imageUrl}')";

        if (mysqli_query($con, $sql)) {
            http_response_code(201);
            $product = [
                'name' => $name,
                'price' => $price,
                'category' => $category,
                'gender' => $gender,
                'color' => $color,
                'size' => $size,
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


// $response = array();
// $upload_dir = 'images/';
// $server_url = 'http://localhost/ClothesShopApi';

// if ($_FILES['avatar']) {
//     $avatar_name = $_FILES["avatar"]["name"];
//     $avatar_tmp_name = $_FILES["avatar"]["tmp_name"];
//     $error = $_FILES["avatar"]["error"];


//     if ($error > 0) {
//         $response = array(
//             "status" => "error",
//             "error" => true,
//             "message" => "Error uploading the file!"
//         );
//     } else {
//         $random_name = rand(1000, 1000000) . "-" . $avatar_name;
//         $upload_name = $upload_dir . strtolower($random_name);
//         $upload_name = preg_replace('/\s+/', '-', $upload_name);

//         if (move_uploaded_file($avatar_tmp_name, $upload_name)) {
//             $response = array(
//                 "status" => "success",
//                 "error" => false,
//                 "message" => "File uploaded successfully",
//                 "url" => $server_url . "/" . $upload_name
//             );
//         } else {
//             $response = array(
//                 "status" => "error",
//                 "error" => true,
//                 "message" => "Error uploading the file!"
//             );
//         }
//     }
// } else {
//     $response = array(
//         "status" => "error",
//         "error" => true,
//         "message" => "No file was sent!"
//     );
// }

// echo json_encode($response);
