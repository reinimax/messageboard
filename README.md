# Message Board

This shall become a message board to show off my php skills! It will use a MVC pattern whose structure is based on the last project we did at the Wifi course I participated in. For the general idea of a message board and its possible features I was inspired by some of the projects in the Ruby on Rails and Node.js courses at [The Odin Project](https://www.theodinproject.com).

## Reflection
- I learned a lot of stuff doing this project.
- The code is not very DRY. Part of this is because I tried to do this project quite fast and copied a lot of stuff. In real life it would be a good idea to refactor the code.

## General ideas
- Visitors can read posts, but only logged in users can write posts

## Page structure/Functionality
- The entry point will be index.php in /public. What will the visitor see there?
  - Maybe a list of the 3? 5? 10? most recent posts?
- From there, the visitor can go to a log in and sign up form
- Logged in users can
  - Write new posts
  - See a list of all their posts
  - Edit their posts (an edit button should show up on posts that they wrote)
  - Delete their posts

## Routes
My ideas for now. This may change during work on the project.

|Route|Method|Controller|Controller-Method|
|-----|------|----------|-----------------|
|/|get|PostController|index|
|/login|get|HomeController|login|
|/login|post|HomeController|login|
|/logout|get|HomeController|logout|
|/register|get|HomeController|register|
|/register|post|HomeController|register|
|/create|get|PostController|create|
|/create|post|PostController|save|
|/show|get|PostController|show|
|/edit|get|PostController|edit|
|/edit|put|PostController|update|
|/delete|delete|PostController|delete|

## Controllers
- Application, which is kind of the "supercontroller" of the whole application
  - It needs methods to register the routes and a property to store them
  - It needs methods to retrieve the path the user entered and the request method
  - When the user navigates to a certain route, the appropriate controller with the appropriate method needs to be called
  - Application then calls the view and passes the data it got back.
- HomeController will handle Login, Signup.
- PostController will handle displaying and manipulating posts.
- Later on it may be useful to add other controllers like a UserController
  - Each controller references a corresponding model and returns the data it got from its model back to the Application

## Models
- The models will need methods for the CRUD operations. This will be based on the routes/controllers.

## Views
- There will be views corresponding to the page structure/functionality described above. These are in essence html documents with placeholders for the data to be filled in.
- A class View will receive the data from the Application and render it by grabbing the appropriate document and filling in the received data.
- Navigating or entering data and sending it will redirect to index.php (thanks to the htaccess file), which will call Application, which will check the route and call the corresponding controller with the appropriate method and so on ... 

## DB:
- A table users with id (pk), username, email, pwd, created_at
- A table posts with id (pk), user_id (fk), title, text, created_at, updated_at
- A table tags with id (pk), tag
- A table posts_tags, which is a pivot table for establishing a m:n relationship between posts and tags

## Misc.
- Classes will be integrated using PSR-4 standard and the Composer autoload module.
- For validation, I will use [GUMP](https://github.com/Wixel/GUMP)

## REST
- So, I came to the question of how to implement the PUT and DELETE requests
- There seem to be two main options here (I refer to [this post](https://stackoverflow.com/questions/12085619/php-rest-put-delete-options)):
  - Send an XMLHttpRequest via JavaScript.
  - Make a hidden form that submits the real method via POST and check for it in the controller. I decided to go this way for now. It should work well with PUT because only ever a single post can be updated at a time. DELETE may become problematic because I'm adding a lot of hidden forms. Maybe I will change something there in the future.
  - A third option would be to make this a proper standalone REST API and separate it completely from the frontend. Well, this may be a project for another time.

## Search function
- I had quite some fun researching and experimenting with implementing a search function. 
- At first I tried the LIKE clause, which worked fine at the first attempt, but turned out to be actually quite limited. Also, it is stated that it eats up much performance though for the scope of this project this would probably be no problem.
- Then I tried the MySql Fulltext search, which is far mightier, more flexible, and also quite weird at times (I'll never wonder again when I get weird search results somewhere). I played around with the boolean mode, but decided to leave it in natural language mode for simplicity's sake. The boolean mode would be cool for a dedicated search mask.
- I also found out that there are external full-text engines. 

## Tags
- This proved more troublesome than expected. I settled for an easy way out by displaying them in the create/edit form as a select with multiple options. That's certainly not great UX/UI, but it accomplishes what I intended: to model and implement an m:n relationship between tables.
- Originally I envisioned having a button that creates a new element (like a datalist) where the user kann select or add a new tag. Via this button a number of these elements could be created. It turned out that the architecture of this project is not really well suited for that. It could be done with JavaScript but then it would also be better to send an XHR via JavaScript to the Backend instead of just submitting the form. This approach would favor also a separation between frontend and backend.
- Another problem is, that creating new tags redirects to a new page. So all input when the user was writing/editing a post ist lost. Also this is a case for an architecture that separates frontend and backend. Or at least for something that doesn't always create a new view when a request is sent.

## Avatars
- The user-avatars took some doing, but it was an interesting research where I learned some new stuff and finally getting it right was very satisfying.
- The original premise was that I didn't want to save the avatars in the public folder, where anyone could access them, but in an uploads folder outside the public folder.
- This led to a problem: I cannot point to this place in the `src` attribute of the `<img>` tag because it is a filepath, not a URL.
- My first try was to output the image directly with the `imagepng()` function (my test-image was a png). There are two catches I found on php.net:
  - Using the output buffer to save the image in a variable (awesome!)
  - When outputting the image directly to the browser (which is done by setting the filepath to null), the other parameters of the function need to be explicitly stated
- Well, at first, this didn't really help me much. I tried including a separate file where I outputted the image, which obviously didn't work, and linking to that file left me with the same problem I started with.
- In the meantime, while souring google, I came across a lot of posts discussing saving images in a MySQL database. I had no luck finding any answers to my original question ("How the heck do I display an image that lies outside my public folder?"), but this MySQL stuff left me curious. "Ok, so people save their images in a database. But how do they get them out of the database again and display them on their site?"
- This question finally yielded the answer in the form of [this tutorial](https://www.digitalocean.com/community/tutorials/how-to-use-the-mysql-blob-data-type-to-store-images-with-php-on-ubuntu-18-04). It turns out the answer lies in using the [data URI scheme](https://en.wikipedia.org/wiki/Data_URI_scheme) in the `src` attribute! 
- The last step was to learn that also the image-variable that I created via output-buffer and the `imagepng()` function needs to be base_64 encoded. Nice! Learned something.

## Extensions that I want to implement
- Send a newly registered user a welcome mail
- Forgot password option in login
- A little portal for the user, where he can change the password, maybe add some information and an avatar
  - Then, there should be a view for displaying that user-info. Some of the info should be displayed only when the user viewing it is logged in.
- And a pagination! (caution: the count has to come from the array, because mysql has a separate dataset for each tag!)

## Cool stuff that didn't make it (for now)
- A like/dislike system
- Email notifications (e.g. when you got a like) and connected to this, the ability to follow users/tags
- Allow formatting messages, adding images and links
- Comments
- Private messages between users

## Notes for making this production ready
- In the end, don't forget to clean stuff up for production (error messsages, redirections/views (no controller or method should go simply to 404 not found), some simple styling)
- Also think about how to handle posts of deleted users. Right now the delete cascades, so the posts are deleted. Another option would be to keep the posts and set user to null. Then I can make a check in index.php if user=null then echo 'deleted user' or so. I could also make a checkbox when deleting the user that asks if all posts should be deleted or not.

