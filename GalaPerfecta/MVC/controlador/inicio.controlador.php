<?php

require_once "modelo/conexionBD.php";
require_once "modelo/consultasBD.php";


class inicioControlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/principal.php";
    }
}

class inicioControladorPrincipalCliente
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/principalCliente.php";
    }
}

class InicioControladorMenu
{
    private $eventosObtenidos;
    private $eventoId;

    public function __construct() {
        $this->eventosObtenidos = new NuestrosEventos(); 
        $this->eventoId = isset($_GET['id_paquete']) ? intval($_GET['id_paquete']) : 0;
    }

    public function mostrarEventos() {
        if ($this->eventoId > 0) {
            return $this->eventosObtenidos->obtenerEvento($this->eventoId);    
        } else {
            return "Seleccione un evento para ver más detalles.";
        }
    }

    public function inicio()
    {
        require_once "vista/menu.php";
    }
}

class inicioControladorLogin
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {

        require_once "vista/login.php";
    }
}
class inicioControladorpagoContado
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {

        require_once "vista/";
    }
}
class inicioControladorpagoPlazos
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {

        require_once "vista/";
    }
}

class inicioControladorCotizacion
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {

        require_once "vista/cotizacion.php";
    }
}

class inicioControladorPagos
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {

        require_once "vista/pagos.php";
    }
}

class inicioControladorCargaLogin 
{
    private $correo;
    private $password;
    private $validarUsuario;

    private $nombreUsuario;
    private $idUsuario;
    private $tipoUsuario;
    private $status;
    private $ideDeveritas;

    public function __construct() {
        $this->correo = $_POST['correo'] ?? null;
        $this->password = $_POST['password'] ?? null;
        $this->validarUsuario = new ValidadorUsuario();
        
    }

    public function validarUser() {
        $resultado = $this->validarUsuario->validarCredenciales($this->correo, $this->password);
        $this->ideDeveritas = $this->validarUsuario->pruebaID;

        if (isset($resultado['status']) && $resultado['status']) {
            $this->nombreUsuario = $resultado['nombreUsuario'] ?? null;
            $this->idUsuario = $resultado['idUsuario'] ?? null;
            $this->tipoUsuario = $resultado['tipoUsuario'] ?? null;
            $this->status = $resultado['status'];
        } else {
            echo htmlspecialchars("Usuario no encontrado");
        }
    }
    

    public function getNombreUsuario() {
        return $this->nombreUsuario;
    }
    public function getIDeUsuarioDeVerdad() {
        return $this->ideDeveritas;
    }

    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function getTipoUsuario() {
        return $this->tipoUsuario;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function getStatus() {
        return $this->status;
    }

    public function inicio()
    {

        require_once "vista/validacionLogin.php";
    }
}

class inicioControladorUsuario 
{
    private $insercionUsuario;
    public $insertada;

    public function __construct() {
        $this->insercionUsuario = new UsuarioInsercion();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $correo = $_POST['correo'];
            $numero_telefono = $_POST['numero_telefono'];
            $password = $_POST['password'];
            
           

            $this->insercionUsuario->insertarUsuario($nombre, $apellido, $correo, $numero_telefono, $password);
            $this->insertada = true;
        }else {
        $this->insertada = false;
        }
    }

    public function inicio()
    {

        require_once "vista/validacionRegistro.php";
    }

}

class inicioControladorAdmin
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/admin.php";
    }
}
class inicioControladorCrearEvento
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/registroEvento.php";
    }
}

class inicioControladorEvento 
{
    private $insercionEvento;
    public $insertada;

    public function __construct() {
        $this->insercionEvento = new EventoInsercion();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre_evento = $_POST['nombre_evento'];
        
            
           

            $this->insercionEvento->insertarEvento($nombre_evento);
            $this->insertada = true;
        }else {
        $this->insertada = false;
        }
    }

    public function inicio()
    {

        require_once "vista/validacionRegistroEvento.php";
    }

}

class inicioControladorCrearPaquete
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/registroPaquetes.php";
    }
}

class inicioControladorPaquete 
{
    private $insercionPaquete;
    public $insertada;

    public function __construct() {
        $this->insercionPaquete = new PaqueteInsercion();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recibir los datos del formulario
            $id_eventos = $_POST['id_eventos'];
            $nombre_paquete = $_POST['nombre_paquete'];
            $ruta_imagen = $_POST['ruta_imagen'];
            $descripcion = $_POST['descripcion'];
            $ruta_imagen1 = $_POST['ruta_imagen1'];
            $ruta_imagen2 = $_POST['ruta_imagen2'];
            $ruta_imagen3 = $_POST['ruta_imagen3'];

            $serviciosSeleccionados = $_POST['servicios'] ?? []; // Recibir servicios seleccionados
            
            // Insertar el paquete y obtener el id del paquete insertado
            $id_paquete = $this->insercionPaquete->insertarPaquete($id_eventos, $nombre_paquete, $ruta_imagen, $descripcion, $ruta_imagen1, $ruta_imagen2, $ruta_imagen3);
            if ($id_paquete) { // Si la inserción fue exitosa
                $this->insertada = true;

                // Registrar los servicios seleccionados en la tabla paquete_servicio
                if (!empty($serviciosSeleccionados)) {
                    $this->insercionPaquete->registrarServiciosPaquete($id_paquete, $serviciosSeleccionados);
                }

                echo "";
            } else {
                echo "Hubo un error al insertar el paquete.";
            }

        } else {
            $this->insertada = false;
        }
    }

    public function inicio() {
        require_once "vista/validacionRegistroPaquete.php";
    }
}

