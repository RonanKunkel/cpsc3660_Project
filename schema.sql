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

INSERT INTO customer (firstName, lastName, gender, dateOfBirth, phone, address, city, state, zip) VALUES
    ('Lewis', 'Augustine', 'Male', '1991-07-09', '403-427-0934', '8107 Valley Drive', 'Lethbridge', 'Alberta', 'R4K 0N4'),
    ('Leopoldo', 'Hodge', 'Male', '1997-12-02', '403-4245-3663', '524 Winding Way', 'Lethbridge', 'Alberta', 'L2A 4S7'),
    ('Jennifer', 'Sanford', 'Female', '2003-10-31', '403-478-0662', '287 Myrtle Avenue', 'Coaldale', 'Alberta', 'R8A 2M0'),
    ('Jody', 'Carlson', 'Female', '1989-09-13', '403-295-8760', '654 Washington Street', 'Lethbridge', 'Alberta', 'N5V 6K7'),
    ('Mandy', 'Dunn', 'Male', '1995-11-11', '403-432-2665', '2 School Street', 'Taber', 'Alberta', 'S2V 9J5'),
    ('Patty', 'Murillo', 'Female', '2005-02-23', '403-457-4555', '99 Glenwood Avenue', 'Brooks', 'Alberta', 'A2H 6G2'),
    ('Rodger', 'Alvarez', 'Male', '1992-07-19', '403-449-1161', '7338 Forest Drive', 'Lethbridge', 'Alberta', 'E4B 1H2'),
    ('Cleo', 'Love', 'Female', '2002-05-28', '403-244-9642', '7980 Sherman Street', 'Lethbridge', 'Alberta', 'M6C 5J8'),
    ('Maxine', 'Nguyen', 'Female', '2000-02-20', '403-672-3641', '755 Madison Court', 'Magrath', 'Alberta', 'H0H 3M5'),
    ('Jarrett', 'Glenn', 'Male', '2007-09-07', '403-245-1935', '6 James Street', 'Lethbridge', 'Alberta', 'N8H 1J8');

INSERT INTO vehicle (vin, make, model, year, color, interior_color, miles, style, condition) VALUES
    ('KL1TD56E59B639081', 'BMW', '328i xDrive', 2011, 'Alpine White', 'Brown', 190000, 'Wagon', 'Light Wear'),
    ('1XKAD29X8KJ533154', 'Ford', 'Taurus', 2006, 'Light Brown', 'Brown', 225000, 'Sedan', 'Moderate Wear'),
    ('JN1BV7AP9EM658367', 'Mitsubishi', 'Lancer SE', 2011, 'Silver', 'Grey', 195000, 'Sedan', 'Light Wear'),
    ('WDDKJ7CB3AF093094', 'BMW', '328d xDrive', 2015, 'Glacier Silver', 'Black', 190000, 'Sedan', 'Light Wear'),
    ('1FDAF56P66EC10260', 'Mercedes-Benz', 'C400 4MATIC', 2015, 'Silver', 'White', 90000, 'Sedan', 'Excellent'),
    ('3VWSS29M51M026061', 'Dodge', 'Ram 3500', 2002, 'White', 'Black', 250000, 'Pickup', 'Moderate Wear'),
    ('3MZBM1L73EM140749', 'BMW', 'X5 M', 2013, 'Black', 'Black', 150000, 'SUV', 'Light Wear'),
    ('KNDJF723067190739', 'Chevrolet', 'Camaro RWD V6', 2012, 'Red', 'Black', 170000, 'Coupe', 'Light Wear'),
    ('JN1CV6AR8BM445759', 'Toyota', 'Camry 3.5', 2008, 'Tan', 'Brown', 140000, 'Sedan', 'Light Wear'),
    ('3LNHM28T47R679651', 'Audi', 'A5 S Line Quattro', 2014, 'Black', 'Black', 205000, 'Coupe', 'Light Wear'),
    ('WVWDB7AJ1EW026333', 'Honda', 'Accord', 2007, 'Silver', 'Black', 190000, 'Coupe', 'Light Wear'),
    ('JF2GPACC8F8202795', 'Audi', 'Q7', 2007, 'Silver', 'Black', 265000, 'SUV', 'Moderate Wear'),
    ('1GTR2VE7XDZ216668', 'Toyota', '4Runner', 1999, 'White', 'Black', 225000, 'SUV', 'Moderate Wear'),
    ('WAUAF78E58A025863', 'Cadillac', 'Escalade', 2010, 'White', 'Brown', 185000, 'SUV', 'Light Wear'),
    ('1GKLVNED8AJ284520', 'Infiniti', 'G37s', 2008, 'Grey', 'Black', 185000, 'Coupe', 'Light Wear');

