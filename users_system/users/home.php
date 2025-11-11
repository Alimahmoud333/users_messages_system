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

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $con->prepare("DELETE FROM comments WHERE message_id=?");
    $stmt->execute([$delete_id]);
    
    $stmt = $con->prepare("DELETE FROM messages WHERE id=? AND user_id=?");
    $stmt->execute([$delete_id, $user_id]);
    
    $_SESSION['message'] = "Message deleted successfully!";
    header("Location: home.php");
    exit();
}

if (isset($_POST['update_message'])) {
    $message_id = intval($_POST['message_id']);
    $message_text = trim($_POST['message_text']);
    $stmt = $con->prepare("UPDATE messages SET message=? WHERE id=? AND user_id=?");
    $stmt->execute([$message_text, $message_id, $user_id]);
    $_SESSION['message'] = "Message updated successfully!";
    header("Location: home.php");
    exit();
}

$stmt = $con->prepare("SELECT * FROM messages WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


userHeader(title: "Home Page");
?>

<div class="container py-5 mt-5">
    <h2 class="text-white">Your Messages</h2>
    <hr>

    <?php if (isset($_SESSION['message'])) { 
        showMessage($_SESSION['message'], "success"); 
        unset($_SESSION['message']);
    } ?>

    <?php if (count($messages) > 0): ?>
    <div class="list-group text-dark">
        <?php foreach ($messages as $msg): ?>
        <div class="list-group-item d-flex justify-content-between align-items-start m-2
                    <?php echo $msg['is_read'] ? 'bg-light' : 'bg-secondary text-white'; ?>">
            <div class="ms-2 me-auto">
                <div class="fw-bold"><?php echo htmlspecialchars($msg['message']); ?></div>
                <small><?php echo $msg['created_at']; ?></small>
            </div>
            <div class="btn-group">
                <button class="btn btn-sm btn-primary mx-2" data-bs-toggle="modal"
                    data-bs-target="#editModal<?php echo $msg['id']; ?>">Edit</button>
                <a href="?delete_id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        </div>

        <div class="modal fade" id="editModal<?php echo $msg['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Message</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="message_text" class="form-control" rows="4"
                                required><?php echo htmlspecialchars($msg['message']); ?></textarea>
                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="update_message" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">No messages found</div>
    <?php endif; ?>
</div>

<?php include "../constant/footer.php";?>