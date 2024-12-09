<?php

namespace App\Controllers;

use Core\BaseController;
use Core\Request;

class BlogController extends BaseController
{
    // Display a list of published blog posts with pagination
    public function index(Request $request)
    {
        // Placeholder: Fetch posts from the database and display them
        $blogModel = new \App\Models\Blog();
        $posts = $blogModel->getAllPublished();

        // Pass data to template
        $this->template->render('blogs/blog_list.html', [
            'blogs' => $posts ? $posts : [],
            'title' => 'Home Page',
            'header' => 'All Published Blog Posts'
        ]);
    }

    // Display a single blog post along with comments
    public function view($id)
    {
        // Fetch the blog post by ID from the database
        $blogModel = new \App\Models\Blog();
        $post = $blogModel->getById($id);

        // Pass data to template
        $this->template->render('blogs/blog_list.html', [
            'blogs' => $post ? $post : [],
            'title' => 'Home',
            'header' => 'All Blog Posts'
        ]);

    }
	
}
