<?php

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Include core classes
use Core\Router;

// Load application configurations
$config = require_once __DIR__ . '/../config.php';

// Start the session
session_start();

// Instantiate the Router
$router = new Router();

// Define routes
// Public routes
$router->add('/', 'BlogController', 'index', ['GET']);
$router->add('/post/{id}', 'BlogController', 'view', ['GET']);
$router->add('/post/create', 'BlogController', 'create', ['GET', 'POST']);
$router->add('/post/edit/{id}', 'BlogController', 'edit', ['GET', 'POST']);
$router->add('/post/delete/{id}', 'BlogController', 'delete', ['POST']);

// Admin routes
$router->add('/admin/login', 'AdminController', 'login', ['GET', 'POST']); // Admin login
$router->add('/admin/logout', 'AdminController', 'logout', ['POST']); // Admin logout
$router->add('/admin/dashboard', 'AdminController', 'dashboard', ['GET']); // Admin dashboard
$router->add('/admin/posts', 'PostController', 'index', ['GET', 'POST']); // Manage blog posts
$router->add('/admin/posts/create/{id}', 'PostController', 'create', ['GET', 'POST']); // Create a new post
$router->add('/admin/posts/edit/{id}', 'PostController', 'edit', ['GET', 'POST']); // Edit a post
$router->add('/admin/posts/delete/{id}', 'PostController', 'delete', ['POST']); // Delete a post
$router->add('/admin/comments/{id}', 'CommentController', 'moderate', ['GET', 'POST']); // Moderate comments
$router->add('/admin/users', 'UserController', 'index', ['GET', 'POST']); // Manage users

// Get the requested URL
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dispatch the request to the appropriate controller and action
$router->dispatch($url);
