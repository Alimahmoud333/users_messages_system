<?php
session_start();
include "../database/config.php";
include "../constant/header.php";
include "../constant/message.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$usersCount = $con->query("SELECT COUNT(*) FROM users")->fetchColumn();
$messagesCount = $con->query("SELECT COUNT(*) FROM messages")->fetchColumn();


renderHeader("Dashboard");


?>

<div class="container py-5 mt-5">
    <h2 class="text-white">Admin Dashboard </h2>
    <hr>

    <div class="row text-center">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h4>Users</h4>
                <h2><?php echo $usersCount; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h4>Messages</h4>
                <h2><?php echo $messagesCount; ?></h2>
            </div>
        </div>

    </div>


</div>

<?php include "../constant/footer.php";?>