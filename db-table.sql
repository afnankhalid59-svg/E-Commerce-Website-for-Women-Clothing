SET FOREIGN_KEY_CHECKS=0;
DROP DATABASE IF EXISTS dbrsl;
CREATE DATABASE dbrsl;
USE dbrsl;
CREATE USER IF NOT EXISTS rsluser@localhost IDENTIFIED BY 'rslpass';
GRANT SELECT, INSERT, UPDATE, DELETE on dbrsl.* TO rsluser@localhost;

-- -------------------------------------
-- Table structure for users
-- -------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_city (city)
);

-- ------------------------------------
-- Table structure for `orders`
-- ------------------------------------
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',

    -- Copy from users at time of order
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
-- ---------------------------
-- Table structure for dress
-- ---------------------------
DROP TABLE IF EXISTS dress;
CREATE TABLE dress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    color VARCHAR(100) NOT NULL,
    material VARCHAR(100) DEFAULT NULL,
    base_price DECIMAL(8,2) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE dress_variant (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dress_id INT UNSIGNED NOT NULL,
    size VARCHAR(5) NOT NULL,
    in_stock TINYINT(1) DEFAULT 1,
    stock_quantity INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (dress_id) REFERENCES dress(id) ON DELETE CASCADE,

    UNIQUE (dress_id, size), -- ensures no duplicate combinations
    INDEX idx_product (dress_id),
    INDEX idx_size (size)
);
-- --------------------------------
-- Table structure for order_items
-- --------------------------------
DROP TABLE IF EXISTS order_items;
CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    variant_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (variant_id) REFERENCES dress_variant(id)
) ENGINE=InnoDB;
