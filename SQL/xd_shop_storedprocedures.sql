use xd_shop;

-- Get list of all users (excluding passwords) GUMAGANA
DELIMITER $$
CREATE PROCEDURE get_all_users()
BEGIN
    SELECT u.user_id, u.username, u.email, u.created_at, r.role_name
    FROM Users u
    JOIN Roles r ON u.role_id = r.role_id;
END;
$$
DELIMITER ;
 
-- Delete user by ID with ACID PROPERTY AND COMMIT ROLLBACK - THIS IS UPDATED ONE GUMAGANA
DELIMITER $$  
CREATE PROCEDURE delete_user_by_id(IN target_user_id INT)  
BEGIN  
    DECLARE EXIT HANDLER FOR SQLEXCEPTION  
    BEGIN  
        ROLLBACK;  
    END;  

    START TRANSACTION;

    -- Prevent deletion of admin users (assuming role_id = 1 = admin)
    IF (SELECT role_id FROM Users WHERE user_id = target_user_id) != 1 THEN
        DELETE FROM Users WHERE user_id = target_user_id;

        -- Log the transaction
        INSERT INTO User_Transaction_Log (user_id, action_type)
        VALUES (target_user_id, 'DELETE_USER');
    END IF;

    COMMIT;  
END;  
$$  
DELIMITER ;


-- stored prcedure for total sales GUMAGANA
DELIMITER $$

CREATE PROCEDURE `get_total_sales`()
BEGIN
    SELECT 
        c.currency_code,
        c.symbol,
        SUM(sal.total_amount) AS total_sales
    FROM sales_audit_log sal
    JOIN currencies c ON sal.currency_id = c.currency_id
    GROUP BY sal.currency_id;
END$$

DELIMITER ;

-- get best seller GUMAGANA
DELIMITER $$
CREATE PROCEDURE get_best_selling_products()
BEGIN
    SELECT 
        p.product_id,
        p.name AS product_name,
        SUM(oi.quantity) AS total_quantity_sold
    FROM 
        order_items oi
    JOIN 
        products p ON oi.product_id = p.product_id
    GROUP BY 
        p.product_id, p.name
    ORDER BY 
        total_quantity_sold DESC;
END $$
DELIMITER ;

CALL get_best_selling_products();

-- search/filter users GUMAGANA
DELIMITER $$
CREATE PROCEDURE get_all_users_filtered(IN search_term VARCHAR(255))
BEGIN
    SELECT u.user_id, u.username, u.email, u.created_at, r.role_name
    FROM Users u
    JOIN Roles r ON u.role_id = r.role_id
    WHERE u.username LIKE search_term OR u.email LIKE search_term;
END$$
DELIMITER ;

