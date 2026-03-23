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
    vehicle_condition ENUM('Excellent', 'Light Wear', 'Moderate Wear', 'Abused'),
    book_price DECIMAL(10,2),

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

CREATE TABLE IF NOT EXISTS warranty (
    id INT AUTO_INCREMENT,
    sale_id INT,
    start_date DATE,
    end_date DATE,
    policy_name VARCHAR(20),
    items_covered VARCHAR(50),
    cost DECIMAL(10,2),
    monthly_cost DECIMAL(10,2),
    deductible DECIMAL(10,2),

    PRIMARY KEY (id),
    FOREIGN KEY (sale_id) REFERENCES sale(id)
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
    due INT,
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

INSERT INTO vehicle (vin, make, model, year, color, interior_color, miles, style, vehicle_condition, book_price) VALUES
    ('KL1TD56E59B639081', 'BMW', '328i xDrive', 2011, 'Alpine White', 'Brown', 190000, 'Wagon', 'Light Wear', 8500.00),
    ('1XKAD29X8KJ533154', 'Ford', 'Taurus', 2006, 'Light Brown', 'Brown', 225000, 'Sedan', 'Moderate Wear', 3200.00),
    ('JN1BV7AP9EM658367', 'Mitsubishi', 'Lancer SE', 2011, 'Silver', 'Grey', 195000, 'Sedan', 'Light Wear', 6800.00),
    ('WDDKJ7CB3AF093094', 'BMW', '328d xDrive', 2015, 'Glacier Silver', 'Black', 190000, 'Sedan', 'Light Wear', 12000.00),
    ('1FDAF56P66EC10260', 'Mercedes-Benz', 'C400 4MATIC', 2015, 'Silver', 'White', 90000, 'Sedan', 'Excellent', 18500.00),
    ('3VWSS29M51M026061', 'Dodge', 'Ram 3500', 2002, 'White', 'Black', 250000, 'Pickup', 'Moderate Wear', 5200.00),
    ('3MZBM1L73EM140749', 'BMW', 'X5 M', 2013, 'Black', 'Black', 150000, 'SUV', 'Light Wear', 15000.00),
    ('KNDJF723067190739', 'Chevrolet', 'Camaro RWD V6', 2012, 'Red', 'Black', 170000, 'Coupe', 'Light Wear', 11200.00),
    ('JN1CV6AR8BM445759', 'Toyota', 'Camry 3.5', 2008, 'Tan', 'Brown', 140000, 'Sedan', 'Light Wear', 7500.00),
    ('3LNHM28T47R679651', 'Audi', 'A5 S Line Quattro', 2014, 'Black', 'Black', 205000, 'Coupe', 'Light Wear', 13000.00),
    ('WVWDB7AJ1EW026333', 'Honda', 'Accord', 2007, 'Silver', 'Black', 190000, 'Coupe', 'Light Wear', 5500.00),
    ('JF2GPACC8F8202795', 'Audi', 'Q7', 2007, 'Silver', 'Black', 265000, 'SUV', 'Moderate Wear', 9000.00),
    ('1GTR2VE7XDZ216668', 'Toyota', '4Runner', 1999, 'White', 'Black', 225000, 'SUV', 'Moderate Wear', 6500.00),
    ('WAUAF78E58A025863', 'Cadillac', 'Escalade', 2010, 'White', 'Brown', 185000, 'SUV', 'Light Wear', 14000.00),
    ('1GKLVNED8AJ284520', 'Infiniti', 'G37s', 2008, 'Grey', 'Black', 185000, 'Coupe', 'Light Wear', 10000.00);

INSERT INTO purchase (vin, purchase_date, location, auction, seller, price_paid) VALUES
    ('KL1TD56E59B639081', '2025-10-15', 'Calgary Auction House', 'Copart', 'Jane Smith', 4500.00),
    ('1XKAD29X8KJ533154', '2025-10-20', 'Calgary Auto Auction', 'IAA', 'Robert Johnson', 550.00),
    ('JN1BV7AP9EM658367', '2025-11-10', 'Lethbridge Motors', 'Copart', 'Mary Williams', 4000.00),
    ('WDDKJ7CB3AF093094', '2025-12-05', 'Lethbridge Auction House', 'Manheim', 'David Brown', 12500.00),
    ('1FDAF56P66EC10260', '2025-12-12', 'Red Deer Auction', 'IAA', 'Sarah Davis', 15300.00),
    ('3VWSS29M51M026061', '2025-12-18', 'Lethbridge Auto Auction', 'Copart', 'Michael Wilson', 5200.00),
    ('3MZBM1L73EM140749', '2026-01-05', 'Lethbridge Motors', 'Manheim', 'Richard Martinez', 9100.00),
    ('KNDJF723067190739', '2026-01-05', 'Lethbridge Motors', 'Manheim', 'Richard Martinez', 8600.00),
    ('JN1CV6AR8BM445759', '2026-01-16', 'Edmonton Auto Auction', 'Copart', 'Linda Anderson', 5000.00),
    ('3LNHM28T47R679651', '2026-01-20', 'Calgary Auction House', 'IAA', 'William Taylor', 9500.00),
    ('WVWDB7AJ1EW026333', '2026-02-23', 'Red Deer Auction', 'Copart', 'Patricia Thomas', 2400.00),
    ('JF2GPACC8F8202795', '2026-02-25', 'Edmonton Auto Auction', 'Manheim', 'James Jackson', 6700.00),
    ('1GTR2VE7XDZ216668', '2026-02-28', 'Calgary Auction House', 'Copart', 'Barbara White', 15000.00),
    ('WAUAF78E58A025863', '2026-02-28', 'Lethbridge Motors', 'IAA', 'Joseph Harris', 16000.00),
    ('1GKLVNED8AJ284520', '2026-03-05', 'Edmonton Auto Auction', 'Copart', 'Susan Martin', 4200.00);

INSERT INTO repair (purchase_id, description, estimated_cost, actual_cost) VALUES
    (1, 'Valve Cover Gasket Leak', 200.00, 250.00),
    (1, 'Power Steering Pump Replacement', 400.00, 400.00),
    (1, 'Water Pump and Thermostat Replacement', 700.00, 820.00),
    (2, 'Transmission Fluid and Filter Change', 150.00, 150.00),
    (2, 'Brake Pad and Rotor Replacement', 500.00, 575.00),
    (3, 'Spark Plugs and Ignition Coils', 300.00, 350.00),
    (3, 'Drive Belts Replacement', 200.00, 200.00),
    (4, 'Diesel Fuel Filter Replacement', 100.00, 120.00),
    (4, 'Engine Oil and Filter Change', 75.00, 75.00),
    (5, 'Air Conditioning Compressor Sealing', 250.00, 250.00),
    (6, 'Clutch Plate and Flywheel Replacement', 800.00, 950.00),
    (6, 'Rear Suspension Bushing Replacement', 600.00, 650.00),
    (7, 'Cabin Air Filter and Engine Air Filter', 120.00, 150.00),
    (8, 'Alternator Replacement', 350.00, 400.00),
    (8, 'Battery Replacement', 200.00, 225.00),
    (9, 'Coolant System Flush and Refill', 180.00, 180.00),
    (9, 'Wheel Alignment', 150.00, 180.00),
    (10, 'Brake Fluid Bleed and Line Check', 200.00, 250.00),
    (10, 'Suspension Shock Absorber Replacement', 400.00, 450.00),
    (11, 'Timing Chain Inspection and Adjustment', 350.00, 400.00),
    (12, 'Transmission Cooler Cleaning', 300.00, 350.00),
    (12, 'Engine Valve Cleaning', 400.00, 500.00),
    (13, 'Spark Plugs and Wires Replacement', 250.00, 300.00),
    (13, 'Radiator Flush and Core Repair', 350.00, 425.00),
    (14, 'Differential Service', 200.00, 225.00),
    (15, 'Fuel Injector Cleaning', 250.00, 300.00);

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

INSERT INTO employee (firstName, lastName, phone) VALUES
    ('Alphonso', 'Mccarthy', '403-800-9225'),
    ('Cara', 'Park', '403-453-1515'),
    ('Samual', 'Lyons', '403-201-6427'),
    ('Kieth', 'Parrish', '403-226-7665'),
    ('Son', 'Rowland', '403-451-9387'),
    ('Walter', 'Costa', '403-761-3061');

INSERT INTO sale (vin, customer_id, employee_id, sale_date, sale_price, down_payment, financed_amount, commission) VALUES
    ('KL1TD56E59B639081', 1, 1, '2025-12-17', 7999.99, 500.00, 7499.99, 400.00),
    ('1XKAD29X8KJ533154', 2, 3, '2025-12-28', 3999.99, 1000.00, 2999.99, 200.00),
    ('JN1BV7AP9EM658367', 3, 2, '2026-01-02', 9999.99, 4000.00, 5999.99, 640.00),
    ('WDDKJ7CB3AF093094', 4, 4, '2026-01-12', 15999.99, 6000.00, 9999.99, 920.00),
    ('1FDAF56P66EC10260', 5, 5, '2026-02-05', 21999.99, 8000.00, 13999.99, 1120.00),
    ('3VWSS29M51M026061', 6, 5, '2026-02-12', 7999.99, 2000.00, 5999.99, 400.00),
    ('3MZBM1L73EM140749', 7, 1, '2026-02-22', 12999.99, 1000.00, 11999.99, 700.00),
    ('KNDJF723067190739', 8, 3, '2026-03-02', 11999.99, 5500.00, 6499.99, 880.00),
    ('JN1CV6AR8BM445759', 9, 2, '2026-03-12', 8999.99, 2000.00, 6999.99, 480.00),
    ('3LNHM28T47R679651', 10, 1, '2026-03-20', 11999.99, 4000.00, 7999.99, 640.00);

INSERT INTO warranty (sale_id, start_date, end_date, policy_name, items_covered, cost, monthly_cost, deductible) VALUES
    (1, '2025-12-17', '2028-12-17', 'Comprehensive', 'Engine, Transmission, Suspension', 999.00, 27.75, 250.00),
    (2, '2025-12-28', '2027-12-28', 'Basic', 'Engine, Transmission', 599.00, 24.96, 500.00),
    (3, '2026-01-02', '2029-01-02', 'Comprehensive', 'Engine, Transmission, Suspension, Electrical', 1299.00, 36.08, 250.00),
    (4, '2026-01-12', '2029-01-12', 'Premium', 'Engine, Transmission, Suspension, Electrical, A/C', 1599.00, 44.42, 200.00),
    (5, '2026-02-05', '2029-02-05', 'Premium', 'Engine, Transmission, Suspension, Electrical, A/C, Paint', 1999.00, 55.53, 150.00),
    (6, '2026-02-12', '2028-02-12', 'Basic', 'Engine, Transmission', 799.00, 33.29, 500.00),
    (7, '2026-02-22', '2029-02-22', 'Comprehensive', 'Engine, Transmission, Suspension, Electrical', 1099.00, 30.53, 300.00),
    (8, '2026-03-02', '2029-03-02', 'Comprehensive', 'Engine, Transmission, Suspension, Electrical, A/C', 1399.00, 38.86, 250.00),
    (9, '2026-03-12', '2028-03-12', 'Basic', 'Engine, Transmission', 699.00, 29.13, 500.00),
    (10, '2026-03-20', '2029-03-20', 'Premium', 'Engine, Transmission, Suspension, Electrical, A/C', 999.00, 27.75, 250.00);

INSERT INTO employment_history (customer_id, employer, title, supervisor, supervisor_phone, address, start_date) VALUES
    (1, 'McDonalds', 'Burger Flipper', 'Joe Biden', '403-295-0954', '9735 Elm Street, Toronto, ON', '2008-09-12'),
    (1, 'Torxen', 'Roughneck', 'Dale Morrison', '403-246-5353', '9 Route 27, Calgary, AB', '2009-10-02'),
    (1, 'Volker Stevin', 'Highway Maintenance', 'Douglas Miller', '403-938-7444', '7175 12th Street, Calgary, AB', '2015-04-25'),
    (1, 'Stephensons Services', 'Heavy Duty Mechanic', 'Harry Whiteman', '403-458-3466', '7370 Oxford Court, Lethbridge, AB', '2021-11-15'),
    (2, 'Subway', 'Sandwich Artist', 'Maria Garcia', '403-246-1234', '456 Downtown St, Calgary, AB', '2016-06-15'),
    (2, 'Home Depot', 'Sales Associate', 'Bob Thompson', '403-245-9876', '1200 Mayor Magrath Drive, Lethbridge, AB', '2018-03-20'),
    (2, 'Rogers Communications', 'Customer Service Rep', 'Sarah Johnson', '403-327-5555', '200 Main Street, Lethbridge, AB', '2020-09-10'),
    (3, 'Starbucks', 'Barista', 'Emily Chen', '403-884-1111', '123 Main Ave, Coaldale, AB', '2022-01-15'),
    (3, 'Walmart', 'Cashier', 'David Lee', '403-883-2222', '456 First Street, Coaldale, AB', '2023-06-01'),
    (4, 'Kelton Metals', 'Assembler', 'Richard White', '403-328-4444', '2000 Industrial Blvd, Calgary, AB', '2005-08-10'),
    (4, 'Transcanada', 'Inspector', 'Michael Brown', '403-293-5555', '500 Centre Street, Calgary, AB', '2010-02-15'),
    (4, 'Genesis Fabrication', 'Supervisor', 'James Wilson', '403-320-6666', '8200 Industrial Ave, Lethbridge, AB', '2016-11-20'),
    (4, 'Prime Foods', 'Warehouse Manager', 'Linda Martinez', '403-327-1111', '5500 Mayor Magrath Drive, Lethbridge, AB', '2022-01-15'),
    (5, 'Tim Hortons', 'Service Team Member', 'Jennifer Roberts', '403-223-3333', '100 Main Street, Taber, AB', '2013-05-20'),
    (5, 'Canadian Tire', 'Assistant Manager', 'Robert Taylor', '403-223-4444', '200 North Railway Street, Taber, AB', '2019-07-10'),
    (5, 'Suncor Energy', 'Operations Specialist', 'Patricia Davis', '403-223-5555', '300 Railway Ave, Taber, AB', '2023-02-01'),
    (6, 'McDonald''s', 'Crew Member', 'Kevin Young', '403-362-1111', '500 Industrial Ave, Brooks, AB', '2023-08-15'),
    (6, 'Servus Credit Union', 'Teller', 'Angela Brown', '403-362-2222', '123 Main Street, Brooks, AB', '2024-06-01'),
    (7, 'Alberta Health Services', 'Maintenance Technician', 'George Hall', '403-388-1111', '960 Bowes Street, Lethbridge, AB', '2010-09-15'),
    (7, 'Fortis Alberta', 'Field Technician', 'Steven Parks', '403-329-2222', '1500 Mayor Magrath Drive, Lethbridge, AB', '2014-04-20'),
    (7, 'Gibson Energy', 'Equipment Operator', 'Frank Anderson', '403-320-3333', '2000 Veterans Drive, Lethbridge, AB', '2018-11-10'),
    (7, 'Perfect Image Janitorial', 'Supervisor', 'Nancy Thompson', '403-330-4444', '3500 12th Avenue South, Lethbridge, AB', '2022-05-01'),
    (8, 'Winners', 'Sales Associate', 'Michelle Green', '403-328-1111', '200 Mayor Magrath Drive, Lethbridge, AB', '2019-09-10'),
    (8, 'Dollarama', 'Assistant Manager', 'Carlos Lopez', '403-327-2222', '100 Scenic Drive, Lethbridge, AB', '2023-03-01'),
    (9, 'Magrath Telephone Company', 'Customer Service', 'Susan Wright', '403-758-1111', '123 Main Street, Magrath, AB', '2018-04-15'),
    (9, 'Agriculture and Agri-Food Canada', 'Administrative Assistant', 'Thomas Clark', '403-758-2222', '456 Queens Avenue, Magrath, AB', '2021-08-01'),
    (9, 'Village Green Cooperative', 'Cashier Supervisor', 'Rebecca Harris', '403-758-3333', '789 Main Street, Magrath, AB', '2024-01-10'),
    (10, 'Lethbridge College', 'Library Assistant', 'Anna Perez', '403-320-3222', '3000 College Drive, Lethbridge, AB', '2025-09-01');

INSERT INTO warranty (sale_id, start_date, end_date, policy_name, items_covered, cost, deductible, monthly_cost) VALUES
    (1, '2025-12-17', '2027-12-17', 'Premium', 'Engine and Transmission', 1500.00, 250.00),
    (1, '2025-12-17', '2026-12-17', 'Basic', 'Electrical System', 400.00, 100.00),
    (1, '2025-12-17', '2028-12-17', 'Extended', 'Suspension and Brakes', 800.00, 150.00),
    (2, '2025-12-28', '2026-12-28', 'Basic', 'Engine Components', 600.00, 100.00),
    (3, '2026-01-02', '2027-01-02', 'Extended', 'Engine and Transmission', 1800.00, 300.00),
    (3, '2026-01-02', '2027-01-02', 'Basic', 'Steering and Suspension', 500.00, 100.00),
    (5, '2026-02-05', '2029-02-05', 'Luxury', 'All Components', 3500.00, 750.00),
    (6, '2026-02-12', '2027-02-12', 'Basic', 'Engine Components', 800.00, 150.00),
    (6, '2026-02-12', '2028-02-12', 'Premium', 'Transmission and Drivetrain', 1600.00, 300.00),
    (6, '2026-02-12', '2027-02-12', 'Extended', 'Suspension System', 700.00, 125.00),
    (7, '2026-02-22', '2028-02-22', 'Premium', 'Engine and Turbocharger', 2200.00, 400.00),
    (7, '2026-02-22', '2027-02-22', 'Extended', 'Electrical and Electronics', 1100.00, 200.00),
    (9, '2026-03-12', '2027-03-12', 'Extended', 'Engine Components', 1300.00, 250.00),
    (9, '2026-03-12', '2028-03-12', 'Premium', 'Transmission Services', 1400.00, 280.00),
    (10, '2026-03-20', '2028-03-20', 'Premium', 'Engine, Transmission, Suspension', 2100.00, 400.00),
    (10, '2026-03-20', '2027-03-20', 'Extended', 'Electrical System', 900.00, 150.00);

INSERT INTO payment (customer_id, sale_id, payment_date, due, paid_date, amount, bank_id) VALUES
    (1, 1, '2026-01-17', 1, '2026-01-15', 125.50, 924817465),
    (2, 2, '2026-01-28', 1, '2026-01-29', 50.00, 517394826),
    (3, 3, '2026-02-02', 1, '2026-02-01', 100.00, 738492615),
    (4, 4, '2026-02-12', 1, '2026-02-10', 166.66, 385729461),
    (1, 1, '2026-02-17', 2, '2026-02-20', 125.50, 924817465),
    (2, 2, '2026-02-28', 2, '2026-02-27', 50.50, 517394826),
    (3, 3, '2026-03-02', 2, '2026-03-05', 100.00, 738492615),
    (5, 5, '2026-03-05', 1, '2026-03-08', 235.00, 629184753),
    (6, 6, '2026-03-12', 1, '2026-03-11', 100.00, 847263951),
    (4, 4, '2026-03-12', 2, '2026-03-12', 166.66, 385729461),
    (1, 1, '2026-03-17', 3, '2026-03-22', 125.50, 924817465),
    (7, 7, '2026-03-22', 1, '2026-03-22', 200.00, 491752836),
    (8, 8, '2026-04-02', 1, '2026-04-02', 108.33, 756183429),
    (9, 9, '2026-04-12', 1, '2026-04-15', 116.66, 312845679),
    (10, 10, '2026-04-20', 1, '2026-04-20', 133.33, 864279531);
