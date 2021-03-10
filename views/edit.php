<?php
use app\lib\Session;

$key =  implode('', array_keys($data['data']));

?>
<form action="/edit.php?id=<?php echo $key; ?>" method="POST" 
class="needs-validation border border-primary mx-auto w-50 p-4" novalidate>
    <h1 class="text-center">Edit post</h1>
    <?php
    if (isset($data['error'])) {
        echo '<div class="alert alert-danger">'.$data['error'].'</div>';
    }
    ?>
    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" class="form-control <?php echo ($data['errors']['title']) ? 'is-invalid' : ''; ?>" 
        id="title" name="title" value="<?php echo $_POST['title'] ?? $data['data'][$key][0]['title']; ?>" required placeholder="The title of your post ...">
        <div class="invalid-feedback"><?php echo $data['errors']['title'] ?? ''; ?></div>
    </div>
    <div class="form-group">
        <label for="message">Message *</label>
        <textarea class="form-control <?php echo ($data['errors']['message']) ? 'is-invalid' : ''; ?>" 
        id="message" name="message" required placeholder="Your message ..."><?php echo $data['data'][$key][0]['content']; ?></textarea>
        <div class="invalid-feedback"><?php echo $data['errors']['message'] ?? ''; ?></div> 
    </div>
    <div class="form-group">
        <label for="tag">Add tags 
        <span class="small">(Click and drag or hold crtl or shift to select multiple tags)</span>
        </label>
        <!-- The brackets in the name are important! This tells PHP to aggregate multiple values into an array -->
        <select name="tag[]" id="tag" class="form-control" multiple size="3">
            <?php
            foreach ($data['taglist'] as $item) {
                $selected = '';
                foreach ($data['data'][$key] as $selectedItem) {
                    if ($item['id'] === $selectedItem['id']) {
                        $selected = 'selected';
                    }
                }
                echo '<option value="'.$item['id'].'" '.$selected.'>'.$item['tag'].'</option>';
            }
            ?>
        </select>
    </div>
    <div class="alert alert-info">
        Don't find the right tag? <a href="/createtag" class="alert-link">Create one!</a>.
        <p class="small">(<strong>Important!</strong> Your message will not be saved if you do this!)</p>
    </div>
    <input type="hidden" name="_token" value="<?php echo Session::init()->setCsrfToken(); ?>">
    <input type="hidden" name="_method" value="put">
    <div class="form-group">
        <input type="submit" class="btn btn-primary w-100" value="Edit your message">
    </div>
</form>
