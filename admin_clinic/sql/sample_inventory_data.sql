-- Sample Inventory Items for MEDINEST
-- IMPORTANT: You must use a valid 'clinic_id' that exists in your 'clinics' table.
-- To find your clinic_id, you can run: SELECT id, clinic_name FROM clinics;

-- This script uses a subquery to automatically pick the FIRST clinic in your database.
-- If you want to use a specific clinic, replace (SELECT id FROM clinics LIMIT 1) with the actual ID.

SET @cid = (SELECT id FROM clinics LIMIT 1);

INSERT INTO `clinic_inventory` (`clinic_id`, `item_name`, `category`, `quantity`, `unit`, `expiration_date`, `status`) VALUES
(@cid, 'Rabies Vaccine (Canine)', 'Vaccine', 50, 'Vials', '2026-12-31', 'Available'),
(@cid, 'DHLPP Combination Vaccine', 'Vaccine', 30, 'Vials', '2026-08-15', 'Available'),
(@cid, 'Feline Viral Rhinotracheitis', 'Vaccine', 20, 'Vials', '2026-09-20', 'Available'),
(@cid, 'Amoxicillin 250mg', 'Medicine', 100, 'Tablets', '2027-01-10', 'Available'),
(@cid, 'Ivermectin Injection', 'Medicine', 5, 'Bottles', '2026-04-05', 'Low Stock'),
(@cid, 'Surgical Masks', 'Supply', 500, 'Pieces', NULL, 'Available'),
(@cid, 'Disposable Syringes (3ml)', 'Supply', 1000, 'Pieces', NULL, 'Available'),
(@cid, 'Medical Gauze (Sterile)', 'Supply', 0, 'Packs', NULL, 'Out of Stock'),
(@cid, 'Dog Multivitamins', 'Supplement', 40, 'Bottles', '2027-03-22', 'Available'),
(@cid, 'Omega-3 Fish Oil', 'Supplement', 15, 'Bottles', '2026-11-12', 'Available'),
(@cid, 'Cat Calming Treats', 'Supplement', 8, 'Packs', '2026-05-15', 'Low Stock'),
(@cid, 'Parvovirus Test Kits', 'Supply', 25, 'Kits', '2027-06-30', 'Available');
