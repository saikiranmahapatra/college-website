<?php
/**
 * Enhanced User Authentication System
 * This class handles user registration, login, and password management
 */

class AuthenticationManager {
    private $conn;
    private $table = 'users';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Register a new user
     */
    public function register($data) {
        // Validate input
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Username, email, and password are required'];
        }

        // Check if user already exists
        $query = "SELECT id FROM " . $this->table . " WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $data['email'], $data['username']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'User already exists'];
        }

        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = $data['role'] ?? 'student';
        $phone = $data['phone'] ?? '';

        $query = "INSERT INTO " . $this->table . " (username, email, password_hash, role, phone, is_active) 
                  VALUES (?, ?, ?, ?, ?, TRUE)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssss', $data['username'], $data['email'], $password_hash, $role, $phone);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User registered successfully', 'user_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Registration failed: ' . $this->conn->error];
    }

    /**
     * User login
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        $query = "SELECT id, username, email, role, password_hash, is_active FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        $user = $result->fetch_assoc();

        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Account is inactive'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Update last login
        $updateQuery = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param('i', $user['id']);
        $updateStmt->execute();

        // Remove password hash from response
        unset($user['password_hash']);

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $this->generateToken($user['id'])
        ];
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        if (empty($email)) {
            return ['success' => false, 'message' => 'Email is required'];
        }

        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Email not found'];
        }

        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iss', $user['id'], $token, $expires_at);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Reset link sent to email', 'token' => $token];
        }
        return ['success' => false, 'message' => 'Error processing request'];
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword) {
        if (empty($token) || empty($newPassword)) {
            return ['success' => false, 'message' => 'Token and password are required'];
        }

        $query = "SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }

        $reset = $result->fetch_assoc();
        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update password
        $updateQuery = "UPDATE " . $this->table . " SET password_hash = ? WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param('si', $password_hash, $reset['user_id']);

        if ($updateStmt->execute()) {
            // Delete used token
            $deleteQuery = "DELETE FROM password_resets WHERE token = ?";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bind_param('s', $token);
            $deleteStmt->execute();

            return ['success' => true, 'message' => 'Password reset successful'];
        }
        return ['success' => false, 'message' => 'Error resetting password'];
    }

    /**
     * Generate JWT token
     */
    private function generateToken($userId) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'user_id' => $userId,
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60) // 24 hours
        ]));
        $signature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, 'secret_key', true));
        return $header . '.' . $payload . '.' . $signature;
    }

    /**
     * Verify JWT token
     */
    public function verifyToken($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];

        $expectedSignature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, 'secret_key', true));

        if ($signature !== $expectedSignature) {
            return false;
        }

        $decoded = json_decode(base64_decode($payload), true);
        if ($decoded['exp'] < time()) {
            return false;
        }

        return $decoded;
    }
}
?>