INSERT INTO employee (firstName, lastName, phone) VALUES
    ('Alphonso', 'Mccarthy', '403-800-9225'),
    ('Cara', 'Park', '403-453-1515'),
    ('Samual', 'Lyons', '403-201-6427'),
    ('Kieth', 'Parrish', '403-226-7665'),
    ('Son', 'Rowland', '403-451-9387'),
    ('Walter', 'Costa', '403-761-3061');

INSERT INTO purchase (vin, purchase_date, location, auction, seller, price_paid) VALUES
    ('KL1TD56E59B639081', '2025-01-15', 'Calgary Auction House', 'Copart', 'Jane Smith', 4500.00),
    ('1XKAD29X8KJ533154', '2025-02-20', 'Calgary Auto Auction', 'IAA', 'Robert Johnson', 550.00),
    ('JN1BV7AP9EM658367', '2025-03-10', 'Lethbridge Motors', 'Copart', 'Mary Williams', 4000.00),
    ('WDDKJ7CB3AF093094', '2025-04-05', 'Lethbridge Auction House', 'Manheim', 'David Brown', 12500.00),
    ('1FDAF56P66EC10260', '2025-05-12', 'Red Deer Auction', 'IAA', 'Sarah Davis', 15300.00),
    ('3VWSS29M51M026061', '2025-06-18', 'Lethbridge Auto Auction', 'Copart', 'Michael Wilson', 5200.00),
    ('3MZBM1L73EM140749', '2025-07-22', 'Calgary Auction House', 'IAA', 'Jennifer Garcia', 9100.00),
    ('KNDJF723067190739', '2025-08-14', 'Lethbridge Motors', 'Manheim', 'Richard Martinez', 8600.00),
    ('JN1CV6AR8BM445759', '2025-09-09', 'Edmonton Auto Auction', 'Copart', 'Linda Anderson', 5000.00),
    ('3LNHM28T47R679651', '2025-10-16', 'Calgary Auction House', 'IAA', 'William Taylor', 9500.00),
    ('WVWDB7AJ1EW026333', '2025-11-11', 'Red Deer Auction', 'Copart', 'Patricia Thomas', 2400.00),
    ('JF2GPACC8F8202795', '2025-12-08', 'Edmonton Auto Auction', 'Manheim', 'James Jackson', 6700.00),
    ('1GTR2VE7XDZ216668', '2026-01-20', 'Calgary Auction House', 'Copart', 'Barbara White', 15000.00),
    ('WAUAF78E58A025863', '2026-02-14', 'Lethbridge Motors', 'IAA', 'Joseph Harris', 16000.00),
    ('1GKLVNED8AJ284520', '2026-03-19', 'Edmonton Auto Auction', 'Copart', 'Susan Martin', 4200.00);

INSERT INTO sale (vin, customer_id, employee_id, sale_date, sale_price, down_payment, financed_amount, commission) VALUES
    ('KL1TD56E59B639081', 1, 1, '2025-03-17', 7999.99, 500.00, 7499.99, 400.00),
    ('1XKAD29X8KJ533154', 2, 3, '2025-03-28', 3999.99, 1000.00, 2999.99, 200.00),
    ('JN1BV7AP9EM658367', 3, 2, '2025-04-02', 9999.99, 4000.00, 5999.99, 640.00),
    ('WDDKJ7CB3AF093094', 4, 4, '2025-04-12', 15999.99, 6000.00, 9999.99, 920.00),
    ('1FDAF56P66EC10260', 5, 5, '2025-06-12', 21999.99, 8000.00, 13999.99, 1120.00),
    ('3VWSS29M51M026061', 6, 5, '2025-07-23', 7999.99, 2000.00, 5999.99, 400.00),
    ('3MZBM1L73EM140749', 7, 1, '2025-08-30', 12999.99, 1000.00, 11999.99, 700.00),
    ('KNDJF723067190739', 8, 3, '2025-09-16', 11999.99, 5500.00, 6499.99, 880.00),
    ('JN1CV6AR8BM445759', 9, 2, '2025-10-03', 8999.99, 2000.00, 6999.99, 480.00),
    ('3LNHM28T47R679651', 10, 1, '2025-12-24', 11999.99, 4000.00, 7999.99, 640.00);
