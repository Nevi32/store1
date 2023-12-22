-- Create tables

CREATE TABLE main_entry (
    main_entry_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    total_quantity DECIMAL(10,2) NOT NULL,
    quantity_description VARCHAR(255),
    UNIQUE KEY (product_name)
);

CREATE TABLE sales (
    sale_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    main_entry_id INT,
    quantity_sold DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (main_entry_id) REFERENCES main_entry(main_entry_id)
);

CREATE TABLE inventory (
    entry_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    main_entry_id INT,
    quantity DECIMAL(10,2) NOT NULL,
    quantity_description VARCHAR(255),
    price DECIMAL(10,2),
    record_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sale_id INT,
    FOREIGN KEY (main_entry_id) REFERENCES main_entry(main_entry_id),
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id)
);

CREATE TABLE users (
    user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('owner', 'staff') NOT NULL,
    store_name VARCHAR(100) NOT NULL,
    remember_token VARCHAR(255),
    token_expiry INT,
    full_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL
);

-- Create trigger

DELIMITER //

CREATE TRIGGER after_insert_sale
AFTER INSERT ON sales FOR EACH ROW
BEGIN
    -- Subtract the quantity_sold from main_entry
    UPDATE main_entry
    SET total_quantity = total_quantity - NEW.quantity_sold
    WHERE main_entry_id = NEW.main_entry_id;
END;

//

DELIMITER ;

