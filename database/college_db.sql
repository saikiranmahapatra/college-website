-- Create Database
CREATE DATABASE IF NOT EXISTS college_db;
USE college_db;

-- Faculty Table
CREATE TABLE IF NOT EXISTS faculty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    qualification VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    department VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    course VARCHAR(100) NOT NULL,
    semester INT,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    duration VARCHAR(50),
    credits INT,
    fee DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Holidays Table
CREATE TABLE IF NOT EXISTS holidays (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    date DATE NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Sample Faculty Data
INSERT INTO faculty (name, position, qualification, email, phone, department) VALUES
('Dr. Rajesh Kumar', 'Professor', 'Ph.D. in Computer Science', 'rajesh@sbpms.edu.in', '+91-9876543210', 'Computer Science'),
('Ms. Priya Singh', 'Associate Professor', 'M.Tech in Electronics', 'priya@sbpms.edu.in', '+91-9876543211', 'Electronics'),
('Mr. Amit Patel', 'Assistant Professor', 'B.Tech in Mechanical', 'amit@sbpms.edu.in', '+91-9876543212', 'Mechanical'),
('Dr. Neha Sharma', 'Professor', 'Ph.D. in Physics', 'neha@sbpms.edu.in', '+91-9876543213', 'Physics');

-- Insert Sample Student Data
INSERT INTO students (name, roll_number, course, semester, email, phone) VALUES
('Ravi Kumar', 'CSE001', 'B.Tech Computer Science', 4, 'ravi@student.sbpms.edu.in', '+91-9876543300'),
('Anjali Verma', 'CSE002', 'B.Tech Computer Science', 4, 'anjali@student.sbpms.edu.in', '+91-9876543301'),
('Vikram Singh', 'ECE001', 'B.Tech Electronics', 6, 'vikram@student.sbpms.edu.in', '+91-9876543302'),
('Sneha Gupta', 'ME001', 'B.Tech Mechanical', 2, 'sneha@student.sbpms.edu.in', '+91-9876543303');

-- Insert Sample Course Data
INSERT INTO courses (name, description, duration, credits, fee) VALUES
('B.Tech Computer Science', 'A 4-year undergraduate program in Computer Science and Engineering', '4 Years', 120, 500000),
('B.Tech Electronics', 'A 4-year undergraduate program in Electronics and Communication', '4 Years', 120, 480000),
('B.Tech Mechanical', 'A 4-year undergraduate program in Mechanical Engineering', '4 Years', 120, 450000),
('Master in Business Administration', 'A 2-year postgraduate program in Business Administration', '2 Years', 60, 800000);

-- Insert Sample Holiday Data
INSERT INTO holidays (name, date, description) VALUES
('Republic Day', '2024-01-26', 'National holiday celebrating the Republic Day of India'),
('Holi', '2024-03-25', 'Festival of colors'),
('Good Friday', '2024-03-29', 'Christian festival'),
('Summer Break Starts', '2024-05-15', 'Summer vacation begins'),
('Independence Day', '2024-08-15', 'National holiday celebrating Independence'),
('Ganesh Chaturthi', '2024-09-07', 'Hindu festival'),
('Dussehra', '2024-10-12', 'Hindu festival'),
('Diwali', '2024-11-01', 'Festival of lights'),
('Christmas', '2024-12-25', 'Christian festival');

-- Create Indexes for Better Query Performance
CREATE INDEX idx_faculty_email ON faculty(email);
CREATE INDEX idx_student_roll ON students(roll_number);
CREATE INDEX idx_student_email ON students(email);
CREATE INDEX idx_holiday_date ON holidays(date);
CREATE INDEX idx_course_name ON courses(name);