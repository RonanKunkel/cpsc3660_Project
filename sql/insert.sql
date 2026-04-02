USE JonesAutoDB;

-- Customers
INSERT INTO customer (last_name, first_name, gender, date_of_birth, phone, address, city, state, zip) VALUES
('Smith','John','Male','1985-03-12','4035551122','123 Maple St','Calgary','Alberta','T2A1A1'),
('Johnson','Emily','Female','1992-07-22','4035552211','88 Lakeview Rd','Lethbridge','Alberta','T1K3A1'),
('Nguyen','David','Male','1988-11-02','4035559988','45 University Dr','Edmonton','Alberta','T5J2R3'),
('Garcia','Luis','Male','1990-05-14','4035551111','22 Pine St','Calgary','Alberta','T2A2B1'),
('Peterson','Hannah','Female','1987-08-09','4035552222','55 Prairie Rd','Lethbridge','Alberta','T1K7A2'),
('Ali','Omar','Male','1995-01-30','4035553333','10 Riverside Dr','Medicine Hat','Alberta','T1A3X2'),
('Kim','Soo','Other','1998-12-11','4035554444','89 University Ave','Edmonton','Alberta','T5K3M2'),
('Williams','Grace','Female','1993-06-20','4035555556','901 Hill St','Red Deer','Alberta','T4N5H4');

-- Employees
INSERT INTO employee (lastName, firstName, phone) VALUES
('Brown','Michael','4035555555'),
('Davis','Sarah','4035556666'),
('Lopez','Carlos','4035557777'),
('Miller','Kate','4035558888'),
('Anderson','Paul','4035559999');

-- Vehicles
INSERT INTO vehicle (vin, make, model, year, color, interior_color, miles, style, vehicle_condition, book_price) VALUES
('1HGCM82633A123456','Honda','Civic',2018,'Blue','Black',72000,'Sedan','Light Wear',18500.00),
('2C3KA53G76H987654','Toyota','Corolla',2017,'White','Gray',83000,'Sedan','Moderate Wear',15000.00),
('1FTFW1EF1EFA54321','Ford','F150',2019,'Black','Black',60000,'Pickup','Excellent',32000.00),
('1N4AL3AP8GC123456','Nissan','Altima',2019,'Gray','Black',64000,'Sedan','Light Wear',19000.00),
('3CZRM3H55GG765432','Honda','CRV',2018,'Red','Black',72000,'SUV','Moderate Wear',21000.00),
('5TDYZ3DC5HS456789','Toyota','Sienna',2017,'Silver','Gray',95000,'Van','Moderate Wear',17500.00),
('1FA6P8CF6H1239876','Ford','Mustang',2020,'Yellow','Black',32000,'Coupe','Excellent',36000.00),
('2HKRW2H59KH223344','Honda','CRV',2021,'Blue','Black',21000,'SUV','Excellent',32000.00);

-- Employment History
INSERT INTO employment_history (customer_id, employer, title, supervisor, supervisor_phone, address, start_date) VALUES
(1,'Calgary Tech Ltd','Technician','Robert King','4035557878','400 Tech Park Calgary','2018-05-01'),
(2,'Lethbridge College','Administrator','Jane Porter','4035551212','300 College Dr Lethbridge','2020-09-10'),
(3,'Edmonton Logistics','Driver','Paul Chen','4035553434','789 Industrial Rd Edmonton','2019-02-14'),
(4,'Prairie Logistics','Dispatcher','Adam Clarke','4035551213','77 Industrial Rd Calgary','2021-02-15'),
(5,'Red Deer Hospital','Nurse','Sandra Holt','4035555454','430 Health Ave Red Deer','2019-04-01'),
(6,'Medicine Hat Retail','Sales Associate','Trevor Miles','4035558787','55 Retail Park Medicine Hat','2022-07-10'),
(7,'University of Alberta','Research Assistant','Dr. Lane','4035553433','900 Campus Dr Edmonton','2023-01-15'),
(8,'Prairie Insurance','Claims Agent','David North','4035556767','100 Finance St Calgary','2020-03-22');

