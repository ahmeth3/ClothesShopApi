<?php

class UploadImage
{
    public $image;
    public $image_name = '';

    function __construct($img)
    {
        $this->image = $img;
    }

    function uploadImage()
    {
        $upload_dir = 'images/';

        $avatar_name = $_FILES["avatar"]["name"];
        $avatar_tmp_name = $_FILES["avatar"]["tmp_name"];
        $error = $_FILES["avatar"]["error"];

        $random_name = rand(1000, 1000000) . "-" . $avatar_name;
        $upload_name = $upload_dir . strtolower($random_name);
        $upload_name = preg_replace('/\s+/', '-', $upload_name);

        $this->image_name = $random_name;

        if (move_uploaded_file($avatar_tmp_name, $upload_name))
            return true;
        else return false;
    }
}
