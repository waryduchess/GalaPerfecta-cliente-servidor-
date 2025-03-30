<?php
require_once "conexionBD.php";


class consultaEventos
{
    private $eventoConexion;
    public function __construct($conexion)
    {
        $this->eventoConexion = $conexion;
    }

    public function consultaImagen() {}
}


class Usuario
{
    private $nombre;
    private $apellido;
    private $correo;
    private $numeroTelefono;
    private $password;
    private $tipoUsuario;
    private $idUsuarios;
    private $token;
    private const API_BASE_URL = "http://localhost:3306"; 
    public function __construct($correoIngresado)
    {
        $this->cargarDatos($correoIngresado);
    }
    
    private function cargarDatos($correoIngresado)
    {
        try {
            $url = self::API_BASE_URL . '/correo/' . urlencode($correoIngresado);
           
            $ch = curl_init($url);
           
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
           
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
           
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }
           
            curl_close($ch);
           
            if ($httpCode == 200) {
                $data = json_decode($response, true);
                if ($data && isset($data['usuario']) && isset($data['token'])) {
                    $this->token = $data['token'];
                    $usuario = $data['usuario'];
                    
                    $this->idUsuarios = $usuario["id_usuarios"] ?? null;
                    $this->nombre = $usuario['nombre'] ?? '';
                    $this->apellido = $usuario['apellido'] ?? '';
                    $this->correo = $usuario['correo'] ?? '';
                    $this->numeroTelefono = $usuario['numero_telefono'] ?? '';
                    $this->password = $usuario['password'] ?? ''; 
                    $this->tipoUsuario = $usuario['id_tipo_user'] ?? null;
                } 
                elseif ($data && is_array($data)) {
                    $this->idUsuarios = $data["id_usuarios"] ?? null;
                    $this->nombre = $data['nombre'] ?? '';
                    $this->apellido = $data['apellido'] ?? '';
                    $this->correo = $data['correo'] ?? '';
                    $this->numeroTelefono = $data['numero_telefono'] ?? '';
                    $this->password = $data['password'] ?? '';
                    $this->tipoUsuario = $data['id_tipo_user'] ?? null;
                    $this->token = null;  } 
                else {
                    throw new Exception("Respuesta del servidor no válida");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al cargar los datos del usuario: " . $e->getMessage());
        }
    }
    
    public function getIdUsuarios()
    {
        return $this->idUsuarios;
    }
    
    public function getNombre()
    {
        return $this->nombre;
    }
    
    public function getApellido()
    {
        return $this->apellido;
    }
    
    public function getCorreo()
    {
        return $this->correo;
    }
    
    public function getNumeroTelefono()
    {
        return $this->numeroTelefono;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getTipoUsuario()
    {
        return $this->tipoUsuario;
    }
    
    public function getToken()
    {
        return $this->token;
    }
    
    // Método para usar el token en solicitudes posteriores
    public function hacerSolicitudAutenticada($endpoint, $metodo = 'GET', $datos = null)
    {
        if (!$this->token) {
            throw new Exception("No hay token disponible para autenticar la solicitud");
        }
        
        $url = self::API_BASE_URL . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);
        
        if ($metodo === 'POST' || $metodo === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
            if ($datos) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception("Error en la conexión: " . curl_error($ch));
        }
        
        curl_close($ch);
        
        return [
            'codigo' => $httpCode,
            'respuesta' => json_decode($response, true)
        ];
    }
}

class ValidadorUsuario
{
    public $pruebaID;

    public function __construct()
    {
    }

