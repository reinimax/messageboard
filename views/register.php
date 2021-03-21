<?php
use app\lib\Session;

?>
<form action="/register.php" method="POST" class="needs-validation border border-primary mx-auto w-50 p-4" novalidate>
    <h1 class="text-center">Register</h1>
    <?php
    if (isset($data['error'])) {
        echo '<div class="alert alert-danger">'.$data['error'].'</div>';
    }
    ?>
    <div class="form-group">
        <label for="user">Username *
        <span class="small">(Only alpha-numeric, underscores and dashes)</span>
        </label>
        <input type="text" class="form-control <?php echo ($data['errors']['user']) ? 'is-invalid' : ''; ?>" 
        id="user" name="user" value="<?php echo $_POST['user'] ?? ''; ?>" required placeholder="Your future username ...">
        <div class="invalid-feedback"><?php echo $data['errors']['user'] ?? ''; ?></div>
    </div>
    <div class="form-group">
        <label for="email">Email *</label>
        <div><small class="text-secondary"><strong>Note: </strong>This is a portfolio project. You are not required to enter a real email address. Something like x@x.x suffices to try this project out. Anyway, your data will not be passed to anyone and you can always delete your profile.</small></div>
        <input type="email" class="form-control <?php echo ($data['errors']['email']) ? 'is-invalid' : ''; ?>" 
        id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required placeholder="Your email ...">
        <div class="invalid-feedback"><?php echo $data['errors']['email'] ?? ''; ?></div>
    </div>
    <div class="form-group">
        <label for="pwd">Password *
        <span class="small">(At least 8 characters, must contain uppercase and lowercase letters and one digit)</span>
        </label>
        <input type="password" class="form-control <?php echo ($data['errors']['pwd']) ? 'is-invalid' : ''; ?>" 
        id="pwd" name="pwd" required>
        <div class="invalid-feedback"><?php echo $data['errors']['pwd'] ?? ''; ?></div> 
    </div>
    <div class="form-group">
        <label for="pwdrepeat">Repeat password *</label>
        <input type="password" class="form-control" id="pwdrepeat" name="pwdrepeat" required>
    </div>
    <input type="hidden" name="_token" value="<?php echo Session::init()->setCsrfToken(); ?>">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Register">
    </div>
</form>