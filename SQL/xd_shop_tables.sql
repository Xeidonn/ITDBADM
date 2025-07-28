create schema xd_shop;

use xd_shop;

-- Roles Table
CREATE TABLE Roles (
    role_id INT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE -- e.g., admin, staff, customer
);

INSERT INTO Roles (role_id, role_name) VALUES
(1, 'admin'),
(2, 'staff'),
(3, 'customer');

select*from Users;

-- Users Table
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES Roles(role_id)
);

-- Categories Table (e.g., Pokémon, NBA, etc.)
CREATE TABLE Categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO Categories (category_name) 
VALUES ('Pokemon'), ('NBA'), ('One Piece');


-- Currencies Table
CREATE TABLE Currencies (
    currency_id INT PRIMARY KEY AUTO_INCREMENT,
    currency_code VARCHAR(10) NOT NULL, -- e.g., PHP, USD
    symbol VARCHAR(5) NOT NULL,
    exchange_rate_to_usd DECIMAL(10,4) NOT NULL
);

INSERT INTO Currencies (currency_code, symbol, exchange_rate_to_usd)
VALUES 
('PHP', '₱', 1), 
('USD', '$', 1), 
('KRW', '₩', 1);

-- do this for curreny table
ALTER TABLE Currencies
DROP COLUMN exchange_rate_to_usd;

INSERT INTO Currencies (currency_code, symbol)
VALUES 
('JPY', '¥'),
('EUR', '€');


-- Products Table
CREATE TABLE Products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL,
    category_id INT,
    currency_id INT,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id),
    FOREIGN KEY (currency_id) REFERENCES Currencies(currency_id)
);

-- Orders Table
CREATE TABLE Orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    currency_id INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (currency_id) REFERENCES Currencies(currency_id)
);

-- Order Items Table
CREATE TABLE Order_Items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Transaction Log Table
CREATE TABLE Transaction_Log (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    payment_method VARCHAR(50),
    payment_status VARCHAR(50),
    amount DECIMAL(10,2),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id)
);
-- PAKI GAWA TO PARA SA TRANSACTION_LOG TABLE
ALTER TABLE transaction_log
ADD COLUMN currency_id INT,
ADD CONSTRAINT fk_transaction_currency FOREIGN KEY (currency_id) REFERENCES currencies(currency_id);

-- Account Audit History Table
CREATE TABLE Audit_Deleted_Users (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    deleted_user_id INT,
    username VARCHAR(50),
    email VARCHAR(100),
    deleted_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
show tables;

-- Account Deletion Log Table for Admin Side
CREATE TABLE User_Transaction_Log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action_type VARCHAR(50),
    action_timestamp DATETIME DEFAULT NOW()
);


-- table for total sales audit admin side
CREATE TABLE sales_audit_log (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    user_id INT,
    total_amount DECIMAL(10,2),
    currency_id INT,
    payment_method VARCHAR(50),
    payment_status VARCHAR(50),
    audit_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (currency_id) REFERENCES currencies(currency_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
-- table for product history - add - delete - update
CREATE TABLE product_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    action ENUM('ADD', 'EDIT', 'DELETE') NOT NULL,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    stock_quantity INT,
    category_id INT,
    currency_id INT,
    action_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