    public function validarCredenciales($correoIngresado, $contraIngresada)
    {
        try {
            if (empty($correoIngresado) || empty($contraIngresada)) {
                throw new Exception("Correo y contraseña son requeridos");
            }
            $usuario = new Usuario($correoIngresado);
            $this->pruebaID = $usuario->getIdUsuarios();
            $contraseñaAlmacenada = $usuario->getPassword();
            if ($usuario->getCorreo() === $correoIngresado && $contraseñaAlmacenada === $contraIngresada) {
                return [
                    "status" => true,
                    "idUsuario" => $usuario->getIdUsuarios(),
                    "nombreUsuario" => $usuario->getNombre(),
                    "correo" => $usuario->getCorreo(),
                    "tipoUsuario" => $usuario->getTipoUsuario()
                ];
            } else {
                throw new Exception("Credenciales incorrectas");
            }
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage()
            ];
        }
    }
}

class UsuarioInsercion
{
    private $api_url;

    public function __construct($api_url)
    {
        $this->api_url = $api_url;
    }

    public function insertarUsuario($nombre, $apellido, $correo, $numero_telefono, $password): void
    {
        try {
            $this->insertarViaAPI($nombre, $apellido, $correo, $numero_telefono, $password);
            echo "";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function insertarViaAPI($nombre, $apellido, $correo, $numero_telefono, $password): void
    {
        // Preparar los datos para enviar a la API
        $data = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'numero_telefono' => $numero_telefono,
            'password' => $password,
            'id_tipo_user' => 1
        ];

        // Inicializar cURL
        $ch = curl_init($this->api_url . '/usuarios');

        // Configurar la solicitud cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',

        ]);

        // Ejecutar la solicitud
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Cerrar la conexión cURL
        curl_close($ch);

        // Verificar la respuesta
        if ($http_code >= 400) {
            $error = json_decode($response, true);
            throw new Exception(isset($error['error']) ? $error['error'] : 'Error al insertar usuario');
        }
    }
}

class Evento
{
    private $conn;
    private $evento_id;
    public $nombre_evento;
    public $paquetes = [];
    public $usuarios = [];
    public $total_evento = 0; // Total general de todos los paquetes

    public function __construct($conn, $evento_id)
    {
        $this->conn = $conn;
        $this->evento_id = $evento_id;
        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        try {
            $this->nombre_evento = $this->obtenerNombreEvento();
            $this->paquetes = $this->obtenerPaquetes();
            $this->usuarios = $this->obtenerUsuarios();
        } catch (Exception $e) {
            throw new Exception("Error al cargar los datos del evento: " . $e->getMessage());
        }
    }

    private function obtenerNombreEvento()
    {
        $sql = "SELECT nombre_evento FROM eventos WHERE id_eventos = :id_eventos";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_eventos', $this->evento_id, PDO::PARAM_INT); // Asociar el valor usando PDO
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nombre_evento'] ?? 'Nombre del evento no encontrado';
    }


    private function obtenerPaquetes()
    {
        $sql = "SELECT id_paquete, nombre_paquete, ruta_imagen, descripcion, ruta_imagen1, ruta_imagen2, ruta_imagen3 
                FROM paquetes WHERE id_eventos = :id_eventos";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_eventos', $this->evento_id, PDO::PARAM_INT);
        $stmt->execute();
        $paquetes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $servicios = $this->obtenerServicios($row['id_paquete']);
            $total_paquete = $this->calcularTotalServicios($servicios);
            $paquetes[] = [
                'id_paquete' => $row['id_paquete'],
                'nombre_paquete' => $row['nombre_paquete'],
                'ruta_imagen' => $row['ruta_imagen'],
                'descripcion' => $row['descripcion'],
                'servicios' => $servicios,
                'ruta_imagen1' => $row['ruta_imagen1'],
                'ruta_imagen2' => $row['ruta_imagen2'],
                'ruta_imagen3' => $row['ruta_imagen3'],
                'total_paquete' => $total_paquete,
            ];
            $this->total_evento += $total_paquete;
        }
        return $paquetes;
    }
    //ERRRRRIUBHBDHBDZBDBY    

    private function obtenerServicios($paquete_id)
    {
        $sql = "SELECT s.id_servicio, s.nombre_servicio, s.descripcion, s.precio_servicio 
                FROM servicios s
                INNER JOIN paquete_servicio ps ON s.id_servicio = ps.id_servicio
                WHERE ps.id_paquete = :id_paquete";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_paquete', $paquete_id, PDO::PARAM_INT);
        $stmt->execute();
        $servicios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $servicios[] = $row;
        }
        return $servicios;
    }


    private function obtenerUsuarios()
    {
        $sql = "SELECT u.id_usuarios, u.nombre, u.apellido, u.correo 
                FROM usuarios u
                INNER JOIN paquetes p ON u.id_usuarios = p.id_usuarios
                WHERE p.id_eventos = :id_eventos";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_eventos', $this->evento_id, PDO::PARAM_INT);
        $stmt->execute();
        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }


    private function calcularTotalServicios($servicios)
    {
        $total = 0;
        foreach ($servicios as $servicio) {
            $total += $servicio['precio_servicio'];
        }
        return $total;
    }
}
class NuestrosEventos
{
    private $db;

