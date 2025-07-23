use xd_shop;

-- Trigger to update stock quantity after an order is placed
DELIMITER $$
CREATE TRIGGER update_stock_after_order
AFTER INSERT ON Order_Items
FOR EACH ROW
BEGIN
    UPDATE Products
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.product_id;
END;
$$ DELIMITER ;

-- Trigger to prevent negative stock in the Products table
DELIMITER $$
CREATE TRIGGER prevent_negative_stock
BEFORE UPDATE ON Products
FOR EACH ROW
BEGIN
    IF NEW.stock_quantity < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock quantity cannot be negative';
    END IF;
END;
$$ DELIMITER ;

-- Trigger to log transactions
DELIMITER $$
CREATE TRIGGER log_transaction
AFTER INSERT ON Transaction_Log
FOR EACH ROW
BEGIN
    INSERT INTO Transaction_Log (order_id, payment_method, payment_status, amount, timestamp)
    VALUES (NEW.order_id, NEW.payment_method, NEW.payment_status, NEW.amount, NOW());
END;
$$ DELIMITER ;

-- Trigger to set default role for new users
DELIMITER $$
CREATE TRIGGER default_role_for_new_user
BEFORE INSERT ON Users
FOR EACH ROW
BEGIN
    IF NEW.role_id IS NULL THEN
        SET NEW.role_id = 3;  -- Assuming 3 is the 'customer' role_id
    END IF;
END;
$$ DELIMITER ;

-- Trigger to update order status when an order is completed
DELIMITER $$
CREATE TRIGGER update_order_status_after_payment
AFTER UPDATE ON Transaction_Log
FOR EACH ROW
BEGIN
    IF NEW.payment_status = 'paid' THEN
        UPDATE Orders
        SET order_status = 'completed'
        WHERE order_id = NEW.order_id;
    END IF;
END;
$$ DELIMITER ;

DELIMITER $$
CREATE TRIGGER log_deleted_user
BEFORE DELETE ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Deleted_Users (deleted_user_id, username, email)
    VALUES (OLD.user_id, OLD.username, OLD.email);
END;

$$ DELIMITER ;
