<?php

namespace App\Controllers;

use Core\BaseController;
use Core\PaginationHelper;
use Core\Request;

class BlogController extends BaseController
{
    // Display a list of published blog posts with pagination
	public function index(Request $request)
	{
		// Placeholder: Fetch posts from the database and display them
		$postsPerPage = 10;
		$currentPage  = $request->get('page', 1);
		$offset       = ($currentPage - 1) * $postsPerPage;
		
		$blogModel  = new \App\Models\Blog();
		$posts      = $blogModel->getAllPublished($postsPerPage, $offset);
		$totalPosts = $blogModel->countAll();
		$totalPages = ceil($totalPosts / $postsPerPage);
		
		// Pass data to template
		$this->template->render(
			'blogs/blog_list.html', [
			'blogs'      => $posts ? $posts : [],
			'title'      => 'Home Page',
			'header'     => 'All Published Blog Posts',
			'pagination' => PaginationHelper::createPaginationLinks($currentPage, $totalPages, '/'),
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
