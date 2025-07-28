use xd_shop;

-- Trigger to log/aduit user details before deletion GUMAGANA
DELIMITER $$
CREATE TRIGGER log_deleted_user
BEFORE DELETE ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Deleted_Users (deleted_user_id, username, email)
    VALUES (OLD.user_id, OLD.username, OLD.email);
END;

$$ DELIMITER ;


-- Trigger para sa transaction log GUMAGANA
DELIMITER $$

CREATE TRIGGER after_transaction_log_insert
AFTER INSERT ON transaction_log
FOR EACH ROW
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_currency_id INT;
    DECLARE v_total_amount DECIMAL(10,2);

    -- Fetch user_id, total_amount, and currency_id from orders
    SELECT user_id, total_amount, currency_id
    INTO v_user_id, v_total_amount, v_currency_id
    FROM orders
    WHERE order_id = NEW.order_id;

    -- Insert into audit log
    INSERT INTO sales_audit_log (
        order_id,
        user_id,
        total_amount,
        currency_id,
        payment_method,
        payment_status
    )
    VALUES (
        NEW.order_id,
        v_user_id,
        v_total_amount,
        v_currency_id,
        NEW.payment_method,
        NEW.payment_status
    );
END $$

DELIMITER ;

-- lahat ng ito ay trigger para sa pag view ng product history hanggang delete GUMAGANA
DELIMITER ;;
CREATE TRIGGER trg_product_insert
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    INSERT INTO product_history (
        product_id, action, name, description, price, stock_quantity, category_id, currency_id
    )
    VALUES (
        NEW.product_id, 'ADD', NEW.name, NEW.description, NEW.price, NEW.stock_quantity, NEW.category_id, NEW.currency_id
    );
END;;
DELIMITER ;


-- GUMAGANA
DELIMITER ;;
CREATE TRIGGER trg_product_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    INSERT INTO product_history (
        product_id, action, name, description, price, stock_quantity, category_id, currency_id
    )
    VALUES (
        NEW.product_id, 'EDIT', NEW.name, NEW.description, NEW.price, NEW.stock_quantity, NEW.category_id, NEW.currency_id
    );
END;;
DELIMITER ;


-- GUMAGANA
DELIMITER ;;
CREATE TRIGGER trg_product_delete
BEFORE DELETE ON products
FOR EACH ROW
BEGIN
    INSERT INTO product_history (
        product_id, action, name, description, price, stock_quantity, category_id, currency_id
    )
    VALUES (
        OLD.product_id, 'DELETE', OLD.name, OLD.description, OLD.price, OLD.stock_quantity, OLD.category_id, OLD.currency_id
    );
END;;
DELIMITER ;
-- end ng 3 to, magkakasama sila