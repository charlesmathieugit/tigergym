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
     * Inscrire un utilisateur.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @return int|null L'ID de l'utilisateur créé ou null en cas d'échec.
     */
    public function get_user_by_mail(string $mail,bool $fetchAsObject = true){

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE mail = :mail");
        $stmt->execute([':mail' => $mail]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function get_user_by_id(string $id,bool $fetchAsObject = true){

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $fetchAsObject ? $stmt->fetch(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_admin(){
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE role = 1");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function register(string $username, string $email, string $password): ?int
    {
        // Hashage du mot de passe avant l'insertion
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return $this->create(['username', 'mail', 'pass'], $username, $email, $hashedPassword);
    }

    public function connection(string $email, string $password): ?int
    {   
        
       $user = $this->get_user_by_mail($email,true);
       ;
       if(password_verify($password, $user->pass) ){
        return 1;
       }else{
        return 0;
       }
    }
}
