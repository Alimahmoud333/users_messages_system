<?php
session_start();
include "../database/config.php";
include "../constant/header_user.php";
include "../constant/message.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_comment'])) {
    $message_id = intval($_POST['message_id']);
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $con->prepare("INSERT INTO comments (message_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$message_id, $user_id, $comment]);
        $_SESSION['message'] = "Comment added successfully!";
        header("Location: all_message.php");
        exit();
    } else {
        showMessage("Comment cannot be empty", "danger");
    }
}

if (isset($_POST['delete_comment_id'])) {
    $comment_id = intval($_POST['delete_comment_id']);
    $stmtCheck = $con->prepare("SELECT * FROM comments WHERE id=? AND user_id=?");
    $stmtCheck->execute([$comment_id, $user_id]);
    $comment = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    if ($comment) {
        $stmtDel = $con->prepare("DELETE FROM comments WHERE id=?");
        $stmtDel->execute([$comment_id]);
        $_SESSION['message'] = "Comment deleted successfully!";
    } else {
        $_SESSION['message'] = "You can only delete your own comments!";
    }
    header("Location: all_message.php");
    exit();
}

$stmt = $con->prepare("SELECT m.*, u.name, u.image FROM messages m
                       INNER JOIN users u ON m.user_id = u.id
                       ORDER BY m.created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

userHeader("Messages");
?>

<div class="container py-5 mt-5">
    <h2 class="text-white">All Messages</h2>
    <hr>

    <?php 
    if (isset($_SESSION['message'])) { 
        showMessage($_SESSION['message'], "success"); 
        unset($_SESSION['message']);
    } 
    ?>

    <?php if (count($messages) > 0): ?>
    <div class="list-group">
        <?php foreach ($messages as $msg): ?>
        <div class="list-group-item mb-3 bg-secondary text-white shadow border-1 border-dark">
            <div class="d-flex align-items-center">
                <?php echo $msg['user_id'] == $user_id ?'you:':'' ?> <img src="../uploads/<?php echo $msg['image']; ?>"
                    width="50" height="50" class="rounded-circle me-2">
                <strong class="text-dark fw-bold"><?php echo htmlspecialchars($msg['name']); ?></strong>
                <small class="ms-auto"><?php echo $msg['created_at']; ?></small>
            </div>
            <p class="mt-2 fs-2" style="margin-left: 30px;"><?php echo htmlspecialchars($msg['message']); ?></p>

            <div class="mt-2 ms-5">
                <?php
                        $stmtC = $con->prepare("SELECT c.*, u.name, u.image FROM comments c 
                                                INNER JOIN users u ON c.user_id = u.id
                                                WHERE c.message_id=? ORDER BY created_at ASC");
                        $stmtC->execute([$msg['id']]);
                        $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                <?php foreach ($comments as $cmt): ?>

                <div class="d-flex align-items-start mb-1" style="margin-left: 20px;">
                    <img src="../uploads/<?php echo $cmt['image']; ?>" width="30" height="30"
                        class="rounded-circle me-2">
                    <div>
                        <strong><?php echo htmlspecialchars($cmt['name']); ?>:</strong>
                        <?php echo htmlspecialchars($cmt['comment']); ?>
                    </div>
                    <?php if ($cmt['user_id'] == $user_id): ?>
                    <form method="POST" class="ms-auto">
                        <input type="hidden" name="delete_comment_id" value="<?php echo $cmt['id']; ?>">
                        <button type="submit" class="btn btn-sms btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    <?php endif; ?>
                </div>
                <hr>
                <?php endforeach; ?>

                <form method="POST" class="mt-2">
                    <div class="input-group">
                        <input type="text" name="comment" class="form-control" placeholder="Add a comment..." required>
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" name="add_comment" class="btn btn-primary">Comment</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">No messages found</div>
    <?php endif; ?>
</div>

<?php include "../constant/footer.php";?>