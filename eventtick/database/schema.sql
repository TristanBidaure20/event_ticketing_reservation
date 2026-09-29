-- EventTick: Event Ticketing Reservation System (matches the ERD in the proposal)
CREATE DATABASE IF NOT EXISTS eventtick CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventtick;

CREATE TABLE users (
  user_id  INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(100) NOT NULL,
  email    VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,               -- password_hash() output, never plain text
  role     ENUM('customer','organizer','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB;

CREATE TABLE venues (
  venue_id   INT AUTO_INCREMENT PRIMARY KEY,
  venue_name VARCHAR(120) NOT NULL,
  location   VARCHAR(160) NOT NULL,
  capacity   INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE events (
  event_id    INT AUTO_INCREMENT PRIMARY KEY,
  venue_id    INT NOT NULL,
  event_name  VARCHAR(140) NOT NULL,
  event_date  DATE NOT NULL,
  event_time  TIME NOT NULL,
  description TEXT,
  INDEX idx_event_date (event_date),
  FOREIGN KEY (venue_id) REFERENCES venues(venue_id)
) ENGINE=InnoDB;

CREATE TABLE ticket_types (
  ticket_id   INT AUTO_INCREMENT PRIMARY KEY,
  event_id    INT NOT NULL,
  ticket_name VARCHAR(60) NOT NULL,
  price       DECIMAL(10,2) NOT NULL,
  quantity    INT NOT NULL,                     -- total stock
  available   INT NOT NULL,                     -- remaining stock
  FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reservations (
  reserve_id   INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  reserve_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- also the start of the 10-minute hold
  total_amount DECIMAL(10,2) NOT NULL,
  status       ENUM('Pending','Confirmed','Cancelled','Expired') NOT NULL DEFAULT 'Pending',
  INDEX idx_status_date (status, reserve_date),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE reservation_details (
  detail_id  INT AUTO_INCREMENT PRIMARY KEY,
  reserve_id INT NOT NULL,
  ticket_id  INT NOT NULL,
  quantity   INT NOT NULL,
  subtotal   DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (reserve_id) REFERENCES reservations(reserve_id) ON DELETE CASCADE,
  FOREIGN KEY (ticket_id)  REFERENCES ticket_types(ticket_id)
) ENGINE=InnoDB;

CREATE TABLE payments (
  payment_id   INT AUTO_INCREMENT PRIMARY KEY,
  reserve_id   INT NOT NULL,
  payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  amount       DECIMAL(10,2) NOT NULL,
  method       ENUM('Card','GCash','PayPal','Bank Transfer') NOT NULL,
  status       ENUM('Paid','Failed') NOT NULL,
  FOREIGN KEY (reserve_id) REFERENCES reservations(reserve_id)
) ENGINE=InnoDB;

-- Sample data (dates are relative to today so events always show as upcoming)
INSERT INTO venues (venue_name, location, capacity) VALUES
 ('Mall of Asia Arena', 'Pasay City', 15000),
 ('SMX Convention Center', 'Pasay City', 5000),
 ('The Theatre at Solaire', 'Parañaque City', 1750);

INSERT INTO events (venue_id, event_name, event_date, event_time, description) VALUES
 (1, 'Music Festival',  DATE_ADD(CURDATE(), INTERVAL 21 DAY), '19:00:00', 'Live performances from top artists.'),
 (2, 'Tech Conference', DATE_ADD(CURDATE(), INTERVAL 35 DAY), '09:00:00', 'Talks and workshops on software, AI, and cloud.'),
 (3, 'Comedy Night Live', DATE_ADD(CURDATE(), INTERVAL 49 DAY), '20:00:00', 'A stand-up night with the country''s funniest.');

INSERT INTO ticket_types (event_id, ticket_name, price, quantity, available) VALUES
 (1, 'VIP',      3500.00,  50,  50),
 (1, 'Regular',  1500.00, 120, 120),
 (1, 'Balcony',   800.00, 200, 200),
 (2, 'Standard', 2000.00, 300, 300),
 (2, 'Student',  1000.00, 150, 150),
 (3, 'Orchestra',1800.00, 100, 100),
 (3, 'Balcony',   800.00, 250, 250);
