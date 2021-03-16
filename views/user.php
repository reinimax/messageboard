<h1 class="text-center"><?php echo $data['data']['user'] ?></h1>

<div class="row w-75 mx-auto">
    <div class="col-md-6 d-flex flex-column align-items-center justify-content-around">
        <!-- Display the avatar via the data URI scheme -->
        <img src="<?php echo 'data:media_type;base64,'.base64_encode($data['data']['avatar']); ?>" 
        class="border border-primary rounded-circle shadow-lg" alt="Your avatar"> 
    </div>
    <div class="col-md-6">
        <p>Member since: <span><?php echo DateTime::createFromFormat('Y-m-d H:i:s', $data['data']['created_at'])->format('M jS, Y'); ?></span></p>
        <p>Number of posts: <span><?php echo $data['data']['count']; ?></span></p>
        <p>Birthday: <span><?php echo ($data['data']['birthday'] !== null) ?
        DateTime::createFromFormat('Y-m-d', $data['data']['birthday'])->format('M jS, Y') :
        'Not specified'; ?></span></p>
        <p>Location: <span><?php echo $data['data']['location'] ?? 'Not specified'; ?></span></p>
    </div>
</div> 

<div class="form-group w-75 mx-auto">
    <label for="description">Description</label>
    <textarea class="form-control" id="description" disabled>
    <?php echo $data['data']['descr'] ?? 'Not specified'; ?>
    </textarea>
</div>
    


