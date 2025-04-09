<?php

class baseDatos {

    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "";
    const DATABASE = "eventos";

    private static $instancia = null;

    private function __construct() {
    }

    private function __clone() {
    }

    public static function conectarBD() {
        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
                    "mysql:host=" . self::HOST . ";dbname=" . self::DATABASE,
                    self::USER,
                    self::PASSWORD
                );
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Error de conexión: " . $e->getMessage();
                exit();
            }
        }
        return self::$instancia;
    }
}

?>
