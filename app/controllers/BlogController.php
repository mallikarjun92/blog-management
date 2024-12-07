<?php

namespace App\Controllers;

use Core\BaseController;

class BlogController extends BaseController
{
    // Display a list of published blog posts with pagination
    public function index()
    {
        // Placeholder: Fetch posts from the database and display them
        echo "Displaying a list of blog posts with pagination.";
    }

    // Display a single blog post along with comments
    public function view($id)
    {
        // Fetch the blog post by ID from the database
        $blogModel = new \App\Models\Blog();
        $post = $blogModel->getById($id);

        // Pass data to template
        $this->template->render('blogs/blog_list.html', [
            'blogs' => $post,
            'title' => 'Home',
            'header' => 'All Blog Posts'
        ]);

    }

    // Handle the creation of a new blog post
    public function create()
    {
        // Placeholder: Display a form to create a new blog post or handle form submission
        echo "Creating a new blog post.";
    }

    // Handle the editing of an existing blog post
    public function edit($id)
    {
        // Placeholder: Fetch the post data for the given ID and display an edit form
        echo "Editing the blog post with ID: $id.";
    }

    // Handle the deletion of a blog post
    public function delete($id)
    {
        // Placeholder: Delete the blog post with the given ID
        echo "Deleting the blog post with ID: $id.";
    }
}
