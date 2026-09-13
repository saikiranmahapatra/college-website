<?php
/**
 * Library Management System
 * Complete book management with issue/return tracking
 */

class LibraryManager {
    private $conn;
    private $booksTable = 'books';
    private $issuesTable = 'book_issues';
    private $reservationsTable = 'book_reservations';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Search books in library
     */
    public function searchBooks($keyword, $category = null) {
        $keyword = '%' . $keyword . '%';
        
        $query = "SELECT * FROM " . $this->booksTable . " WHERE (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
        $params = [$keyword, $keyword, $keyword];
        $types = 'sss';
        
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
            $types .= 's';
        }
        
        $query .= " ORDER BY title ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        return ['success' => true, 'data' => $books];
    }

    /**
     * Get all books
     */
    public function getAllBooks() {
        $query = "SELECT * FROM " . $this->booksTable . " ORDER BY title ASC";
        $result = $this->conn->query($query);

        $books = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            return ['success' => true, 'data' => $books];
        }
        return ['success' => true, 'data' => []];
    }

    /**
     * Add new book to library
     */
    public function addBook($data) {
        $title = $this->conn->real_escape_string($data['title'] ?? '');
        $author = $this->conn->real_escape_string($data['author'] ?? '');
        $isbn = $this->conn->real_escape_string($data['isbn'] ?? '');
        $publisher = $this->conn->real_escape_string($data['publisher'] ?? '');
        $publication_year = intval($data['publication_year'] ?? 0);
        $category = $this->conn->real_escape_string($data['category'] ?? '');
        $total_copies = intval($data['total_copies'] ?? 1);
        $price = floatval($data['price'] ?? 0);

        if (empty($title) || empty($author)) {
            return ['success' => false, 'message' => 'Title and author are required'];
        }

        $query = "INSERT INTO " . $this->booksTable . " (title, author, isbn, publisher, publication_year, category, total_copies, available_copies, price) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssiidi', $title, $author, $isbn, $publisher, $publication_year, $category, $total_copies, $total_copies, $price);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Book added successfully', 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Error adding book'];
    }

    /**
     * Issue book to student
     */
    public function issueBook($studentId, $bookId) {
        // Check book availability
        $query = "SELECT available_copies FROM " . $this->booksTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();

        if (!$book || $book['available_copies'] <= 0) {
            return ['success' => false, 'message' => 'Book not available'];
        }

        // Check if student already has this book
        $checkQuery = "SELECT id FROM " . $this->issuesTable . " WHERE student_id = ? AND book_id = ? AND status = 'issued'";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param('ii', $studentId, $bookId);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Student already has this book issued'];
        }

        // Issue book
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        $issueQuery = "INSERT INTO " . $this->issuesTable . " (student_id, book_id, due_date) VALUES (?, ?, ?)";
        $issueStmt = $this->conn->prepare($issueQuery);
        $issueStmt->bind_param('iis', $studentId, $bookId, $dueDate);

        if ($issueStmt->execute()) {
            // Update available copies
            $updateQuery = "UPDATE " . $this->booksTable . " SET available_copies = available_copies - 1 WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param('i', $bookId);
            $updateStmt->execute();

            return ['success' => true, 'message' => 'Book issued successfully', 'due_date' => $dueDate];
        }
        return ['success' => false, 'message' => 'Error issuing book'];
    }

    /**
     * Return book
     */
    public function returnBook($issueId) {
        $returnDate = date('Y-m-d');
        
        // Get issue details
        $query = "SELECT book_id, due_date FROM " . $this->issuesTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $issueId);
        $stmt->execute();
        $result = $stmt->get_result();
        $issue = $result->fetch_assoc();

        if (!$issue) {
            return ['success' => false, 'message' => 'Issue record not found'];
        }

        // Calculate fine if overdue
        $fine = 0;
        $dueDate = new DateTime($issue['due_date']);
        $today = new DateTime($returnDate);
        $diff = $today->diff($dueDate);
        
        if ($diff->invert) {
            $fine = $diff->days * 10; // 10 per day
        }

        // Update issue
        $updateQuery = "UPDATE " . $this->issuesTable . " SET return_date = ?, status = 'returned', fine_amount = ? WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param('sdi', $returnDate, $fine, $issueId);

        if ($updateStmt->execute()) {
            // Update available copies
            $bookUpdateQuery = "UPDATE " . $this->booksTable . " SET available_copies = available_copies + 1 WHERE id = ?";
            $bookUpdateStmt = $this->conn->prepare($bookUpdateQuery);
            $bookUpdateStmt->bind_param('i', $issue['book_id']);
            $bookUpdateStmt->execute();

            return ['success' => true, 'message' => 'Book returned successfully', 'fine_amount' => $fine];
        }
        return ['success' => false, 'message' => 'Error returning book'];
    }

    /**
     * Get student's issued books
     */
    public function getStudentIssuedBooks($studentId) {
        $query = "SELECT bi.id, b.title, b.author, b.isbn, bi.issue_date, bi.due_date, bi.status, bi.fine_amount
                  FROM " . $this->issuesTable . " bi
                  JOIN " . $this->booksTable . " b ON bi.book_id = b.id
                  WHERE bi.student_id = ?
                  ORDER BY bi.issue_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $issues = [];
        while ($row = $result->fetch_assoc()) {
            $issues[] = $row;
        }
        return ['success' => true, 'data' => $issues];
    }

    /**
     * Reserve book
     */
    public function reserveBook($studentId, $bookId) {
        // Check if book is available
        $checkQuery = "SELECT available_copies FROM " . $this->booksTable . " WHERE id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param('i', $bookId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $book = $result->fetch_assoc();

        if (!$book || $book['available_copies'] > 0) {
            return ['success' => false, 'message' => 'Book is available. No need to reserve.'];
        }

        // Check if already reserved
        $existingQuery = "SELECT id FROM " . $this->reservationsTable . " WHERE student_id = ? AND book_id = ? AND status = 'pending'";
        $existingStmt = $this->conn->prepare($existingQuery);
        $existingStmt->bind_param('ii', $studentId, $bookId);
        $existingStmt->execute();
        if ($existingStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Book already reserved'];
        }

        $query = "INSERT INTO " . $this->reservationsTable . " (student_id, book_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $studentId, $bookId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Book reserved successfully'];
        }
        return ['success' => false, 'message' => 'Error reserving book'];
    }
}
?>