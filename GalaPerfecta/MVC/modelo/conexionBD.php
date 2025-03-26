<?php

class baseDatos {

    const HOST = "galaperfecta.cfyayigqgbtk.mx-central-1.rds.amazonaws.com";
    const USER = "admin";
    const PASSWORD = "4gHBaQMALL6OltzNwhq1";
    const DATABASE = "gala";

    // Propiedad para almacenar la única instancia de la conexión
    private static $instancia = null;

    // Constructor privado para evitar instanciación directa
    private function __construct() {
        // Evita que se cree una instancia desde fuera
    }

    // Clonación deshabilitada
    private function __clone() {
        // Evita que se clone la instancia
    }

    // Método para obtener la única instancia de la conexión
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
