<?php
use app\lib\Session;

?>
<form action="/login.php" method="POST" class="needs-validation border border-primary mx-auto w-50 p-4" novalidate>
    <h1 class="text-center">Login</h1>
    <?php
    if (isset($data['error'])) {
        echo '<div class="alert alert-danger">'.$data['error'].'</div>';
    }
    ?>
    <div class="form-group">
        <label for="user">Username or email</label>
        <input type="text" class="form-control <?php echo ($data['errors']['user']) ? 'is-invalid' : ''; ?>" 
        id="user" name="user" value="<?php echo $_POST['user'] ?? ''; ?>" required placeholder="Your username or email...">
        <div class="invalid-feedback"><?php echo $data['errors']['user'] ?? ''; ?></div>
    </div>
    <div class="form-group">
        <label for="pwd">Password</label>
        <input type="password" class="form-control <?php echo ($data['errors']['pwd']) ? 'is-invalid' : ''; ?>" 
        id="pwd" name="pwd" required>
        <div class="invalid-feedback"><?php echo $data['errors']['pwd'] ?? ''; ?></div> 
    </div>
    <input type="hidden" name="_token" value="<?php echo Session::init()->setCsrfToken(); ?>">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Login">
    </div>
    <div class="text-center"><a href="/forgotpwd.php">I forgot my password</a></div>
</form>