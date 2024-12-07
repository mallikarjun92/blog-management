<?php

namespace App\Models;

use Core\Database;

class Blog
{
    private $db;

    public function __construct()
    {
        // Instantiate the database connection
        $this->db = Database::getInstance();
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
        $sql = "SELECT * FROM blog_posts LIMIT :limit OFFSET :offset";
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
        $sql = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY publish_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
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
        $sql = "SELECT * FROM blog_posts WHERE id = :id AND status = 'published'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new blog post.
     *
     * @param array $data Blog post data (title, content, author_id, etc.).
     * @return bool True if successful, false otherwise.
     */
    public function create($data)
    {
        $sql = "INSERT INTO blog_posts (title, content, author_id, image, status, publish_date)
                VALUES (:title, :content, :author_id, :image, :status, :publish_date)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':content', $data['content']);
        $stmt->bindValue(':author_id', $data['author_id'], \PDO::PARAM_INT);
        $stmt->bindValue(':image', $data['image']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->bindValue(':publish_date', $data['publish_date']);
        return $stmt->execute();
    }

    /**
     * Update an existing blog post.
     *
     * @param int $id Blog post ID.
     * @param array $data Blog post data to update.
     * @return bool True if successful, false otherwise.
     */
    public function update($id, $data)
    {
        $sql = "UPDATE blog_posts
                SET title = :title, content = :content, image = :image, status = :status
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':content', $data['content']);
        $stmt->bindValue(':image', $data['image']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete a blog post by its ID.
     *
     * @param int $id Blog post ID.
     * @return bool True if successful, false otherwise.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM blog_posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
