<?php
require_once "conexionBD.php";

/*
class consultaEventos
{
    private $eventoConexion;
    public function __construct($conexion)
    {
        $this->eventoConexion = $conexion;
    }

    public function consultaImagen() {}
}
*/

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
    private const API_BASE_URL = "http://localhost:3002"; // Cambiado a un puerto típico para Node.js

    public function __construct($correoIngresado)
    {
        $this->cargarDatos($correoIngresado);
    }

    private function cargarDatos($correoIngresado)
    {
        try {
            $url = self::API_BASE_URL . '/correo/' . urlencode($correoIngresado);

            // Inicializar cURL
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

                if ($data && is_array($data)) {
                    $usuario = $data['usuario'] ?? null;
                    $token = $data['token'] ?? null;

                    if ($usuario) {
                        $this->idUsuarios = $usuario["id_usuarios"] ?? null;
                        $this->nombre = $usuario['nombre'] ?? '';
                        $this->apellido = $usuario['apellido'] ?? '';
                        $this->correo = $usuario['correo'] ?? '';
                        $this->numeroTelefono = $usuario['numero_telefono'] ?? '';
                        $this->password = $usuario['password'] ?? '';
                        $this->tipoUsuario = $usuario['id_tipo_user'] ?? null;
                    }

                    // Guardar el token si está disponible
                    if ($token) {
                        $this->token = $token;
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['token'] = $token;
                    }
                } else {
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

    // Agregar un método para obtener el token
    public function getToken()
    {
        return $this->token ?? null;
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
}

class ValidadorUsuario
{
    public $pruebaID;

    public function __construct() {}

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
                    "tipoUsuario" => $usuario->getTipoUsuario(),
                    "token" => $usuario->getToken()
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

class TodosLosUsuarios
{
    private $usuarios = [];
    private const API_BASE_URL = "http://localhost:3002"; // Cambiado a un puerto típico para Node.js

    public function __construct()
    {
        $this->cargarUsuarios();
    }

    private function cargarUsuarios()
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];
            $url = self::API_BASE_URL . '/usuarios';

            // Inicializar cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            curl_close($ch);

            if ($httpCode == 200) {
                $data = json_decode($response, true);

                if ($data && is_array($data)) {
                    foreach ($data as $usuario) {
                        $this->usuarios[] = [
                            'id_usuarios' => $usuario['id_usuarios'] ?? null,
                            'nombre' => $usuario['nombre'] ?? '',
                            'apellido' => $usuario['apellido'] ?? '',
                            'correo' => $usuario['correo'] ?? '',
                            'numero_telefono' => $usuario['numero_telefono'] ?? '',
                            'password' => $usuario['password'] ?? '',
                            'id_tipo_user' => $usuario['id_tipo_user'] ?? null
                        ];
                    }
                } else {
                    throw new Exception("Respuesta del servidor no válida");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al cargar los datos de los usuarios: " . $e->getMessage());
        }
    }

    public function getUsuarios()
    {
        return $this->usuarios;
    }

    public function eliminarUsuario($id_usuario)
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];
            $url = self::API_BASE_URL . '/usuarios/' . $id_usuario;

            // Inicializar cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            curl_close($ch);

            if ($httpCode == 200 || $httpCode == 204) {
                // Actualizar la lista local de usuarios eliminando el usuario
                $this->usuarios = array_filter($this->usuarios, function ($usuario) use ($id_usuario) {
                    return $usuario['id_usuarios'] != $id_usuario;
                });
                return true;
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }

    public function actualizarUsuario($id_usuario, $nombre, $apellido, $correo, $numero_telefono, $password, $id_tipo_user)
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];
            $url = self::API_BASE_URL . '/usuarios/' . $id_usuario;

            // Preparar los datos para enviar a la API
            $data = [
                'id_usuarios' => $id_usuario,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'numero_telefono' => $numero_telefono,
                'password' => $password,
                'id_tipo_user' => $id_tipo_user
            ];

            // Inicializar cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  // Usar PUT para actualizar
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            curl_close($ch);

            if ($httpCode == 200) {
                // Actualizar el usuario en la lista local
                foreach ($this->usuarios as $key => $usuario) {
                    if ($usuario['id_usuarios'] == $id_usuario) {
                        $this->usuarios[$key] = [
                            'id_usuarios' => $id_usuario,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'correo' => $correo,
                            'numero_telefono' => $numero_telefono,
                            'password' => $password,
                            'id_tipo_user' => $id_tipo_user
                        ];
                        break;
                    }
                }
                return true;
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
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
//Esta Aun no queda EVENTO ES LA QUE SE ME DIFICULTA
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
//Igual aun no esta listo es muy dificl;
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
//ya esta el carrusel
class imagenesParaElCarrusel
{
    private $apiUrl;

    public function __construct()
    {
        // URL base del endpoint de la API
        $this->apiUrl = "http://localhost:3002/carrusel"; // Cambia el puerto si es necesario
    }

    public function obtenerPaquetesSinUsuario()
    {
        try {
            // Inicializar cURL
            $ch = curl_init($this->apiUrl);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar el código de respuesta HTTP
            if ($httpCode >= 200 && $httpCode < 300) {
                // Decodificar la respuesta JSON
                $data = json_decode($response, true);
                if (is_array($data)) {
                    return $data; // Retornar los datos obtenidos
                } else {
                    throw new Exception("Respuesta del servidor no válida.");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            // Manejo de errores
            echo "Error al obtener paquetes: " . $e->getMessage();
            return [];
        }
    }
}

class EventoInsercion
{
    private $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = "http://localhost:3002";
    }

    public function insertarEvento($nombre_evento): void
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];

            // Preparar los datos para enviar a la API
            $data = [
                'nombre_evento' => $nombre_evento
            ];

            // Inicializar cURL
            $ch = curl_init($this->apiBaseUrl . '/eventos');

            // Configurar la solicitud cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar la respuesta
            if ($httpCode >= 200 && $httpCode < 300) {
                echo "Evento insertado correctamente.";
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            echo "Error al insertar el evento: " . $e->getMessage();
        }
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
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];
            $url = "http://localhost:3002/servicios"; // URL del endpoint de servicios

            // Inicializar cURL
            $ch = curl_init($url);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar el código de respuesta HTTP
            if ($httpCode >= 200 && $httpCode < 300) {
                $data = json_decode($response, true);
                if (is_array($data)) {
                    return $data; // Retorna los servicios como un array
                } else {
                    throw new Exception("Respuesta del servidor no válida.");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            echo "Error al obtener servicios: " . $e->getMessage();
            return [];
        }
    }


    public function obtenerEventos()
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];
            $url = "http://localhost:3002/eventos"; // URL del endpoint de eventos

            // Inicializar cURL
            $ch = curl_init($url);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $data = json_decode($response, true);
                if (is_array($data)) {
                    return $data;
                } else {
                    throw new Exception("Respuesta del servidor no válida.");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            echo "Error al obtener eventos: " . $e->getMessage();
            return [];
        }
    }
    public function insertarPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3)
    {
        try {
            // Llamar a insertarEnPaquete y obtener la respuesta
            $response = $this->insertarEnPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3);

            // Verificar si la inserción fue exitosa y obtener el ID del paquete
            if ($response && isset($response['id_paquete'])) {
                return $response['id_paquete'];
            } else {
                throw new Exception("No se pudo obtener el ID del paquete insertado");
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }


    public function insertarEnPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1 = null, $ruta_imagen2 = null, $ruta_imagen3 = null)
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];
            $url = "http://localhost:3002/insertarPaquete";
            $data = [
                'id_eventos' => $id_eventos,
                'nombre_paquete' => $nombre_paquete,
                'ruta_imagen' => "../../img/" . $ruta_imagen,
                'descripcion' => $descripcion,
                'ruta_imagen1' => "../../img/" . $ruta_imagen1,
                'ruta_imagen2' => "../../img/" . $ruta_imagen2,
                'ruta_imagen3' => "../../img/" . $ruta_imagen3
            ];

            // Inicializar cURL
            $ch = curl_init($url);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar el código de respuesta HTTP
            if ($httpCode >= 200 && $httpCode < 300) {
                $data = json_decode($response, true);
                if (isset($data['message'])) {
                    return $data; // Retorna la respuesta del servidor
                } else {
                    throw new Exception("Respuesta del servidor no válida.");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            echo "Error al insertar el paquete: " . $e->getMessage();
            return null;
        }
    }


    public function registrarServiciosPaquete($id_paquete, $servicios)
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }
           
            $token = $_SESSION['token'];

            // Verificar que los servicios sean un array no vacío
            if (!is_array($servicios) || empty($servicios)) {
                throw new Exception("La lista de servicios debe ser un array no vacío.");
            }

            // Construir la URL del endpoint
            $url ="http://localhost:3002"."/serviciosXpaquete/". $id_paquete;

            // Preparar los datos para enviar
            $data = [
                "servicios" => $servicios
            ];

            // Inicializar cURL
            $ch = curl_init($url);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar el código de respuesta HTTP
            if ($httpCode >= 200 && $httpCode < 300) {
                $data = json_decode($response, true);
                if (isset($data['message'])) {
                    return $data['message'];
                } else {
                    throw new Exception("Respuesta del servidor no válida.");
                }
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al registrar servicios en el paquete: " . $e->getMessage());
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
//Parcialmente terminado
class obtenerPacks
{
    private $apiBaseUrl;
    private $evento_id = null;
    public $paquetes = [];
    public $total_servicios_evento = 0;

    public function __construct($evento_id = null)
    {
        $this->apiBaseUrl = "http://localhost:3002";
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
        try {
            $url = $this->apiBaseUrl . "/eventos"; // Endpoint de eventos
            $response = $this->realizarSolicitudGET($url);

            if (is_array($response)) {
                return $response; // Retorna los eventos como un array
            } else {
                throw new Exception("Respuesta del servidor no válida.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al obtener eventos: " . $e->getMessage());
        }
    }

    private function obtenerPaquetes()
    {
        try {
            $url = $this->apiBaseUrl . "/paquetesEvento/{$this->evento_id}"; // Endpoint de paquetes
            $response = $this->realizarSolicitudGET($url);

            if (is_array($response)) {
                return $response; // Retorna los paquetes como un array
            } else {
                throw new Exception("Respuesta del servidor no válida.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al obtener paquetes: " . $e->getMessage());
        }
    }

    private function calcularTotalServiciosEvento()
    {
        try {
            $url = $this->apiBaseUrl . "/total/{$this->evento_id}"; // Endpoint de total de servicios
            $response = $this->realizarSolicitudGET($url);

            if (isset($response['total_servicios'])) {
                return $response['total_servicios']; // Retorna el total de servicios
            } else {
                throw new Exception("Respuesta del servidor no válida.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al calcular el total de servicios: " . $e->getMessage());
        }
    }

    private function realizarSolicitudGET($url)
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];

            // Inicializar cURL
            $ch = curl_init($url);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar el código de respuesta HTTP
            if ($httpCode >= 200 && $httpCode < 300) {
                return json_decode($response, true); // Decodificar la respuesta JSON
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al realizar la solicitud GET: " . $e->getMessage());
        }
    }
}
//Ya terminado
class cotizacionInsercion
{
    private $apiBaseUrl;

    public function __construct()
    {
        // URL base de la API
        $this->apiBaseUrl = "http://localhost:3002"; // Cambia el puerto si es necesario
    }

    public function obtenerServiciosCotizacion()
    {
        try {
            $url = $this->apiBaseUrl . "/servicios"; // Endpoint de servicios
            $response = $this->realizarSolicitudGET($url);

            if (is_array($response)) {
                return $response; // Retorna los servicios como un array
            } else {
                throw new Exception("Respuesta del servidor no válida.");
            }
        } catch (Exception $e) {
            echo "Error al obtener servicios: " . $e->getMessage();
            return [];
        }
    }

    public function obtenerEventosCotizacion()
    {
        try {
            $url = $this->apiBaseUrl . "/eventos"; // Endpoint de eventos
            $response = $this->realizarSolicitudGET($url);

            if (is_array($response)) {
                return $response; // Retorna los eventos como un array
            } else {
                throw new Exception("Respuesta del servidor no válida.");
            }
        } catch (Exception $e) {
            echo "Error al obtener eventos: " . $e->getMessage();
            return [];
        }
    }

    private function realizarSolicitudGET($url)
    {
        try {
            // Verificar si el token está disponible en la sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['token'])) {
                throw new Exception("Token no disponible. Por favor, inicie sesión.");
            }

            $token = $_SESSION['token'];

            // Inicializar cURL
            $ch = curl_init($url);

            // Configurar cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token // Incluir el token en los encabezados
            ]);

            // Ejecutar la solicitud
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Manejo de errores de cURL
            if (curl_errno($ch)) {
                throw new Exception("Error en la conexión: " . curl_error($ch));
            }

            // Cerrar la conexión cURL
            curl_close($ch);

            // Verificar el código de respuesta HTTP
            if ($httpCode >= 200 && $httpCode < 300) {
                return json_decode($response, true); // Decodificar la respuesta JSON
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['error'] ?? "Error del servidor (Código HTTP: $httpCode)";
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            throw new Exception("Error al realizar la solicitud GET: " . $e->getMessage());
        }
    }
}