    public function __construct()
    {
        $this->db = baseDatos::conectarBD();
    }

    public function obtenerEvento($evento_id)
    {
        try {
            $evento = new Evento($this->db, $evento_id);
            if (empty($evento->nombre_evento)) {
                throw new Exception("Evento no encontrado.");
            }
            return $evento;
        } catch (Exception $e) {
            error_log("Error al obtener evento: " . $e->getMessage());
            return null;
        }
    }
}
class imagenesParaElCarrusel
{
    private $db;

    public function __construct()
    {;
        $this->db = baseDatos::conectarBD();
    }

    public function obtenerPaquetesSinUsuario()
    {
        $query = "SELECT id_paquete, ruta_imagen FROM paquetes WHERE id_usuarios IS NULL";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error al obtener paquetes: " . $e->getMessage();
            return [];
        }
    }
}

class EventoInsercion
{
    private $db;

    public function __construct()
    {
        $this->db = baseDatos::conectarBD();
    }

    public function insertarEvento($nombre_evento): void
    {
        try {
            // Iniciar la transacción
            $this->db->beginTransaction();


            $this->insertarEnEvento($nombre_evento);

            // Confirmar la transacción
            $this->db->commit();
            echo "";
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $this->db->rollback();
            echo "Error: " . $e->getMessage();
        }
    }

    private function insertarEnEvento($nombre_evento): void
    {
        $query = "INSERT INTO eventos (nombre_evento) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$nombre_evento]);
    }
}

class PaqueteInsercion
{
    private $db;

    public function __construct()
    {
        $this->db = baseDatos::conectarBD();
    }

    public function obtenerServicios()
    {
        $query = "SELECT id_servicio, nombre_servicio FROM servicios";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener servicios: " . $e->getMessage();
            return [];
        }
    }

    public function obtenerEventos()
    {
        $query = "SELECT id_eventos, nombre_evento FROM eventos";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener eventos: " . $e->getMessage();
            return [];
        }
    }





    public function insertarPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3)
    {
        try {

            $this->db->beginTransaction();

            $this->insertarEnPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3);

            // Obtener el id del paquete insertado
            $id_paquete = $this->db->lastInsertId();

            $this->db->commit();

            return $id_paquete;
        } catch (Exception $e) {

            $this->db->rollback();
            echo "Error: " . $e->getMessage();
            return null;
        }
    }


    private function insertarEnPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3): int
    {
        $query = "INSERT INTO paquetes (id_eventos, id_usuarios, nombre_paquete, ruta_imagen, descripcion, ruta_imagen1, ruta_imagen2, ruta_imagen3) 
                  VALUES (?, NULL, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3]);


        return $this->db->lastInsertId();
    }


    public function registrarServiciosPaquete($id_paquete, $servicios)
    {
        $query = "INSERT INTO paquete_servicio (id_paquete, id_servicio) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);

        foreach ($servicios as $id_servicio) {
            $stmt->execute([$id_paquete, $id_servicio]);
        }
    }
}

