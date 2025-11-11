<?php
define('MB', 1048576);

function imageUpload($imageRequest, $uploadDir = "../upload/")
{
    global $msgError;
    $msgError = "";

    if (
        !isset($_FILES[$imageRequest]) ||
        $_FILES[$imageRequest]['error'] !== UPLOAD_ERR_OK
    ) {
        $msgError = "NO_FILE";
        return "fail";
    }

    $imagename = $_FILES[$imageRequest]['name'];
    $imagetmp  = $_FILES[$imageRequest]['tmp_name'];
    $imagesize = $_FILES[$imageRequest]['size'];

    $allowedExt = ["jpg", "jpeg", "png", "gif"];
    $ext = strtolower(pathinfo($imagename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        $msgError = "EXT";
        return "fail";
    }

    if ($imagesize > 2 * MB) {
        $msgError = "SIZE";
        return "fail";
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newName = uniqid("img_", true) . "." . $ext;
    $destination = rtrim($uploadDir, "/") . "/" . $newName;

    if (move_uploaded_file($imagetmp, $destination)) {
        return $newName;
    } else {
        $msgError = "UPLOAD_FAIL";
        return "fail";
    }
}

function deleteFile($dir, $imagename)
{
    $path = rtrim($dir, "/") . "/" . $imagename;
    if (file_exists($path) && is_file($path)) {
        unlink($path);
    }
}
?>