<?php
	
	namespace App\services;
	
	class BlogDataService
	{
		public function getBlogData($blog, $author)
		{
			$post                = $blog;
			$post['author_name'] = $author['username'];
			$post['image_url']   = $blog['image'] ? "<img width='200' height='200' src='" . 'http://localhost:8000/' . $blog['image'] . "' alt='blog image'>" : "";
			
			return $this->extracted($blog, $post);
		}
		
		public function getBlogDataForDashboard($blog)
		{
			$post              = $blog;
			$post['image_url'] = $blog['image'] ? "<img width='100' height='100' src='" . 'http://localhost:8000/' . $blog['image'] . "' alt='blog image'>" : "";
			$post['content']   = strlen($blog['content']) > 50 ? substr($blog['content'], 0, 50) . "..." : $blog['content'];
			
			return $this->extracted($blog, $post);
		}
		
		/**
		 * @param $blog
		 * @param mixed $post
		 * @return mixed
		 */
		public function extracted($blog, mixed $post): mixed
		{
			$post['status_text']  = $blog['status'] ? 'Published' : 'Draft';
			$post['created_at']   = $blog['created_at'] ? date("d/m/Y h:i A", strtotime($blog['created_at'])) : '';
			$post['updated_at']   = $blog['updated_at'];
			$post['publish_date'] = $post['publish_date'] ? date("d/m/Y h:i A", strtotime($post['publish_date'])) : '';
			
			return $post;
		}
	}