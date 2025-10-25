CREATE DATABASE IF NOT EXISTS smartlorry;
USE smartlorry;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  role ENUM('client','admin','driver') DEFAULT 'client'
);

CREATE TABLE trucks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  plate_number VARCHAR(50),
  model VARCHAR(100),
  capacity VARCHAR(50),
  cost_per_km DECIMAL(10,2),
  status ENUM('available','booked','on_trip','maintenance') DEFAULT 'available',
  current_lat DECIMAL(10,6) DEFAULT NULL,
  current_lng DECIMAL(10,6) DEFAULT NULL
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT,
  truck_id INT,
  origin VARCHAR(255),
  destination VARCHAR(255),
  distance_km DECIMAL(10,2),
  estimated_cost DECIMAL(10,2),
  status ENUM('pending','approved','on_trip','completed','cancelled') DEFAULT 'pending',
  booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (truck_id) REFERENCES trucks(id) ON DELETE SET NULL
);

CREATE TABLE trip_updates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT,
  lat DECIMAL(10,6),
  lng DECIMAL(10,6),
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Insert a default admin (password: admin123)
INSERT INTO users (name, email, password, role) VALUES ('Admin','admin@smartlorry.test', CONCAT('*','admin123'), 'admin');
-- Note: After import, update password using PHP script or set password_hash in app.
