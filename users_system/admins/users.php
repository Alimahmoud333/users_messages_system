<?php
session_start();
include "../database/config.php";
include "../constant/header.php";
include "../constant/message.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $con->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$delete_id]);
    $_SESSION['message'] = "User deleted successfully!";
    header("Location: users.php");
    exit();
}

$stmt = $con->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

renderHeader("Users");
?>

<div class="container py-5 mt-5">
    <h2 class="text-white">All Users</h2>
    <hr>

    <?php if (isset($_SESSION['message'])) { 
        showMessage($_SESSION['message'], "success"); 
        unset($_SESSION['message']);
    } ?>

    <?php if (count($users) > 0): ?>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Profile</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td>
                    <img src="../uploads/<?php echo $user['image']; ?>" width="50" height="50" class="rounded-circle">
                </td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="text-center">
                    <a href="?delete_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-warning">No users found</div>
    <?php endif; ?>
</div>

<?php include "../constant/footer.php";?>