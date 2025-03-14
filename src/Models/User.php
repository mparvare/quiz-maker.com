<?php
namespace Src\Models;

use Core\Database;

class User {
    private $id;
    private $email;
    private $username;
    private $password;
    private $fullName;
    private $role;
    private $active;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getFullName() {
        return $this->fullName;
    }

    public function getRole() {
        return $this->role;
    }

    public function isActive() {
        return $this->active;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
        return $this;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'fullName' => $this->fullName,
            'role' => $this->role,
            'active' => $this->active,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
?>