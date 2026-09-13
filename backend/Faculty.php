<?php
class Faculty {
    private $conn;
    private $table = 'faculty';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all faculty members
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

    // Get single faculty member
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        }
        return ['success' => false, 'message' => 'Faculty not found'];
    }

    // Add new faculty
    public function add($data) {
        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $position = $this->conn->real_escape_string($data['position'] ?? '');
        $qualification = $this->conn->real_escape_string($data['qualification'] ?? '');
        $email = $this->conn->real_escape_string($data['email'] ?? '');
        $phone = $this->conn->real_escape_string($data['phone'] ?? '');
        $department = $this->conn->real_escape_string($data['department'] ?? '');

        if (empty($name) || empty($position) || empty($email)) {
            return ['success' => false, 'message' => 'Name, position, and email are required'];
        }

        $query = "INSERT INTO " . $this->table . " (name, position, qualification, email, phone, department) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssss', $name, $position, $qualification, $email, $phone, $department);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Faculty added successfully', 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Error adding faculty: ' . $this->conn->error];
    }

    // Update faculty
    public function update($data) {
        $id = $data['id'] ?? null;
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $position = $this->conn->real_escape_string($data['position'] ?? '');
        $qualification = $this->conn->real_escape_string($data['qualification'] ?? '');
        $email = $this->conn->real_escape_string($data['email'] ?? '');
        $phone = $this->conn->real_escape_string($data['phone'] ?? '');
        $department = $this->conn->real_escape_string($data['department'] ?? '');

        $query = "UPDATE " . $this->table . " SET name=?, position=?, qualification=?, email=?, phone=?, department=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssssssi', $name, $position, $qualification, $email, $phone, $department, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Faculty updated successfully'];
        }
        return ['success' => false, 'message' => 'Error updating faculty'];
    }

    // Delete faculty
    public function delete($id) {
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $query = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Faculty deleted successfully'];
        }
        return ['success' => false, 'message' => 'Error deleting faculty'];
    }
}
?>