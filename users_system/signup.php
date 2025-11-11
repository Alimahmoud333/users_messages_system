<?php
session_start();
include "database/config.php";
include "functions.php";
include "constant/message.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = sha1($_POST["password"]);

    // Upload image using the helper function
    $image = imageUpload("image", "uploads/");

    // Check if upload failed
    if ($image === "fail") {
        global $msgError;
        if ($msgError == "NO_FILE") $error = "Please upload an image.";
        elseif ($msgError == "EXT") $error = "Invalid file type. Only JPG, PNG, GIF allowed.";
        elseif ($msgError == "SIZE") $error = "File too large. Max 2MB allowed.";
        elseif ($msgError == "UPLOAD_FAIL") $error = "Failed to upload image.";
    } else {
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already exists.";
            deleteFile("uploads/", $image);
        } else {
          
            $insert = $con->prepare("INSERT INTO users (name, email, password, image, role) VALUES (?, ?, ?, ?, 'user')");
            $insert->execute([$name, $email, $password, $image]);

            $_SESSION["user_id"] = $con->lastInsertId();
            $_SESSION["role"] = "user";
            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="js/bootstrap.bundle.js"></script>
</head>

<body class="bg-secondary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4 shadow-lg bg-secondary border-3 border-white">
                    <h4 class="mb-3 text-center text-white">Sign Up</h4>

                    <?php if (!empty($error)) showMessage($error, "danger"); ?>

                    <form method="post" enctype="multipart/form-data">
                        <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Password"
                            required>
                        <input type="file" name="image" class="form-control mb-3" accept="image/*" required>
                        <button class="btn btn-primary w-100">Sign Up</button>
                        <p class="text-center mt-3">Already have an account? <a href="index.php"
                                class="text-white text-decoration-none">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php include "constant/footer.php";?>