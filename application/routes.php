<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

// show index page
Route::get('/', function()
{
	$posts = Post::with('user')->order_by('updated_at', 'desc')->paginate(5);
    return View::make('home')
        ->with('posts', $posts);
});

// When a user is logged in he/she is taken to creating new post
Route::get('admin', array('before' => 'auth', 'do' => function() {
	$user = Auth::user();
    return View::make('new')->with('user', $user);
}));


// When the new post is submitted we handle that here
Route::post('admin', array('before' => 'auth', 'do' => function() {
	$new_post = array(
        'post_title'    => Input::get('post_title'),
        'post_body'     => Input::get('post_body'),
        'post_author'   => Input::get('post_author')
    );
   
    $rules = array(
        'post_title'     => 'required|min:3|max:255',
        'post_body'      => 'required|min:10'
    );
    
    $validation = Validator::make($new_post, $rules);
    if ( $validation -> fails() )
    {
        return Redirect::to('admin')
                ->with('user', Auth::user())
                ->with_errors($validation)
                ->with_input();
    }
    // create the new post after passing validation
    $post = new Post($new_post);
    $post->save();
    // redirect to viewing all posts
    return Redirect::to('/');
}));

// Present the user with login form
Route::get('login', function() {
	return View::make('login');
});

// Process the login form
Route::post('login', function() {
	$userinfo = array(
        'username' => Input::get('username'),
        'password' => Input::get('password')
    );
    if ( Auth::attempt($userinfo) )
    {
        return Redirect::to('admin');
    }
    else
    {
        return Redirect::to('login')
            ->with('login_errors', true);
    }
});

// Process Logout process
Route::get('logout', function() {
	Auth::logout();
	return Redirect::to('/');
});

Route::delete('post/(:num)', array('before' => 'auth', 'do' => function($id){
    $delete_post = Post::with('user')->find($id);
    $delete_post -> delete();
    return Redirect::to('/')
            ->with('success_message', true);
})) ;
/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

Route::get('sendemail', function()
{
	// basic setup of your info and what to send
	$username = "montogeek@gmail.com"; // your Gmail account username
	$password = "aprbleyhgofhkqot"; // your Gmail account password
	$from_address 	= "montogeek@gmail.com"; // your Gmail account email
	$from_name 		= "Fernando Montoya"; 
	$to_address 	= "montoya.azul@gmail.com";
	$to_name		= "Fernandito Montoya";
	$message_subj	= "Hello there";
	$plain_message	= "Lalalala"; 
	$html_message	= "Google!";

	$mailer = IoC::resolve('mailer');

	$transporter = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
	  ->setUsername($username)
	  ->setPassword($password);

	$mailer = Swift_Mailer::newInstance($transporter);

	// Construct the message
	$message = Swift_Message::newInstance($message_subj)
	    ->setFrom(array($from_address=>$from_name))
	    ->setTo(array($to_address=>$to_name))
	    ->addPart($plain_message,'text/plain')
	    ->setBody($html_message,'text/html');

	// Send the email
	$numSent = $mailer->send($message);

	if ($numSent == 1) {
		$status_message = "Success! Email has been sent!";
	} else {
		$status_message = "Error, email was not sent...";
	}
	
	return View::make('home.index')->with('status_message', $status_message);
});

// Route for Blog_Controller
Route::controller('blog');

// Route for Blosjs_Controller
Route::controller('blosjs');

// Route for Blos_Controller
Route::controller('blos');