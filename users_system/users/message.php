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

// Add new message
if (isset($_POST['add_message'])) {
    $message_text = trim($_POST['message_text']);
    if (!empty($message_text)) {
        $stmt = $con->prepare("INSERT INTO messages (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
        $stmt->execute([$user_id, $message_text]);
        $_SESSION['message'] = "Message added successfully!";
        header("Location: home.php");
        exit();
    } else {
        showMessage("Message cannot be empty", "danger");
    }
}

userHeader("Messages");

?>


<div class="container py-5 mt-5">
    <h2 class="text-white">User Messages</h2>
    <hr>

    <?php if (isset($_SESSION['message'])) { 
        showMessage($_SESSION['message'], "success"); 
        unset($_SESSION['message']);
    } ?>

    <!-- Add New Message Form -->
    <div class="card mb-4 p-3 shadow-sm  bg-secondary">
        <form method="POST">
            <div class="mb-3">
                <textarea name="message_text" class="form-control" rows="3" placeholder="Type a new message..."
                    required></textarea>
            </div>
            <button type="submit" name="add_message" class="btn btn-primary">Add Message</button>
        </form>
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


</div>

</div>

<?php include "../constant/footer.php";?>