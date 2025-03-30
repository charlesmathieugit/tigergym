<?php
namespace Models;

use PDO;

class UserModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users');
    }

    /**
     * Récupérer un utilisateur par son email
     */
    public function getUserByEmail(string $email, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un utilisateur par son ID
     */
    public function getUserById(string $id, bool $fetchAsObject = true)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les administrateurs
     */
    public function getAdminUsers()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE role = 'admin'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Inscrire un nouvel utilisateur
     */
    public function register(array $userData)
    {
        if ($this->getUserByEmail($userData['email'])) {
            return false;
        }

        $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT);
        
        $columns = ['email', 'password', 'firstname', 'lastname', 'role', 'created_at'];
        $values = [
            $userData['email'],
            $hashedPassword,
            $userData['firstname'],
            $userData['lastname'],
            'user',
            date('Y-m-d H:i:s')
        ];
        
        return $this->create($columns, ...$values);
    }

    /**
     * Authentifier un utilisateur
     */
    public function authenticate(string $email, string $password)
    {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            // Créer la session
            $_SESSION['user'] = [
                'id' => $user->id,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'role' => $user->role
            ];
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(int $userId, array $userData)
    {
        $updates = [];
        $params = [':id' => $userId];

        // Construire la requête dynamiquement
        foreach (['firstname', 'lastname', 'email'] as $field) {
            if (isset($userData[$field])) {
                $updates[] = "{$field} = :{$field}";
                $params[":{$field}"] = $userData[$field];
            }
        }

        // Gérer le mot de passe séparément
        if (!empty($userData['password'])) {
            $updates[] = "password = :password";
            $params[':password'] = password_hash($userData['password'], PASSWORD_BCRYPT);
        }

        if (empty($updates)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Vérifier si un utilisateur est admin
     */
    public function isAdmin($userId)
    {
        $user = $this->getUserById($userId);
        return $user && $user->role === 'admin';
    }
}
