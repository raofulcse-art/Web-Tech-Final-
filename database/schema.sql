-- ============================================================
-- database/schema.sql
-- Run this in phpMyAdmin to set up the database
-- ============================================================

-- Step 1: Create database
CREATE DATABASE IF NOT EXISTS comment_system;
USE comment_system;

-- Step 2: Users table (for login session simulation)
CREATE TABLE IF NOT EXISTS users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email    VARCHAR(150) NOT NULL UNIQUE,
    role     ENUM('user','admin','author') DEFAULT 'user',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 3: Articles table
CREATE TABLE IF NOT EXISTS articles (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(300) NOT NULL,
    body       TEXT NOT NULL,
    author_id  INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Step 4: Comments table (flat — no nesting, all top-level)
CREATE TABLE IF NOT EXISTS comments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id    INT NOT NULL,
    body       TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
);

-- Step 5: Reported comments table
-- UNIQUE on (comment_id, reported_by) prevents double-reporting
CREATE TABLE IF NOT EXISTS reported_comments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    comment_id  INT NOT NULL,
    reported_by INT NOT NULL,
    reason      TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_report (comment_id, reported_by),
    FOREIGN KEY (comment_id)  REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_by) REFERENCES users(id)    ON DELETE CASCADE
);

-- Step 6: Sample data for testing
INSERT INTO users (username, email, role, password) VALUES
('admin',   'admin@test.com',   'admin',  MD5('admin123')),
('alice',   'alice@test.com',   'author', MD5('alice123')),
('bob',     'bob@test.com',     'user',   MD5('bob123'));

INSERT INTO articles (title, body, author_id) VALUES
('Getting Started with PHP MVC',
 'PHP MVC separates your application into Model, View, and Controller layers...',
 2),
('Understanding AJAX and JSON',
 'AJAX allows web pages to communicate with the server without reloading...',
 2);

INSERT INTO comments (article_id, user_id, body) VALUES
(1, 3, 'Great article! Very helpful for beginners.'),
(1, 1, 'Thanks for this breakdown. Clear explanation.'),
(2, 3, 'AJAX makes so much more sense now.');
