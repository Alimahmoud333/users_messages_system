<?php
session_start();
include "../database/config.php";
include "../constant/header_user.php";
include "../constant/message.php";
include "../functions.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? sha1($_POST['password']) : $user['password'];
    $image = $user['image'];

    if (!empty($_FILES['image']['name'])) {
        $newImage = imageUpload("image", "../uploads/");
        if ($newImage !== "fail") {
           
            deleteFile("../uploads", $user['image']);
            $image = $newImage;
        } else {
            showMessage("Image upload failed: $msgError", "danger");
        }
    }

    $stmt = $con->prepare("UPDATE users SET name = ?, email = ?, password = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $email, $password, $image, $id]);

    showMessage("Profile updated successfully!", "success");

    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

}


userHeader("Profile");
?>

<div class="container py-5 mt-5">
    <h2 class="text-white">User Profile</h2>
    <hr>

    <div class="card p-4 shadow-sm text-center m-auto" style="width: 500px;">
        <img src="../uploads/<?php echo htmlspecialchars($user['image']); ?>" class="m-auto rounded-circle mb-3"
            alt="User Image" width="100" height="100">

        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <!-- <p>Role: <?php echo htmlspecialchars($user['role']); ?></p> -->

        <button class="btn btn-primary mt-3 w-50 d-block m-auto" data-bs-toggle="modal" data-bs-target="#editModal">
            Edit Profile
        </button>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <img src="../uploads/<?php echo htmlspecialchars($user['image']); ?>" alt="User Image"
                            class="rounded-circle mb-2" width="80" height="80">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../constant/footer.php";?>