class ServicioInsercion
{
    private $apiUrl;
    private $apiToken;
    public function __construct($apiUrl, $apiToken)
    {
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
    }
    public function insertarServicio($nombre_servicio, $descripcion, $precio_servicio): void
    {
        try {
            $data = [
                'nombre_servicio' => $nombre_servicio,
                'descripcion' => $descripcion,
                'precio_servicio' => $precio_servicio
            ];
            $ch = curl_init($this->apiUrl . '/servicios');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiToken
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('Error en la solicitud cURL: ' . curl_error($ch));
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $responseData = json_decode($response, true);
            if ($httpCode >= 200 && $httpCode < 300) {
                echo "Servicio agregado correctamente. ID: " . ($responseData['id'] ?? 'N/A');
            } else {
                throw new Exception($responseData['error'] ?? 'Error desconocido');
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}


class Tarjeta
{
    private $db;

    public function __construct()
    {
        $this->db = baseDatos::conectarBD();
    }

    public function insertar($idUsuario, $nombreTitular, $numeroTarjeta, $fechaVencimiento, $cvv)
    {
        try {
            $sql = "INSERT INTO tarjetas (id_usuarios, nombre_titular, numero_tarjeta, fecha_vencimiento, cvv) 
                    VALUES (:id_usuarios, :nombre_titular, :numero_tarjeta, :fecha_vencimiento, :cvv)";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':id_usuarios', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_titular', $nombreTitular, PDO::PARAM_STR);
            $stmt->bindParam(':numero_tarjeta', $numeroTarjeta, PDO::PARAM_STR); // Puede ser INT o BIGINT en la BD
            $stmt->bindParam(':fecha_vencimiento', $fechaVencimiento, PDO::PARAM_STR);
            $stmt->bindParam(':cvv', $cvv, PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error al insertar tarjeta: " . $e->getMessage();
            return false;
        }
    }
}
class Pagos
{
    private $db;

    public function __construct()
    {
        $this->db = baseDatos::conectarBD();
    }

    // Método para registrar un pago al contado
    public function registrarPagoContado($idUsuarios, $idPaquete, $montoTotal, $fechaPago)
    {
        $tipoPago = 'contado';
        $query = "INSERT INTO pagos (id_usuarios, id_paquete, monto_total, tipo_pago, fecha_pago) 
                  VALUES (:id_usuarios, :id_paquete, :monto_total, :tipo_pago, :fecha_pago)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id_usuarios', $idUsuarios);
        $stmt->bindParam(':id_paquete', $idPaquete);
        $stmt->bindParam(':monto_total', $montoTotal);
        $stmt->bindParam(':tipo_pago', $tipoPago);
        $stmt->bindParam(':fecha_pago', $fechaPago);

        if ($stmt->execute()) {
            return "Pago al contado registrado exitosamente.";
        } else {
            return "Error al registrar el pago al contado.";
        }
    }

    // Método para registrar un pago a plazos
    public function registrarPagoPlazos($idUsuarios, $idPaquete, $montoTotal, $fechaPago, $plazos)
    {
        try {
            $this->db->beginTransaction();

            // Registrar el pago principal
            $tipoPago = 'plazos';
            $queryPago = "INSERT INTO pagos (id_usuarios, id_paquete, monto_total, tipo_pago, fecha_pago) 
                          VALUES (:id_usuarios, :id_paquete, :monto_total, :tipo_pago, :fecha_pago)";
            $stmtPago = $this->db->prepare($queryPago);

            $stmtPago->bindParam(':id_usuarios', $idUsuarios);
            $stmtPago->bindParam(':id_paquete', $idPaquete);
            $stmtPago->bindParam(':monto_total', $montoTotal);
            $stmtPago->bindParam(':tipo_pago', $tipoPago);
            $stmtPago->bindParam(':fecha_pago', $fechaPago);

            $stmtPago->execute();
            $idPago = $this->db->lastInsertId();

            // Registrar los plazos
            $queryPlazo = "INSERT INTO pagos_plazos (id_pago, numero_plazo, monto_plazo, fecha_pago, estado_pago) 
                           VALUES (:id_pago, :numero_plazo, :monto_plazo, :fecha_pago, :estado_pago)";
            $stmtPlazo = $this->db->prepare($queryPlazo);

            foreach ($plazos as $plazo) {
                $stmtPlazo->bindParam(':id_pago', $idPago);
                $stmtPlazo->bindParam(':numero_plazo', $plazo['numero_plazo']);
                $stmtPlazo->bindParam(':monto_plazo', $plazo['monto_plazo']);
                $stmtPlazo->bindParam(':fecha_pago', $plazo['fecha_pago']);
                $stmtPlazo->bindValue(':estado_pago', 'pendiente');

                $stmtPlazo->execute();
            }

            $this->db->commit();
            return "Pago a plazos registrado exitosamente.";
        } catch (Exception $e) {
            $this->db->rollBack();
            return "Error al registrar el pago a plazos: " . $e->getMessage();
        }
    }
}
class obtenerPacks
{
    private $conn;
    private $evento_id = null; // Por defecto es null
    public $paquetes = [];
    public $total_servicios_evento = 0;

    public function __construct($evento_id = null)
    {
        $this->conn = baseDatos::conectarBD();
        $this->evento_id = $evento_id;

        if ($this->evento_id) {
            $this->cargarPaquetesYServicios();
        }
    }

    private function cargarPaquetesYServicios()
    {
        try {
            if ($this->evento_id) {
                $this->paquetes = $this->obtenerPaquetes();
                $this->total_servicios_evento = $this->calcularTotalServiciosEvento();
            }
        } catch (Exception $e) {
            throw new Exception("Error al cargar los datos: " . $e->getMessage());
        }
    }

    public function obtenerTodosLosEventos()
    {
        $sql = "SELECT id_eventos, nombre_evento FROM eventos";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = [
                'id_eventos' => $row['id_eventos'],
                'nombre_evento' => $row['nombre_evento']
            ];
        }
        return $eventos;
    }

    private function obtenerPaquetes()
    {
        $sql = "SELECT id_paquete, nombre_paquete 
                FROM paquetes 
                WHERE id_eventos = :id_eventos";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_eventos', $this->evento_id, PDO::PARAM_INT);
        $stmt->execute();

        $paquetes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paquetes[] = [
                'id_paquete' => $row['id_paquete'],
                'nombre_paquete' => $row['nombre_paquete']
            ];
        }
        return $paquetes;
    }

    private function calcularTotalServiciosEvento()
    {
        $sql = "SELECT SUM(s.precio_servicio) AS total_servicios
                FROM servicios s
                INNER JOIN paquete_servicio ps ON s.id_servicio = ps.id_servicio
                INNER JOIN paquetes p ON ps.id_paquete = p.id_paquete
                WHERE p.id_eventos = :id_eventos";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_eventos', $this->evento_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_servicios'] ?? 0;
    }
}

class cotizacionInsercion
{
    private $db;

    public function __construct()
    {
        $this->db = baseDatos::conectarBD();
    }

    public function obtenerServiciosCotizacion()
    {
        $query = "SELECT id_servicio, nombre_servicio FROM servicios";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener servicios: " . $e->getMessage();
            return [];
        }
    }

    public function obtenerEventosCotizacion()
    {
        $query = "SELECT id_eventos, nombre_evento FROM eventos";  // Verifica que los nombres de las columnas sean correctos

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devuelve los eventos como un array asociativo
        } catch (PDOException $e) {
            echo "Error al obtener eventos: " . $e->getMessage();
            return [];
        }
    }
}
