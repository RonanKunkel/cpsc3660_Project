CREATE DATABASE IF NOT EXISTS JonesAutoDB;
USE JonesAutoDB;

CREATE TABLE IF NOT EXISTS customer (
    id INT AUTO_INCREMENT,
    lastName VARCHAR(20) NOT NULL,
    firstName VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female', 'Other'),
    dateOfBirth DATE,
    phone VARCHAR(15) UNIQUE,
    address VARCHAR(50),
    city VARCHAR(20),
    state VARCHAR(20),
    zip VARCHAR(20),

    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS employment_history (
    id INT AUTO_INCREMENT,
    customer_id INT,
    employer VARCHAR(30),
    title VARCHAR(30),
    supervisor VARCHAR(30),
    supervisor_phone VARCHAR(15),
    address VARCHAR(50),
    start_date DATE,

    PRIMARY KEY (id),
    FOREIGN KEY (customer_id) REFERENCES customer(id)
);

CREATE TABLE IF NOT EXISTS vehicle (
    vin CHAR(17),
    make VARCHAR(30) NOT NULL,
    model VARCHAR(30) NOT NULL,
    year SMALLINT NOT NULL,
    color VARCHAR(20),
    interior_color VARCHAR(20),
    miles INT UNSIGNED,
    style ENUM('Coupe', 'Sedan', 'Hatchback', 'Pickup', 'Van', 'SUV', 'Wagon'),
    condition ENUM('Excellent', 'Light Wear', 'Moderate Wear', 'Abused'),

    PRIMARY KEY (vin)
);

CREATE TABLE IF NOT EXISTS employee (
    id INT AUTO_INCREMENT,
    lastName VARCHAR(20) NOT NULL,
    firstName VARCHAR(20) NOT NULL,
    phone VARCHAR(15) UNIQUE,

    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS sale (
    id INT AUTO_INCREMENT,
    vin CHAR(17) NOT NULL,
    customer_id INT NOT NULL,
    employee_id INT NOT NULL,
    sale_date DATE,
    sale_price DECIMAL(10,2),
    down_payment DECIMAL(10,2),
    financed_amount DECIMAL(10,2),
    commission DECIMAL(10,2),

    PRIMARY KEY (id),
    FOREIGN KEY (vin) REFERENCES vehicle(vin),
    FOREIGN KEY (customer_id) REFERENCES customer(id),
    FOREIGN KEY (employee_id) REFERENCES employee(id)
);

CREATE TABLE IF NOT EXISTS warranty_policy (
    id INT AUTO_INCREMENT,
    policy_name VARCHAR(20),
    items_covered VARCHAR(50),

    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS warranty (
    id INT AUTO_INCREMENT,
    warranty_policy_id INT,
    sale_id INT,
    start_date DATE,
    end_date DATE,
    cost DECIMAL(10,2),
    deductible DECIMAL(10,2),

    PRIMARY KEY (id),
    FOREIGN KEY (sale_id) REFERENCES sale(id),
    FOREIGN KEY (warranty_policy_id) REFERENCES warranty_policy(id)
);

CREATE TABLE IF NOT EXISTS purchase (
    id INT AUTO_INCREMENT,
    vin CHAR(17) NOT NULL,
    purchase_date DATE,
    location VARCHAR(50),
    auction VARCHAR(30),
    seller VARCHAR(30),
    price_paid DECIMAL(10,2),

    PRIMARY KEY (id),
    FOREIGN KEY (vin) REFERENCES vehicle(vin)
);

CREATE TABLE IF NOT EXISTS payment (
    id INT AUTO_INCREMENT,
    customer_id INT,
    sale_id INT,
    payment_date DATE,
    due_date DATE,
    paid_date DATE,
    amount DECIMAL(10, 2),
    bank_id INT,

    PRIMARY KEY (id),
    FOREIGN KEY (customer_id) REFERENCES customer(id),
    FOREIGN KEY (sale_id) REFERENCES sale(id)
);

CREATE TABLE IF NOT EXISTS repair (
    id INT AUTO_INCREMENT,
    purchase_id INT,
    description VARCHAR(100),
    estimated_cost DECIMAL(10, 2),
    actual_cost DECIMAL(10, 2),

    PRIMARY KEY (id),
    FOREIGN KEY (purchase_id) REFERENCES purchase(id)
);
