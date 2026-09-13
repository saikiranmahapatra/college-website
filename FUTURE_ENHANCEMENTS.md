<?php
/**
 * FUTURE ENHANCEMENTS - Feature Planning Document
 * This file documents planned features for the college website
 */
?>

# Future Enhancements & Feature Roadmap

## Phase 2: Authentication & Security (High Priority)

### 1. User Authentication System

#### Features:
- User registration (Students, Faculty, Admin)
- Login/Logout functionality
- Password reset via email
- Two-factor authentication (2FA)
- Session management
- Remember me functionality

#### Database Schema:
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    phone VARCHAR(15),
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Backend Implementation:
```php
<?php
class Authentication {
    private $conn;
    private $table = 'users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($data) {
        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $query = "INSERT INTO " . $this->table . " (username, email, password_hash, role, phone) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssss', $data['username'], $data['email'], $password_hash, $data['role'], $data['phone']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User registered successfully'];
        }
        return ['success' => false, 'message' => 'Registration failed'];
    }

    public function login($email, $password) {
        $query = "SELECT id, username, email, role, password_hash FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                return ['success' => true, 'user' => $user];
            }
        }
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    public function resetPassword($email) {
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Get user ID
        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Insert reset token
            $insertQuery = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bind_param('iss', $user['id'], $token, $expires_at);
            if ($insertStmt->execute()) {
                return ['success' => true, 'token' => $token, 'message' => 'Reset link sent to email'];
            }
        }
        return ['success' => false, 'message' => 'User not found'];
    }
}
?>
```

---

## Phase 3: Email Notification System (High Priority)

### 1. Email Notifications

#### Features:
- Welcome email on registration
- Password reset emails
- Holiday announcements
- Course updates
- Exam schedules
- Library notifications
- Event reminders

#### Database Schema:
```sql
CREATE TABLE email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE email_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT
);
```

#### Implementation using PHPMailer:
```php
<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $conn;
    private $mail;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    private function configureSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('MAIL_USERNAME');
        $this->mail->Password = getenv('MAIL_PASSWORD');
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
    }

    public function sendWelcomeEmail($recipientEmail, $name) {
        try {
            $this->mail->setFrom('noreply@sbpms.edu.in', 'SBPMS');
            $this->mail->addAddress($recipientEmail);
            $this->mail->Subject = 'Welcome to SBPMS';
            $this->mail->Body = "<h1>Welcome, $name!</h1>
                                 <p>Thank you for registering at Sri Bharata Pati Mahavidylay Samantiyapalli.</p>
                                 <p>You can now login to your account and explore our platform.</p>";
            $this->mail->isHTML(true);
            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendPasswordResetEmail($recipientEmail, $resetToken) {
        try {
            $resetLink = "http://yoursite.com/reset-password.php?token=" . $resetToken;
            $this->mail->setFrom('noreply@sbpms.edu.in', 'SBPMS');
            $this->mail->addAddress($recipientEmail);
            $this->mail->Subject = 'Password Reset Request';
            $this->mail->Body = "<h1>Password Reset</h1>
                                 <p>Click the link below to reset your password:</p>
                                 <a href='$resetLink'>Reset Password</a>
                                 <p>This link will expire in 1 hour.</p>";
            $this->mail->isHTML(true);
            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendHolidayAnnouncement($holidayData) {
        // Get all users
        $query = "SELECT email FROM users WHERE is_active = TRUE";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            while ($user = $result->fetch_assoc()) {
                try {
                    $this->mail->clearAddresses();
                    $this->mail->addAddress($user['email']);
                    $this->mail->Subject = 'Holiday Announcement: ' . $holidayData['name'];
                    $this->mail->Body = "<h2>" . $holidayData['name'] . "</h2>
                                         <p><strong>Date:</strong> " . $holidayData['date'] . "</p>
                                         <p>" . $holidayData['description'] . "</p>";
                    $this->mail->isHTML(true);
                    $this->mail->send();
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }
}
?>
```

---

## Phase 4: Student Dashboard

### Features:
- Personal profile management
- View enrolled courses
- Course materials and resources
- Assignment submission
- Grade tracking
- Attendance records
- Fee payment status
- Download academic documents
- Schedule and timetable
- Internal marks

### Database Schema:
```sql
CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    internal_marks DECIMAL(5, 2),
    external_marks DECIMAL(5, 2),
    total_marks DECIMAL(5, 2),
    grade VARCHAR(2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    date_marked DATE,
    status ENUM('present', 'absent', 'leave') DEFAULT 'absent',
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE assignment_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submission_file VARCHAR(255),
    submitted_at TIMESTAMP,
    marks_obtained DECIMAL(5, 2),
    feedback TEXT,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

CREATE TABLE fee_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount DECIMAL(10, 2),
    payment_date DATE,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(id)
);
```

