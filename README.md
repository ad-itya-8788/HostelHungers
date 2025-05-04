Digital Menu Card System
This is a mini project for a Digital Menu Card System built using HTML, CSS, PHP, and PostgreSQL. It provides a simple platform where users can view the menu items categorized as Veg or Non-Veg, and the admin can easily manage the products by adding or deleting items.

🚀 Project Overview
The Digital Menu Card System is designed for restaurants or cafes to digitize their menu, allowing users to easily browse items. The admin panel allows authorized users to manage the menu by adding or deleting products. There is no update functionality, making it straightforward and efficient.

User Interface
Displays a list of products with:
Product Name
Price
Category (Veg or Non-Veg)
Admin Interface
Add Product: Admins can add new products by entering the product name, price, and category (Veg/Non-Veg).
Delete Product: Admins can remove products from the menu.
💻 Technologies Used
HTML: For creating the web pages.
CSS: For styling the interface.
PHP: For backend logic and server-side processing.
PostgreSQL: For storing product data.




//this is database or procedure 

-- Create category table with primary key and a check on cname
CREATE TABLE category (
    cid INTEGER PRIMARY KEY, 
    cname VARCHAR(10) CHECK (cname IN ('Veg', 'Non_veg'))
);



-- Create menu table with foreign key reference to category table
CREATE TABLE menu (
    mid SERIAL PRIMARY KEY,
    pname VARCHAR(50) NOT NULL UNIQUE,
    cid INTEGER,
    description VARCHAR(100),
    avl BOOLEAN DEFAULT TRUE,
    img VARCHAR(100),
    FOREIGN KEY (cid) REFERENCES category (cid)
);

-- Create size_price table with foreign key reference to menu table
CREATE TABLE size_price (
    sid SERIAL PRIMARY KEY,
    mid INTEGER,
    size VARCHAR(5) NOT NULL CHECK (size IN ('Full', 'Half')),
    price NUMERIC(10, 2) NOT NULL,
    quantity VARCHAR(100) NOT NULL,
    FOREIGN KEY (mid) REFERENCES menu (mid)
);

-- Create procedure to insert menu with category and description
CREATE OR REPLACE PROCEDURE insert_menu(
    IN pname VARCHAR,
    IN cid INTEGER,
    IN description VARCHAR DEFAULT 'No description',
    IN avl BOOLEAN DEFAULT TRUE
) LANGUAGE plpgsql AS $$
BEGIN
    -- Modify the description based on category
    IF cid = 1 THEN
        description := 'Veg: ' || description;
    ELSIF cid = 2 THEN
        description := 'Non-Veg: ' || description;
    ELSE
        RAISE EXCEPTION 'Invalid category ID. Must be 1 (Veg) or 2 (Non-Veg)';
    END IF;

    -- Insert the new menu item
    INSERT INTO menu (pname, cid, description, avl)
    VALUES (pname, cid, description, avl);
END;
$$;
