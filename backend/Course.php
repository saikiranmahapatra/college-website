<?php
class Course {
    private $conn;
    private $table = 'courses';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all courses
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

    // Get single course
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        }
        return ['success' => false, 'message' => 'Course not found'];
    }

    // Add new course
    public function add($data) {
        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $description = $this->conn->real_escape_string($data['description'] ?? '');
        $duration = $this->conn->real_escape_string($data['duration'] ?? '');
        $credits = intval($data['credits'] ?? 0);
        $fee = floatval($data['fee'] ?? 0);

        if (empty($name) || empty($duration)) {
            return ['success' => false, 'message' => 'Name and duration are required'];
        }

        $query = "INSERT INTO " . $this->table . " (name, description, duration, credits, fee) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssid', $name, $description, $duration, $credits, $fee);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Course added successfully', 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Error adding course'];
    }

    // Update course
    public function update($data) {
        $id = $data['id'] ?? null;
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $description = $this->conn->real_escape_string($data['description'] ?? '');
        $duration = $this->conn->real_escape_string($data['duration'] ?? '');
        $credits = intval($data['credits'] ?? 0);
        $fee = floatval($data['fee'] ?? 0);

        $query = "UPDATE " . $this->table . " SET name=?, description=?, duration=?, credits=?, fee=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssidi', $name, $description, $duration, $credits, $fee, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Course updated successfully'];
        }
        return ['success' => false, 'message' => 'Error updating course'];
    }

    // Delete course
    public function delete($id) {
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $query = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Course deleted successfully'];
        }
        return ['success' => false, 'message' => 'Error deleting course'];
    }
}
?>