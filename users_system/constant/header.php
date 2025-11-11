<?php
function renderHeader($pageTitle = "User") {
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="../js/bootstrap.bundle.js"></script>
</head>

<body style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; font-weight:bold;"
    class="bg-secondary">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><?php echo htmlspecialchars($pageTitle); ?></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="../admins/dashboard.php">
                            <i class="fa-solid fa-gauge"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="../admins/messages.php">
                            <i class="fa-solid fa-box"></i> Messages
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="../admins/users.php">
                            <i class="fa-solid fa-users"></i> Users
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="../index.php" class="btn btn-outline-danger">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php
}
?>