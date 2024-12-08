<?php
	
	namespace App\controllers;
	
	use App\Models\Blog;
	use App\Models\User;
	use App\services\BlogDataService;
	use Core\BaseController;
	use Core\Form;
	use Core\Request;
	use Core\Session;
	
	class PostController extends BaseController
	{
		public function index(Request $request)
		{
			$postsModel = new Blog();
			$posts = $postsModel->getAll();
			
			foreach ($posts as &$post) {
				$post = (new BlogDataService())->getBlogDataForDashboard($post);
			}
			
			$this->render('admin/posts.html', [
				'posts' => $posts,
				'title' => 'Blog Management',
			]);
		}
		
		public function create(Request $request): void
		{
			$form = new Form();
			
			$form->setMethod('post');
			$form->setAction('/admin/post/create');
			$form->addField('text', 'title', 'Post Title', null, ['required' => true]);
			$form->addField('textarea', 'content', 'Post Content', null, ['required' => true]);
			$form->addField('file', 'image', 'Post Image');
			
			$form->setSubmitButtonName('Post');
			
			$form->handle($request);
			
			if ($form->isSubmitted() && $form->validate($request))
			{
				$formData = $form->getFormData();
				
				$blog = new Blog();
				
				$blog->create(
					[
						'title'   => $formData['title'],
						'content' => $formData['content'],
						'image'   => $formData['image'],
						'author_id'  => Session::get('admin'),
					]);
				
				$form->clearForm($request);
				
				Session::set('message', 'Post has been created');
				
			}
			
			$this->render(
				'admin/manage_post.html', [
				'form'   => $form->render(),
				'title'  => 'Create Post',
				'errors' => $form->getErrors()
			]);
		}
		
		public function edit(Request $request, $id): void
		{
			$form = new Form();
			
			// Set form method and action for update
			$form->setMethod('post');
			$form->setAction("/admin/post/edit/$id");
			
			// Get existing post data for the form
			$blogModel = new Blog();
			$post      = $blogModel->getById($id);
			
			// If post doesn't exist, redirect or show error
			if (!$post) {
				Session::set('error', 'Post not found');
				$this->redirect('/admin/posts');
				return;
			}
			
			// Pre-fill form fields with the existing data
			$form->addField('text', 'title', 'Post Title', $post['title'], ['required' => true]);
			$form->addField('textarea', 'content', 'Post Content', $post['content'], ['required' => true]);
			$form->addField('file', 'image', 'Post Image'); // Optional, since the image may or may not be updated
			
			// Set submit button for the form
			$form->setSubmitButtonName('Update Post');
			
			// Handle form submission
			$form->handle($request);
			
			if ($form->isSubmitted() && $form->validate($request)) {
				// Get the form data
				$formData = $form->getFormData();
				
				// Prepare the data for updating
				$updateData = [
					'title'     => $formData['title'],
					'content'   => $formData['content'],
					'image'     => isset($formData['image']) ? $formData['image'] : $post['image'], // Use existing image if no new image is uploaded
					'author_id' => Session::get('admin'),
				];
				
				// Update the blog post in the database
				$blogModel->update($id, $updateData);
				
				// Clear the form after successful submission
				$form->clearForm($request);
				
				// Set success message
				Session::set('message', 'Post has been updated');
				
				// Redirect to manage posts
				$this->redirect('/admin/posts');
			}
			
			// Render the update form with errors, if any
			$this->render(
				'admin/manage_post.html', [
				'form'   => $form->render(),
				'title'  => 'Update Post',
				'errors' => $form->getErrors(),
				'post'   => $post
			]);
		}
		
		public function delete(Request $request, $id)
		{
			$blogModel = new Blog();
			$post      = $blogModel->getById($id);
			
			if (!$post) {
				$this->redirect('/admin/posts');
			} else {
				$blogModel->delete($id);
			}
			
			Session::set('message', 'Post has been deleted');
			$this->redirect('/admin/posts');
			
		}
		
		public function show(Request $request, $id): void
		{
			$blogModel = new Blog();
			$post      = $blogModel->getById($id);
			
			$userModel = new User();
			$author = $userModel->getById($post['author_id']);
			
			$blogDataService = new BlogDataService();
			
			$data = $blogDataService->getBlogData($post, $author);
			
			$placeholders = [
				'title' => 'View Post',
				'data'  => [$data],
			];
			if (!$post) {
				
				Session::set('error', 'Post not found');
				$this->redirect('/admin/posts');
				
			} else {
				
				$this->render(
					'admin/post.html',
					$placeholders
				);
			}
		}
		
	}