### Student Dashboard HTML/PHP:
```html
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            margin-top: 0;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <h3>📊 Grades</h3>
            <div id="gradesContainer"></div>
        </div>
        
        <div class="card">
            <h3>✓ Attendance</h3>
            <div id="attendanceContainer"></div>
        </div>
        
        <div class="card">
            <h3>📝 Assignments</h3>
            <div id="assignmentsContainer"></div>
        </div>
        
        <div class="card">
            <h3>💰 Fee Status</h3>
            <div id="feeContainer"></div>
        </div>
    </div>
    
    <script>
        // Load dashboard data
        function loadDashboard() {
            loadGrades();
            loadAttendance();
            loadAssignments();
            loadFeeStatus();
        }
        
        function loadGrades() {
            fetch('api.php?action=getStudentGrades')
                .then(r => r.json())
                .then(d => {
                    let html = '';
                    d.data.forEach(grade => {
                        html += `<div><strong>${grade.course_name}</strong><br>Marks: ${grade.total_marks}/100</div>`;
                    });
                    document.getElementById('gradesContainer').innerHTML = html;
                });
        }
        
        function loadAttendance() {
            fetch('api.php?action=getStudentAttendance')
                .then(r => r.json())
                .then(d => {
                    let totalClasses = d.data.length;
                    let presentDays = d.data.filter(a => a.status === 'present').length;
                    let percentage = Math.round((presentDays / totalClasses) * 100);
                    document.getElementById('attendanceContainer').innerHTML = 
                        `<strong>${percentage}%</strong><br>Present: ${presentDays}/${totalClasses}`;
                });
        }
        
        function loadAssignments() {
            fetch('api.php?action=getStudentAssignments')
                .then(r => r.json())
                .then(d => {
                    let html = '';
                    d.data.forEach(assignment => {
                        html += `<div><strong>${assignment.title}</strong><br>Due: ${assignment.due_date}</div>`;
                    });
                    document.getElementById('assignmentsContainer').innerHTML = html;
                });
        }
        
        function loadFeeStatus() {
            fetch('api.php?action=getStudentFeeStatus')
                .then(r => r.json())
                .then(d => {
                    let status = d.data.status;
                    let color = status === 'completed' ? 'green' : 'red';
                    document.getElementById('feeContainer').innerHTML = 
                        `<div style="color: ${color}"><strong>${status.toUpperCase()}</strong></div>`;
                });
        }
        
        window.addEventListener('load', loadDashboard);
    </script>
</body>
</html>
```

---

## Phase 5: Library Management System (High Priority)

### Features:
- Book catalog and search
- Issue and return books
- Reservation system
- Fine management
- Digital library access
- Reading list recommendations
- Book availability tracking

### Database Schema:
```sql
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100),
    isbn VARCHAR(20) UNIQUE,
    publisher VARCHAR(100),
    publication_year INT,
    category VARCHAR(50),
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE book_issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    book_id INT NOT NULL,
    issue_date DATE DEFAULT (CURDATE()),
    due_date DATE,
    return_date DATE,
    fine_amount DECIMAL(10, 2) DEFAULT 0,
    status ENUM('issued', 'returned', 'lost') DEFAULT 'issued',
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE book_reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_date DATE DEFAULT (CURDATE()),
    status ENUM('pending', 'issued', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE TABLE digital_library (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    resource_type VARCHAR(50),
    file_path VARCHAR(255),
    access_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Library Management Class:
```php
<?php
class LibraryManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function issueBook($studentId, $bookId) {
        // Check book availability
        $query = "SELECT available_copies FROM books WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        if ($book['available_copies'] > 0) {
            // Issue book
            $dueDate = date('Y-m-d', strtotime('+14 days'));
            $insertQuery = "INSERT INTO book_issues (student_id, book_id, due_date) VALUES (?, ?, ?)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bind_param('iis', $studentId, $bookId, $dueDate);
            
