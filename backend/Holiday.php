<?php
class Holiday {
    private $conn;
    private $table = 'holidays';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all holidays
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY date ASC";
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

    // Get single holiday
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        }
        return ['success' => false, 'message' => 'Holiday not found'];
    }

    // Add new holiday
    public function add($data) {
        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $date = $this->conn->real_escape_string($data['date'] ?? '');
        $description = $this->conn->real_escape_string($data['description'] ?? '');

        if (empty($name) || empty($date)) {
            return ['success' => false, 'message' => 'Name and date are required'];
        }

        // Validate date format
        if (!strtotime($date)) {
            return ['success' => false, 'message' => 'Invalid date format'];
        }

        $query = "INSERT INTO " . $this->table . " (name, date, description) 
                  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sss', $name, $date, $description);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Holiday added successfully', 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Error adding holiday'];
    }

    // Update holiday
    public function update($data) {
        $id = $data['id'] ?? null;
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $name = $this->conn->real_escape_string($data['name'] ?? '');
        $date = $this->conn->real_escape_string($data['date'] ?? '');
        $description = $this->conn->real_escape_string($data['description'] ?? '');

        $query = "UPDATE " . $this->table . " SET name=?, date=?, description=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssi', $name, $date, $description, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Holiday updated successfully'];
        }
        return ['success' => false, 'message' => 'Error updating holiday'];
    }

    // Delete holiday
    public function delete($id) {
        if (!$id) {
            return ['success' => false, 'message' => 'ID is required'];
        }

        $query = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Holiday deleted successfully'];
        }
        return ['success' => false, 'message' => 'Error deleting holiday'];
    }
}
?>