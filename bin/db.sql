CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT NULL
);

CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    image VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'draft',
    publish_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    user_id INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('approved', 'pending') DEFAULT 'pending'
);

ALTER TABLE users
    ADD COLUMN reset_token VARCHAR(45) NULL AFTER last_login,
ADD COLUMN token_expiry DATETIME NULL AFTER reset_token;

ALTER TABLE `blog_posts`
    CHANGE COLUMN `publish_date` `publish_date` DATETIME NULL ;

ALTER TABLE `blog_posts`
    CHANGE COLUMN `status` `status` INT NULL DEFAULT 0 ;
