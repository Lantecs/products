<?php

class Logout
{
    private $db;
    private $token;

    public function __construct($db, $token)
    {
        $this->db = $db;
        $this->token = $token;
    }

    public function isTokenInvalidated()
    {
        $query = "SELECT * FROM jw_token WHERE token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();

        return $stmt->rowCount() > 0; // Return true if token is found
    }

    public function invalidateToken()
    {
        $query = "INSERT INTO jw_token (token) VALUES (:token)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $this->token);
        return $stmt->execute(); // Return true if insertion is successful
    }

    public function processLogout()
    {
        if ($this->isTokenInvalidated()) {
            return ['success' => false, 'message' => 'Token already invalidated.'];
        }

        if ($this->invalidateToken()) {
            return ['success' => true, 'message' => 'Logged out successfully. Token invalidated.'];
        } else {
            return ['success' => false, 'message' => 'Failed to invalidate token.'];
        }
    }
}
