<?php
class UserRepository
{
    public static function checkLogin($user, $password)
    {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM user WHERE name = '" . $user . "' AND password = '" . md5($password) . "';";
        $result = $bd->query($q);
        if ($datos = $result->fetch_assoc()) {
            return new User($datos);
        }
        return null;
    }

    public static function checkUserExists($username) {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM user WHERE name = '".$username."';";
        $result = $bd->query($q);
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public static function registerUser($user, $password) {
        $bd = Conectar::conexion();

        $q = "INSERT INTO user VALUES(NULL, '".$user."', '".md5($password)."', 1);";
        $result = $bd->query($q);
    }
}
?>