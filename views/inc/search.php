<form class="form-inline" action="/search.php" method="GET">
    <input class="form-control mr-2" type="text" name="query" placeholder="Search">
    <label for="limit" class="mr-2"> for </label>
    <select class="form-control mr-2" id="limit" name="limit">
        <option value="all">All</option>
        <option value="user">User</option>
        <option value="title">Title</option>
        <option value="tag">Tag</option>
    </select>
    <button class="btn btn-outline-primary" type="submit">Search</button>
</form>
