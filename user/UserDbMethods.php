<?php
class UserDbMethods
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/../includes/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    function createUser($email, $password)
    {
        $stmt = $this->con->prepare("INSERT INTO user (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $password);
        if ($stmt->execute())
            return true;
        return false;
    }

    function getUsers()
    {
        $stmt = $this->con->prepare("SELECT id, email, password from user");
        $stmt->execute();
        $stmt->bind_result($id, $email, $password);
        $users = array();
        while ($stmt->fetch()) {
            $user = array();
            $user['id'] = $id;
            $user['email'] = $email;
            $user['password'] = $password;
            array_push($users, $user);
        }
        return $users;
    }

    function updateUser($id, $email, $password)
    {
        $stmt = $this->con->prepare("UPDATE user SET email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $email, $password, $id);
        if ($stmt->execute())
            return true;
        return false;
    }

    function deleteUser($id)
    {
        $stmt = $this->con->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute())
            return true;
        return false;
    }
}
