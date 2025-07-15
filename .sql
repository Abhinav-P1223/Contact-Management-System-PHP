-- Create the database
CREATE DATABASE contact_manager;

-- Use the database
USE contact_manager;

-- Create the contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    hobbies TEXT,
    country VARCHAR(50) NOT NULL,
    other_country VARCHAR(50),
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admin table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert a default admin user (password should be hashed in real usage)
INSERT INTO admins (username, password)
VALUES ('admin', '$2y$10$examplehashedpassword');
-- Replace with real hashed password from PHP using password_hash()

-- Optional: sample contact insert
INSERT INTO contacts (name, email, phone, gender, hobbies, country, username, password, profile_pic)
VALUES (
    'John Doe',
    'john@example.com',
    '9876543210',
    'Male',
    'Reading, Coding',
    'India',
    'john_doe',
    '$2y$10$samplehashedpass1234567890',
    'uploads/sample.jpg'
);
