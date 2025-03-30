<?php

class baseDatos {

    const HOST = "galaperfecta.cfyayigqgbtk.mx-central-1.rds.amazonaws.com";
    const USER = "admin";
    const PASSWORD = "4gHBaQMALL6OltzNwhq1";
    const DATABASE = "gala";

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
