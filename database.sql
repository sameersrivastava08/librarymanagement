-- Corrected database.sql - uses InnoDB and makes user_id nullable for ON DELETE SET NULL

CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_db;

-- users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- members
DROP TABLE IF EXISTS members;
CREATE TABLE members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  membership_number VARCHAR(50) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  membership_start DATE,
  membership_end DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- books
DROP TABLE IF EXISTS books;
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  serial_no VARCHAR(100) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  type ENUM('book','movie') DEFAULT 'book',
  copies INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- transactions (user_id is nullable so ON DELETE SET NULL is valid)
DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  member_id INT NULL,
  book_id INT NOT NULL,
  serial_no VARCHAR(100) NOT NULL,
  issue_date DATE NOT NULL,
  return_date DATE NOT NULL,
  returned_date DATE,
  remarks TEXT,
  fine DECIMAL(8,2) DEFAULT 0,
  fine_paid BOOLEAN DEFAULT FALSE,
  status ENUM('issued','returned') DEFAULT 'issued',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tx_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tx_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
  CONSTRAINT fk_tx_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- seed default users (passwords hashed for demo)
INSERT INTO users (username, password, full_name, role)
VALUES
('admin', '$2y$10$9uE1bU0WZr5g8NQ1t0q5Y.Nbq2p2k0C84m2F8v7p.gwq03z1qgqO6', 'Administrator', 'admin'),
('user', '$2y$10$g2p9vI1mP9k1bq4a0nQ8sOaBxj9b7kD7HRy7D0kQGqXZa0W3zE5s6', 'Standard User', 'user');  