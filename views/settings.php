<?php

use app\lib\Session;

$csrf = Session::init()->setCsrfToken();

echo '<h1 class="text-center">My Settings</h1>';

if (!empty($data['success'])) {
    include ROOT.'/views/inc/success.php';
}
if (!empty($data['error'])) {
    include ROOT.'/views/inc/error.php';
}

/* echo '<pre>';
var_dump($data['data']);
echo '</pre>'; */

?>

<div class="row">
    <div class="col-md-6 d-flex flex-column align-items-center justify-content-around">
        <!-- Display the avatar via the data URI scheme -->
        <img src="<?php echo 'data:media_type;base64,'.base64_encode($data['data']['avatar']); ?>" 
        class="border border-primary rounded-circle shadow-lg" alt="Your avatar"> 
        
        <form action="/settings.php" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap">
            <div id="uploadform" class="custom-file <?php echo ($data['errors']['avatar']) ? 'is-invalid' : ''; ?>">
                <input type="file" class="custom-file-input" id="avatar" name="avatar">
                <label class="custom-file-label" for="avatar">Choose file</label>
            </div>
            <input type="hidden" name="_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="_update" value="avatar">
            <input type="submit"  class="btn btn-primary" value="Add">
            <div class="invalid-feedback"><?php echo $data['errors']['avatar'] ?? ''; ?></div>
        </form>
        <script>
            document.querySelector('.custom-file-input').addEventListener('change', (e) => {
                e.target.nextElementSibling.innerText = document.getElementById("avatar").files[0].name;
            });
        </script>
       
    </div>

    <div class="col-md-6">
        <h4 class="">About you</h4>
        <div class="form-group">
            <label for="user">My username</label>
            <input type="text" id="user" class="form-control" value="<?php echo $_SESSION['user']; ?>" disabled>
        </div>
        <div class="form-group">
            <label for="email">My email</label>
            <input type="text" id="email" class="form-control" value="<?php echo $data['data']['email']; ?>" disabled>
        </div>
        <div class="form-group">
            <label for="since">Member since</label>
            <input type="text" id="since" class="form-control" value="<?php
            echo DateTime::createFromFormat('Y-m-d H:i:s', $data['data']['created_at'])->format('M jS, Y'); ?>" disabled>
        </div>
        <div class="form-group">
            <label for="numposts">Number of posts</label>
            <input type="text" id="numposts" class="form-control" value="<?php echo $data['data']['count']; ?>" disabled>
        </div>
    </div>
</div> 

<form action="/settings.php" method="POST" class="needs-validation m-4" novalidate>
    <div class="form-group">
        <label for="birthday">My birthday</label>
        <input type="date" class="form-control <?php echo ($data['errors']['birthday']) ? 'is-invalid' : ''; ?>" 
        id="birthday" name="birthday" value="<?php echo $_POST['birthday'] ?? $data['data']['birthday']; ?>">
        <div class="invalid-feedback"><?php echo $data['errors']['birthday'] ?? ''; ?></div>
    </div>
    <div class="form-group">
        <label for="location">My location</label>
        <input type="text" class="form-control <?php echo ($data['errors']['location']) ? 'is-invalid' : ''; ?>" 
        id="location" name="location" placeholder="Your location ..." value="<?php echo $_POST['location'] ?? $data['data']['location']; ?>">
        <div class="invalid-feedback"><?php echo $data['errors']['location'] ?? ''; ?></div> 
    </div>
    <div class="form-group">
        <label for="description">My description</label>
        <textarea class="form-control <?php echo ($data['errors']['description']) ? 'is-invalid' : ''; ?>" 
        id="description" name="description" placeholder="Describe yourself ..."><?php echo $_POST['description'] ?? $data['data']['descr']; ?></textarea>
        <div class="invalid-feedback"><?php echo $data['errors']['description'] ?? ''; ?></div> 
    </div>
    <input type="hidden" name="_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="_update" value="info">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Save your infos">
    </div>
</form>


<div class="alert alert-danger m-4">
<h4 class="text-center">Danger zone!</h4>

<form action="/settings.php" method="POST" 
class="needs-validation" novalidate>
    <h5 class="">Change password</h5>
    <div class="form-group">
        <label for="pwd">New Password *
        <span class="small">(At least 8 characters, must contain uppercase and lowercase letters and one digit)</span>
        </label>
        <input type="password" class="form-control <?php echo ($data['errors']['pwd']) ? 'is-invalid' : ''; ?>" 
        id="pwd" name="pwd" required>
        <div class="invalid-feedback"><?php echo $data['errors']['pwd'] ?? ''; ?></div> 
    </div>
    <div class="form-group">
        <label for="pwdrepeat">Repeat new password *</label>
        <input type="password" class="form-control" id="pwdrepeat" name="pwdrepeat" required>
    </div>
    <div class="form-group">
        <label for="confirm">Old Password *
        <span class="small">(You must enter your old password to confirm the action)</span>
        </label>
        <input type="password" class="form-control <?php echo ($data['errors']['confirm']) ? 'is-invalid' : ''; ?>" 
        id="confirm" name="confirm" required>
        <div class="invalid-feedback"><?php echo $data['errors']['confirm'] ?? ''; ?></div> 
    </div>
    <input type="hidden" name="_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="_update" value="pwd">
    <div class="form-group">
        <input type="submit" class="btn btn-danger w-100" value="Change your password">
    </div>
</form>

<form action="/settings.php" method="POST" 
class="needs-validation" novalidate>
    <h5 class="">Delete account</h5>
    <div class="form-group">
        <label for="confirmdelete">Password *
        <span class="small">(You must enter your password to confirm the action)</span>
        </label>
        <input type="password" class="form-control <?php echo ($data['errors']['confirmdelete']) ? 'is-invalid' : ''; ?>" 
        id="confirmdelete" name="confirmdelete" required>
        <div class="invalid-feedback"><?php echo $data['errors']['confirmdelete'] ?? ''; ?></div> 
    </div>
    <div><strong>Warning: </strong>This action cannot be undone</div>
    <div><strong>Note: </strong>Your posts are not deleted automatically. Please delete them manually before deleting your account, if you wish so.</div>
    <input type="hidden" name="_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="_method" value="delete">
    <div class="form-group">
        <input type="submit" class="btn btn-danger w-100" value="Delete your account">
    </div>
</form>
</div>