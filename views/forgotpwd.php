<?php
use app\lib\Session;

?>
<form action="/forgotpwd.php" method="POST" class="needs-validation border border-primary mx-auto w-50 p-4" novalidate>
    <h1 class="text-center">Forgot Password</h1>
    <?php
    if (isset($data['error'])) {
        echo '<div class="alert alert-danger">'.$data['error'].'</div>';
    } elseif (isset($data['success'])) {
        echo '<div class="alert alert-success">'.$data['success'].'</div>';
    }
    ?>
    <div class="form-group">
        <label for="email">Enter your email</label>
        <input type="text" class="form-control <?php echo ($data['errors']['email']) ? 'is-invalid' : ''; ?>" 
        id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
        <div class="invalid-feedback"><?php echo $data['errors']['email'] ?? ''; ?></div>
    </div>
    <input type="hidden" name="_token" value="<?php echo Session::init()->setCsrfToken(); ?>">
    <input type="hidden" name="_method" value="put">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Begin resetting my password">
    </div>
    <div class="text-center">Remembered your password? <a href="/login.php">Go back to Login</a></div>
</form>