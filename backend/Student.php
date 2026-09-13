<?php
class Student {
    private $conn;
    private $table = 'students';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all students
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $result = $this->conn->query($query);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return ['success' => true, 'data' => $data];
        }
        return ['success' => true, 'data' => []];
    }

    // Get single student
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        }
        return ['success' => false, 'message' => 'Student not found'];
    }

    // Add new student
    public function add($data) {
        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $roll_number = $this->conn->real_escape_string($data['roll_number'] ?? '');
        $course = $this->conn->real_escape_string($data['course'] ?? '');
        $semester = $this->conn->real_escape_string($data['semester'] ?? '');
        $email = $this->conn->real_escape_string($data['email'] ?? '');
        $phone = $this->conn->real_escape_string($data['phone'] ?? '');

        if (empty($name) || empty($roll_number) || empty($course)) {
            return ['success' => false, 'message' => 'Name, roll number, and course are required'];
        }

        $query = "INSERT INTO " . $this->table . " (name, roll_number, course, semester, email, phone) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssisss', $name, $roll_number, $course, $semester, $email, $phone);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Student added successfully', 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Error adding student'];
    }

    // Update student
    public function update($data) {
        $id = $data['id'] ?? null;
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $roll_number = $this->conn->real_escape_string($data['roll_number'] ?? '');
        $course = $this->conn->real_escape_string($data['course'] ?? '');
        $semester = $this->conn->real_escape_string($data['semester'] ?? '');
        $email = $this->conn->real_escape_string($data['email'] ?? '');
        $phone = $this->conn->real_escape_string($data['phone'] ?? '');

        $query = "UPDATE " . $this->table . " SET name=?, roll_number=?, course=?, semester=?, email=?, phone=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssisssi', $name, $roll_number, $course, $semester, $email, $phone, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Student updated successfully'];
        }
        return ['success' => false, 'message' => 'Error updating student'];
    }

    // Delete student
    public function delete($id) {
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $query = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Student deleted successfully'];
        }
        return ['success' => false, 'message' => 'Error deleting student'];
    }
}
?>