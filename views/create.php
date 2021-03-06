<?php
use app\lib\Session;

?>
<form action="/create.php" method="POST" class="needs-validation border border-primary mx-auto w-50 p-4" novalidate>
    <h1 class="text-center">New post</h1>
    <?php
    if (isset($data['error'])) {
        echo '<div class="alert alert-danger">'.$data['error'].'</div>';
    }
    ?>
    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" class="form-control <?php echo ($data['errors']['title']) ? 'is-invalid' : ''; ?>" 
        id="title" name="title" value="<?php echo $_POST['title'] ?? ''; ?>" required placeholder="The title of your post ...">
        <div class="invalid-feedback"><?php echo $data['errors']['title'] ?? ''; ?></div>
    </div>
    <div class="form-group">
        <label for="message">Message *</label>
        <textarea class="form-control <?php echo ($data['errors']['message']) ? 'is-invalid' : ''; ?>" 
        id="message" name="message" required placeholder="Your message ..."></textarea>
        <div class="invalid-feedback"><?php echo $data['errors']['message'] ?? ''; ?></div> 
    </div>
    <input type="hidden" name="_token" value="<?php echo Session::init()->setCsrfToken(); ?>">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Post your message">
    </div>
</form>