            if ($insertStmt->execute()) {
                // Update book count
                $updateQuery = "UPDATE books SET available_copies = available_copies - 1 WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bind_param('i', $bookId);
                $updateStmt->execute();
                
                return ['success' => true, 'message' => 'Book issued successfully'];
            }
        }
        return ['success' => false, 'message' => 'Book not available'];
    }
    
    public function returnBook($issueId) {
        $returnDate = date('Y-m-d');
        $query = "UPDATE book_issues SET return_date = ?, status = 'returned' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $returnDate, $issueId);
        
        if ($stmt->execute()) {
            // Calculate fine if overdue
            $this->calculateFine($issueId);
            // Update book count
            $this->updateBookAvailability($issueId);
            return ['success' => true, 'message' => 'Book returned successfully'];
        }
        return ['success' => false, 'message' => 'Error returning book'];
    }
    
    private function calculateFine($issueId) {
        $query = "SELECT due_date, return_date FROM book_issues WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $issueId);
        $stmt->execute();
        $result = $stmt->get_result();
        $issue = $result->fetch_assoc();
        
        $dueDate = new DateTime($issue['due_date']);
        $returnDate = new DateTime($issue['return_date']);
        $diff = $returnDate->diff($dueDate);
        $days = $diff->days;
        
        if ($days > 0) {
            $fineAmount = $days * 10; // 10 per day
            $updateQuery = "UPDATE book_issues SET fine_amount = ? WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param('di', $fineAmount, $issueId);
            $updateStmt->execute();
        }
    }
    
    private function updateBookAvailability($issueId) {
        $query = "SELECT book_id FROM book_issues WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $issueId);
        $stmt->execute();
        $result = $stmt->get_result();
        $issue = $result->fetch_assoc();
        
        $updateQuery = "UPDATE books SET available_copies = available_copies + 1 WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param('i', $issue['book_id']);
        $updateStmt->execute();
    }
    
    public function searchBooks($keyword) {
        $keyword = '%' . $keyword . '%';
        $query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sss', $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        return ['success' => true, 'data' => $books];
    }
}
?>
```

---

## Phase 6: Online Admission System

### Features:
- Online application form
- Document upload (Photo, 10th cert, 12th cert, etc.)
- Application tracking
- Merit list generation
- Admission status updates
- Email notifications
- Payment gateway integration

### Database Schema:
```sql
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_number VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    date_of_birth DATE,
    gender VARCHAR(10),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pin_code VARCHAR(10),
    course_applied VARCHAR(100),
    marks_10 DECIMAL(5, 2),
    marks_12 DECIMAL(5, 2),
    entrance_marks DECIMAL(5, 2),
    status ENUM('submitted', 'under_review', 'shortlisted', 'admitted', 'rejected') DEFAULT 'submitted',
    applied_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE application_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    document_type VARCHAR(50),
    file_path VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

CREATE TABLE admission_fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    amount DECIMAL(10, 2),
    payment_date DATE,
    transaction_id VARCHAR(100),
    status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    FOREIGN KEY (application_id) REFERENCES applications(id)
);
```

---

## Phase 7: Additional Enhancements

### 1. Role-Based Access Control (RBAC)
```php
// Define roles and permissions
$roles = [
    'admin' => ['manage_faculty', 'manage_students', 'manage_courses', 'manage_holidays', 'manage_library', 'process_admissions'],
    'faculty' => ['view_students', 'submit_grades', 'view_courses', 'upload_materials'],
    'student' => ['view_grades', 'view_courses', 'issue_books', 'submit_assignments'],
    'librarian' => ['manage_books', 'issue_books', 'manage_fines']
];

function checkPermission($userRole, $permission) {
    global $roles;
    return in_array($permission, $roles[$userRole] ?? []);
}
```

### 2. Notifications System
```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255),
    message TEXT,
    type VARCHAR(50),
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 3. Audit Logging
```sql
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Implementation Priority

1. **Immediate (Sprint 1-2):**
   - User Authentication
   - Email Notifications
   - Library Management Basic

2. **Short-term (Sprint 3-4):**
   - Student Dashboard
   - Library Advanced Features
   - RBAC System

3. **Medium-term (Sprint 5-6):**
   - Online Admission
   - Advanced Notifications
   - Analytics Dashboard

4. **Long-term (Sprint 7+):**
   - Mobile App
   - AI-based Recommendations
   - Blockchain Certificates
   - Advanced Analytics

---

## Dependencies & Libraries

- PHPMailer (Email)
- JWT (Authentication)
- PayPal/Razorpay SDK (Payments)
- Chart.js (Analytics)
- Bootstrap 5 (UI)
- Axios (AJAX)

---

## Testing Strategy

- Unit Tests (PHPUnit)
- Integration Tests
- E2E Tests (Selenium)
- Security Testing (OWASP)
- Performance Testing (LoadRunner)

---

## Deployment Checklist

- [ ] Environment configuration
- [ ] Database migration
- [ ] SSL certificate
- [ ] CDN setup
- [ ] Email service
- [ ] Backup system
- [ ] Monitoring tools
- [ ] Documentation

---

**Last Updated:** 2024
**Version:** 2.0 Planning