-- Purchases
INSERT INTO purchase (vin, purchase_date, LOCATION, auction, seller, price_paid) VALUES
('1HGCM82633A123456','2024-01-10','Calgary','Manheim Auction','Fleet Sales Ltd',14000.00),
('2C3KA53G76H987654','2024-02-05','Edmonton','Dealer Auction','AutoWest Ltd',11000.00),
('1FTFW1EF1EFA54321','2024-02-20','Red Deer','Manheim Auction','TruckWorld',25000.00),
('1N4AL3AP8GC123456','2024-04-10','Calgary','Manheim Auction','Fleet Liquidators',15000.00),
('3CZRM3H55GG765432','2024-04-15','Edmonton','Dealer Auction','West Auto Group',16000.00),
('5TDYZ3DC5HS456789','2024-05-01','Red Deer','Manheim Auction','Family Vans Ltd',12000.00),
('1FA6P8CF6H1239876','2024-05-10','Calgary','Dealer Auction','Performance Autos',29000.00),
('2HKRW2H59KH223344','2024-05-20','Edmonton','Dealer Auction','Urban Motors',26000.00);

-- Repairs
INSERT INTO repair (purchase_id, description, estimated_cost, actual_cost) VALUES
(1,'Brake pad replacement',400.00,380.00),
(2,'Paint touch-up',250.00,260.00),
(3,'Oil change and inspection',120.00,115.00),
(4,'Battery replacement',200.00,190.00),
(5,'Front brake replacement',450.00,430.00),
(6,'Transmission inspection',600.00,580.00),
(7,'Paint correction',300.00,310.00),
(8,'Tire replacement',700.00,690.00);

-- Sales
INSERT INTO sale (vin, customer_id, employee_id, sale_date, sale_price, down_payment, financed_amount, commission) VALUES
('1HGCM82633A123456',1,1,'2024-03-15',18500.00,2000.00,16500.00,925.00),
('2C3KA53G76H987654',2,2,'2024-03-20',15000.00,1500.00,13500.00,750.00),
('1N4AL3AP8GC123456',4,3,'2024-06-01',19500.00,2500.00,17000.00,975.00),
('3CZRM3H55GG765432',5,4,'2024-06-05',22000.00,3000.00,19000.00,1100.00),
('5TDYZ3DC5HS456789',6,3,'2024-06-12',18500.00,2000.00,16500.00,925.00),
('1FA6P8CF6H1239876',7,5,'2024-06-20',38000.00,5000.00,33000.00,1900.00),
('2HKRW2H59KH223344',8,4,'2024-06-28',33500.00,4000.00,29500.00,1675.00);

-- Warranty Types
INSERT INTO warranty_types (name, items_covered) VALUES
('Basic','Engine, Transmission'),
('Silver','Engine, Transmission, Electrical'),
('Gold','Engine, Transmission, Electrical, Suspension'),
('Platinum','All major components');

-- Warranties
INSERT INTO warranty (sale_id, start_date, end_date, policy_name, items_covered, cost, monthly_cost, deductible) VALUES
(1,'2024-03-15','2027-03-15','Gold Plan','Engine, Transmission, Electrical',1800.00,50.00,200.00),
(2,'2024-03-20','2026-03-20','Silver Plan','Engine, Transmission',1200.00,40.00,300.00),
(3,'2024-06-01','2027-06-01','Silver Plan','Engine, Transmission, Electrical',1400.00,45.00,250.00),
(4,'2024-06-05','2027-06-05','Gold Plan','Engine, Transmission, Electrical, Suspension',1800.00,55.00,200.00),
(5,'2024-06-12','2026-06-12','Basic Plan','Engine, Transmission',900.00,30.00,300.00);

-- Payments
INSERT INTO payment (customer_id, sale_id, payment_date, due, paid_date, amount, bank_id) VALUES
(1,1,'2024-04-01',450.00,'2024-04-01',450.00,101),
(1,1,'2024-05-01',450.00,'2024-05-01',450.00,101),
(2,2,'2024-04-05',400.00,'2024-04-05',400.00,102),
(4,3,'2024-07-01',500.00,'2024-07-01',500.00,301),
(5,4,'2024-07-05',520.00,'2024-07-05',520.00,302),
(6,5,'2024-07-12',450.00,'2024-07-12',450.00,303),
(7,6,'2024-07-20',800.00,'2024-07-20',800.00,304),
(8,7,'2024-07-28',700.00,'2024-07-28',700.00,305);
