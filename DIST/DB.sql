CREATE DATABASE itsa;

USE itsa;


CREATE TABLE order_status (
                order_status_id INT AUTO_INCREMENT NOT NULL,
                description VARCHAR(50) NOT NULL,
                PRIMARY KEY (order_status_id)
);


CREATE TABLE companies (
                company_id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(200) NOT NULL,
                PRIMARY KEY (company_id)
);


CREATE TABLE stores (
                store_id INT AUTO_INCREMENT NOT NULL,
                company_id INT NOT NULL,
                name VARCHAR(200) NOT NULL,
                short_name VARCHAR(100) NOT NULL,
                PRIMARY KEY (store_id)
);


CREATE TABLE users (
                user_id INT AUTO_INCREMENT NOT NULL,
                store_id INT NULL,
                name VARCHAR(200) NOT NULL,
                login VARCHAR(50) NOT NULL,
                passwd VARCHAR(32) NOT NULL,
                admin BOOLEAN NOT NULL,
                PRIMARY KEY (user_id)
);


CREATE TABLE orders (
                order_id INT AUTO_INCREMENT NOT NULL,
                user_id INT NOT NULL,
                store_id INT NOT NULL,
                order_status_id INT NOT NULL,
                date DATETIME NOT NULL,
                client_name VARCHAR(200) NOT NULL,
                po_number VARCHAR(100) NOT NULL,
                comments VARCHAR(255),
                delivery_date DATE NOT NULL,
                PRIMARY KEY (order_id)
);


CREATE TABLE products (
                product_id INT AUTO_INCREMENT NOT NULL,
                company_id INT NOT NULL,
                description VARCHAR(100) NOT NULL,
                photo_path VARCHAR(200) NOT NULL,
                available TINYINT DEFAULT 0 NOT NULL,
                PRIMARY KEY (product_id)
);


CREATE TABLE order_items (
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                PRIMARY KEY (order_id, product_id)
);


ALTER TABLE orders ADD CONSTRAINT order_status_order_fk
FOREIGN KEY (order_status_id)
REFERENCES order_status (order_status_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE stores ADD CONSTRAINT companies_stores_fk
FOREIGN KEY (company_id)
REFERENCES companies (company_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE users ADD CONSTRAINT stores_users_fk
FOREIGN KEY (store_id)
REFERENCES stores (store_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE orders ADD CONSTRAINT stores_orders_fk
FOREIGN KEY (store_id)
REFERENCES stores (store_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE orders ADD CONSTRAINT users_order_fk
FOREIGN KEY (user_id)
REFERENCES users (user_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE products ADD CONSTRAINT companies_products_fk
FOREIGN KEY (company_id)
REFERENCES companies (company_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE order_items ADD CONSTRAINT order_order_items_fk
FOREIGN KEY (order_id)
REFERENCES orders (order_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE order_items ADD CONSTRAINT products_order_items_fk
FOREIGN KEY (product_id)
REFERENCES products (product_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

INSERT INTO order_status (description) VALUES
    ('Not Started'),
    ('In Progress'),
    ('Canceled'),
    ('Completed');