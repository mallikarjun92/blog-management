<?php
	
	namespace App\Models;
	
	use Core\Model;
	
	class Blog extends Model
	{
		
		const BLOG_STATUS = [
			'draft'     => 0,
			'published' => 1,
		];
		
		protected $table = "blog_posts";
		
		public function __construct()
		{
			parent::__construct($this->table);
		}
		
		/**
		 * Fetch all the blog posts
		 *
		 * @param int $limit Number of posts per page.
		 * @param int $offset Offset for pagination.
		 * @return array List of blog posts.
		 */
		public function getAll($limit = 10, $offset = 0)
		{
			$sql  = "SELECT * FROM blog_posts LIMIT :limit OFFSET :offset";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
			$stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
			$stmt->execute();
			
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		
		
		/**
		 * Fetch all published blog posts with pagination.
		 *
		 * @param int $limit Number of posts per page.
		 * @param int $offset Offset for pagination.
		 * @return array List of blog posts.
		 */
		public function getAllPublished($limit = 10, $offset = 0)
		{
			$sql  = "SELECT * FROM blog_posts WHERE status = :status ORDER BY publish_date DESC LIMIT :limit OFFSET :offset";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
			$stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
			$stmt->bindValue(':status', self::BLOG_STATUS['published'], \PDO::PARAM_INT);
			$stmt->execute();
			
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		
		/**
		 * Fetch a single blog post by its ID.
		 *
		 * @param int $id Blog post ID.
		 * @return array|null Blog post data or null if not found.
		 */
		public function getById($id)
		{
			$sql  = "SELECT * FROM blog_posts WHERE id = :id AND status = 'published'";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':id', $id, \PDO::PARAM_INT);
			$stmt->execute();
			
			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}
	}
