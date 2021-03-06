# Message Board

This shall become a message board to show off my php skills! It will use a MVC pattern whose structure is based on the last project we did at the Wifi course I participated in. For the general idea of a message board and its possible features I was inspired by some of the projects in the Ruby on Rails and Node.js courses at [The Odin Project](https://www.theodinproject.com).

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

## Misc.
- Classes will be integrated using PSR-4 standard and the Composer autoload module.
- For validation, I will use [GUMP](https://github.com/Wixel/GUMP)

## REST
- So, I came to the question of how to implement the PUT and DELETE requests
- There seem to be two main options here (I refer to [this post](https://stackoverflow.com/questions/12085619/php-rest-put-delete-options)):
  - Send an XMLHttpRequest via JavaScript.
  - Make a hidden form that submits the real method via POST and check for it in the controller. I decided to go this way for now. It should work well with PUT because only ever a single post can be updated at a time. DELETE may become problematic because I'm adding a lot of hidden forms. Maybe I will change something there in the future.
  - A third option would be to make this a proper standalone REST API and separate it completely from the frontend. Well, this may be a project for another time.


## Possible extensions
- Send a newly registered user a welcome mail
- Forgot password option in login
- A little portal for the user, where he can change the password, maybe add some information and an avatar
  - Then, there should be a view for displaying that user-info. Some of the info should be displayed only when the user viewing it is logged in.
- Allow users to comment under posts
- Allow integrating uploaded images into the posts
- The user may add tags/topics to the post. One can then search based on these tags
- Generally a search function