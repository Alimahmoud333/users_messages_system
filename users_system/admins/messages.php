<?php
session_start();
include "../database/config.php";
include "../constant/header.php";
include "../constant/message.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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
        header("Location: messages.php");
        exit();
    }
}

if (isset($_POST['mark_read'])) {
    $message_id = intval($_POST['message_id']);
    $stmt = $con->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$message_id]);
    header("Location: messages.php");
    exit();
}

if (isset($_POST['delete_message'])) {
    $message_id = intval($_POST['message_id']);
    $stmtC = $con->prepare("DELETE FROM comments WHERE message_id = ?");
    $stmtC->execute([$message_id]);
    $stmt = $con->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $_SESSION['message'] = "Message and its comments deleted successfully!";
    header("Location: messages.php");
    exit();
}

$stmt = $con->prepare("SELECT m.*, u.name, u.image FROM messages m
                       INNER JOIN users u ON m.user_id = u.id
                       ORDER BY m.created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

renderHeader("Messages");
?>

<div class="container py-5 mt-5">
    <h2 class="text-white">All Messages</h2>
    <hr>

    <?php if (isset($_SESSION['message'])) {
        showMessage($_SESSION['message'], "success");
        unset($_SESSION['message']);
    } ?>

    <?php if (count($messages) > 0): ?>
    <div class="list-group">
        <?php foreach ($messages as $msg): ?>
        <div
            class="list-group-item mb-3 shadow-lg text-white <?php echo $msg['is_read'] ? 'bg-secondary' : 'bg-warning'; ?>">
            <div class="d-flex align-items-center mb-2">
                <img src="../uploads/<?php echo $msg['image']; ?>" width="50" height="50" class="rounded-circle me-2">
                <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                <small class="ms-auto"><?php echo $msg['created_at']; ?></small>

            </div>



            <h3 style="margin-left: 20px;"><?php echo htmlspecialchars($msg['message']); ?></h3>




            <div class="mt-2 ms-5">
                <?php
                $stmtC = $con->prepare("SELECT c.*, u.name, u.image FROM comments c 
                                        INNER JOIN users u ON c.user_id = u.id
                                        WHERE c.message_id=? ORDER BY created_at ASC");
                $stmtC->execute([$msg['id']]);
                $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($comments as $cmt): ?>
                <div class="d-flex align-items-start mb-1">
                    <img src="../uploads/<?php echo $cmt['image']; ?>" width="30" height="30"
                        class="rounded-circle me-2">
                    <div>
                        <strong class="text-dark fw-bold"><?php echo htmlspecialchars($cmt['name']); ?>:</strong>
                        <?php echo htmlspecialchars($cmt['comment']); ?>
                        <p style="font-size: 10px; color: #948e8eff;">
                            <?php echo htmlspecialchars($cmt['created_at']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>

                <form method="POST" class="mt-2">
                    <div class="input-group">
                        <input type="text" name="comment" class="form-control" placeholder="Add a comment..." required>
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" name="add_comment" class="btn btn-primary">Comment</button>
                    </div>
                </form>
            </div>
            <div class="d-flex my-3">
                <form method="POST" class="me-2">
                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                    <?php if ($msg['is_read'] == 0): ?>
                    <button type="submit" name="mark_read" class="btn btn-sm btn-success">Mark as Read</button>
                    <?php else: ?>
                    <span class="badge bg-secondary">Read</span>
                    <?php endif; ?>
                </form>

                <form method="POST">
                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                    <button type="submit" name="delete_message" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this message and its comments?')"><i
                            class="fas fa-times"></i></button>
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