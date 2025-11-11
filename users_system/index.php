<?php
session_start();
include "database/config.php";
include "constant/message.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = sha1($_POST["password"]);

    $stmt = $con->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["username"] = $user["name"];
        if ($user["role"] == "admin") {
            header("Location: admins/dashboard.php");
        } else {
            header("Location: users/home.php");
        }
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="js/bootstrap.bundle.js"></script>
</head>

<body class="bg-secondary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4 shadow-lg bg-secondary border-3 border-white">
                    <h4 class="mb-3 text-center text-white">Login</h4>
                    <?php if (!empty($error)) showMessage($error, "danger"); ?>
                    <form method="post">
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Password"
                            required>
                        <button class="btn btn-primary w-100">Login</button>
                        <p class="text-center mt-3">Don't have an account? <a href="signup.php"
                                class="text-white text-decoration-none">Sign Up</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php include "constant/footer.php";?>