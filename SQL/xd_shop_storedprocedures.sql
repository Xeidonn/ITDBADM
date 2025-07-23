use xd_shop;

-- Stored Procedure to get products by category
DELIMITER $$
CREATE PROCEDURE get_products_by_category(IN category_name VARCHAR(100))
BEGIN
    SELECT p.product_id, p.name, p.price, p.stock_quantity
    FROM Products p
    JOIN Categories c ON p.category_id = c.category_id
    WHERE c.category_name = category_name;
END;
$$ DELIMITER ;


-- Stored Procedure to get user orders
DELIMITER $$
CREATE PROCEDURE get_user_orders(IN user_id INT)
BEGIN
    SELECT o.order_id, o.order_date, o.total_amount, c.currency_code
    FROM Orders o
    JOIN Currencies c ON o.currency_id = c.currency_id
    WHERE o.user_id = user_id;
END;
$$ DELIMITER ;


-- Stored Procedure to calculate the total amount for an order
DELIMITER $$
CREATE PROCEDURE calculate_order_total(IN order_id INT)
BEGIN
    SELECT SUM(oi.quantity * oi.price) AS total_amount
    FROM Order_Items oi
    WHERE oi.order_id = order_id;
END;
$$ DELIMITER ;


-- Stored Procedure to add a new product
DELIMITER $$
CREATE PROCEDURE add_new_product(IN name VARCHAR(100), IN description TEXT, IN price DECIMAL(10,2), IN stock_quantity INT, IN category_id INT, IN currency_id INT)
BEGIN
    INSERT INTO Products (name, description, price, stock_quantity, category_id, currency_id)
    VALUES (name, description, price, stock_quantity, category_id, currency_id);
END;
$$ DELIMITER ;


-- Stored Procedure to update the status of an order
DELIMITER //
CREATE PROCEDURE update_order_status(IN order_id INT, IN new_status VARCHAR(50))
BEGIN
    UPDATE Orders
    SET order_status = new_status
    WHERE order_id = order_id;
END;
//
DELIMITER ;

-- Get list of all users (excluding passwords)
DELIMITER $$
CREATE PROCEDURE get_all_users()
BEGIN
    SELECT u.user_id, u.username, u.email, u.created_at, r.role_name
    FROM Users u
    JOIN Roles r ON u.role_id = r.role_id;
END;
$$
DELIMITER ;

-- Delete user by ID
DELIMITER $$
CREATE PROCEDURE delete_user_by_id(IN target_user_id INT)
BEGIN
    DELETE FROM Users
    WHERE user_id = target_user_id;
END;
$$
DELIMITER ;






