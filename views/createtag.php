<?php
use app\lib\Session;

?>
<form action="/createtag.php" method="POST" class="needs-validation border border-primary mx-auto w-50 p-4" novalidate>
    <h1 class="text-center">New tag</h1>
    <?php
    if (isset($data['error'])) {
        echo '<div class="alert alert-danger">'.$data['error'].'</div>';
    }
    ?>
    <div class="form-group">
        <label for="newtag">The name of the tag *</label>
        <input type="text" class="form-control <?php echo ($data['errors']['newtag']) ? 'is-invalid' : ''; ?>" 
        id="newtag" name="newtag" value="<?php echo $_POST['newtag'] ?? ''; ?>" required placeholder="New tag ...">
        <div class="invalid-feedback"><?php echo $data['errors']['newtag'] ?? ''; ?></div>
    </div>
    <input type="hidden" name="_token" value="<?php echo Session::init()->setCsrfToken(); ?>">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Create new tag">
    </div>
</form>
