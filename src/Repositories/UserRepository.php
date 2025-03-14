<?php
namespace Src\Repositories;

use Core\Database;
use Src\Models\User;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        
        if ($stmt->rowCount() > 0) {
            return $this->createUserFromRow($stmt->fetch(\PDO::FETCH_ASSOC));
        }
        
        return null;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->query($sql, ['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            return $this->createUserFromRow($stmt->fetch(\PDO::FETCH_ASSOC));
        }
        
        return null;
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->query($sql, ['username' => $username]);
        
        if ($stmt->rowCount() > 0) {
            return $this->createUserFromRow($stmt->fetch(\PDO::FETCH_ASSOC));
        }
        
        return null;
    }

    public function findAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM users ORDER BY id DESC LIMIT :offset, :limit";
        $stmt = $this->db->query($sql, ['offset' => $offset, 'limit' => $perPage]);
        
        $users = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = $this->createUserFromRow($row);
        }
        
        return $users;
    }

    public function create(User $user) {
        $sql = "INSERT INTO users (email, username, password, full_name, role, active) 
                VALUES (:email, :username, :password, :fullName, :role, :active)";
        
        $params = [
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'fullName' => $user->getFullName(),
            'role' => $user->getRole(),
            'active' => $user->isActive() ? 1 : 0
        ];
        
        $this->db->query($sql, $params);
        
        return $this->findByEmail($user->getEmail());
    }

    public function update(User $user) {
        $sql = "UPDATE users SET 
                email = :email,
                username = :username,
                full_name = :fullName,
                role = :role,
                active = :active
                WHERE id = :id";
        
        $params = [
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'fullName' => $user->getFullName(),
            'role' => $user->getRole(),
            'active' => $user->isActive() ? 1 : 0,
            'id' => $user->getId()
        ];
        
        $this->db->query($sql, $params);
        
        return $this->findById($user->getId());
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
        
        return true;
    }

    public function countTotal() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return (int) $result['total'];
    }

    private function createUserFromRow(array $row) {
        $user = new User();
        $user->setId($row['id'])
             ->setEmail($row['email'])
             ->setUsername($row['username'])
             ->setFullName($row['full_name'])
             ->setRole($row['role'])
             ->setActive((bool) $row['active']);

        return $user;
    }
}
?>