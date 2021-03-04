<form action="/register.php" method="POST" class="needs-validation border border-primary mx-auto w-50 p-4">
    <h1 class="text-center">Register</h1>
    <div class="form-group">
        <label for="user">Username *</label>
        <input type="text" class="form-control <?php echo 'is-invalid'; ?>" 
        id="user" name="user" value="<?php echo $_POST['user'] ?? ''; ?>" required placeholder="Your future username ...">
        <div class="invalid-feedback"><?php echo ''; ?></div>
    </div>
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" class="form-control <?php echo 'is-invalid'; ?>" 
        id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required placeholder="Your email ...">
        <div class="invalid-feedback"><?php echo ''; ?></div>
    </div>
    <div class="form-group">
        <label for="pwd">Password *
        <span class="small">At least 8 characters, must contain uppercase and lowercase letters and one digit</span>
        </label>
        <input type="password" class="form-control <?php echo 'is-invalid'; ?>" 
        id="pwd" name="pwd" required>
        <div class="invalid-feedback"><?php echo ''; ?></div> 
    </div>
    <div class="form-group">
        <label for="pwdrepeat">Repeat password *</label>
        <input type="password" class="form-control <?php echo 'is-invalid'; ?>" 
        id="pwdrepeat" name="pwdrepeat" required>
        <div class="invalid-feedback"><?php echo ''; ?></div>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Register">
    </div>
</form>