class inicioControladorCrearServicio
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/registroServicio.php";
    }
}

class inicioControladorServicio 
{
    private $insercionServicio;
    public $insertada;

    public function __construct() {
        $this->insercionServicio = new ServicioInsercion();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre_servicio = $_POST['nombre_servicio'];
            $descripcion = $_POST['descripcion'];
            $precio_servicio = $_POST['precio_servicio'];
        
            
           

            $this->insercionServicio->insertarServicio($nombre_servicio, $descripcion, $precio_servicio);
            $this->insertada = true;
        }else {
        $this->insertada = false;
        }
    }

    public function inicio()
    {

        require_once "vista/validacionRegistroServicio.php";
    }

}


class mandarAContado
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/pagoContado.php";
    }
}
class mandarAPlazos
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new consultaEventos(baseDatos::conectarBD());
    }

    public function inicio()
    {
        require_once "vista/pagosPlazos.php";
    }
}
class ControladorTarjeta {
    private $tarjeta;

    public function __construct() {
        $this->tarjeta = new Tarjeta();
    }

    public function procesarFormulario($datos) {
        if (empty($datos['id_usuarios']) || empty($datos['nombreTitular']) || empty($datos['numeroTarjeta']) || 
            empty($datos['fechaVencimiento']) || empty($datos['cvv'])) {
            return "Todos los campos son obligatorios.";
        }

        
        $resultado = $this->tarjeta->insertar(
            $datos['id_usuarios'], 
            $datos['nombreTitular'], 
            $datos['numeroTarjeta'], 
            $datos['fechaVencimiento'], 
            $datos['cvv']
        );

        return $resultado ? "Tarjeta registrada exitosamente." : "Error al registrar la tarjeta.";
    }
    public function inicio()
    {
        require_once "vista/seleccionTipoPago.php";
    }
}
class ProcesarPagoContado {
    private $pagos;
    private $datos;

    public function __construct( array $datos) {
        $this->pagos = new Pagos();
        $this->datos = $datos;
    }

    public function procesar() {
        $idUsuarios = $this->datos['id_usuarios'];
        $idPaquete = $this->datos['id_paquete'];
        $montoTotal = $this->datos['monto_total'];
        $fechaPago = $this->datos['fecha_pago'];

        return $this->pagos->registrarPagoContado($idUsuarios, $idPaquete, $montoTotal, $fechaPago);
    }
    public function inicio(){
        require_once "vista/";

    }
}
class ProcesarPagoPlazos {
    private $pagos;
    private $datos;

    public function __construct(array $datos = []) {
        $this->pagos = new Pagos();
        $this->datos = $datos;
    }

    // Método para procesar el registro del pago a plazos
    public function procesar() {
        $idUsuarios = $this->datos['id_usuarios'];
        $idPaquete = $this->datos['id_paquete'];
        $montoTotal = $this->datos['monto_total'];
        $fechaPago = $this->datos['fecha_pago'];

        // Aquí asumimos que los plazos vienen en un arreglo llamado 'plazos'
        $plazos = $this->datos['plazos']; // Array con detalles de cada plazo

        // Llamar al método de la clase Pagos para registrar el pago a plazos
        return $this->pagos->registrarPagoPlazos($idUsuarios, $idPaquete, $montoTotal, $fechaPago, $plazos);
    }
    public function inicio(){
    require_once "";
    }
}
class PaqueteController {
    private $packs;

    public function __construct() {
        $this->packs = new obtenerPacks(); // Inicializa sin evento_id
    }

    // Método para obtener todos los eventos
    public function obtenerEventos() {
        return $this->packs->obtenerTodosLosEventos();
    }

    // Método para obtener paquetes de un evento específico
    public function obtenerPaquetesPorEvento($evento_id) {
        $this->packs = new obtenerPacks($evento_id); // Reinstancia con evento_id
        return $this->packs->paquetes;
    }

    // Método para obtener el total de servicios de un evento específico
    public function obtenerTotalServiciosPorEvento($evento_id) {
        $this->packs = new obtenerPacks($evento_id); // Reinstancia con evento_id
        return $this->packs->total_servicios_evento;
    }
}







?>
