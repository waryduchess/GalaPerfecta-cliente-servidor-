const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken'); 
const {connection} = require("../config/config.db");
const SECRET_KEY = 'EsperemosQueEstaClaveSiFuncionePlease2013460';

/**
 * Middleware para verificar el token JWT
 * 
 * Este middleware valida el token JWT enviado en el encabezado `Authorization`.
 * Si el token es válido, se agrega la información del usuario decodificada al objeto `req`.
 * Si el token no es válido o no se proporciona, se devuelve un error 401.
 * 
 * @param {Object} req - Objeto de solicitud HTTP
 * @param {Object} res - Objeto de respuesta HTTP
 * @param {Function} next - Función para pasar al siguiente middleware
 * 
 * @returns {Object} Respuesta HTTP con error 401 si el token no es válido o no se proporciona
 */
const verificarToken = (req, res, next) => {
    const token = req.headers.authorization?.split(' ')[1];
    
    if (!token) {
        return res.status(401).json({ 
            mensaje: 'Token no proporcionado' 
        });
    }

    try {
        const decoded = jwt.verify(token, SECRET_KEY);
        req.usuario = decoded;
        next();
    } catch (error) {
        return res.status(401).json({ 
            mensaje: 'Token inválido' 
        });
    }
};

/**
 * @swagger
 * /paquetesEvento/{id}:
 *   get:
 *     summary: Obtener paquetes asociados a un evento
 *     tags: [Paquetes]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento para obtener los paquetes asociados
 *     responses:
 *       200:
 *         description: Lista de paquetes asociados al evento
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 type: object
 *                 properties:
 *                   id_paquete:
 *                     type: integer
 *                     description: ID del paquete
 *                   nombre_paquete:
 *                     type: string
 *                     description: Nombre del paquete
 *       400:
 *         description: ID del evento no proporcionado o inválido
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       500:
 *         description: Error interno del servidor
 */
router.get('/paquetesEvento/:id',verificarToken, (req, res) => {
    const { id } = req.params;
    const sql = `
        SELECT id_paquete, nombre_paquete 
        FROM paquetes 
        WHERE id_eventos = ?`;

    connection.query(sql, [id], (err, results) => {
        if (err) {
            console.error("Error al obtener paquetes:", err);
            return res.status(500).json({ error: "Error interno del servidor" });
        }

        res.json(results);
    });
});

/**
 * @swagger
 * /paquetes:
 *   post:
 *     summary: Insertar un nuevo paquete
 *     tags: [Paquetes]
 *     security:
 *       - bearerAuth: []
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             properties:
 *               id_eventos:
 *                 type: integer
 *                 description: ID del evento al que pertenece el paquete
 *               nombre_paquete:
 *                 type: string
 *                 description: Nombre del paquete
 *               ruta_imagen:
 *                 type: string
 *                 description: Ruta de la imagen principal
 *               descripcion:
 *                 type: string
 *                 description: Descripción del paquete
 *               ruta_imagen1:
 *                 type: string
 *                 description: Ruta de la imagen secundaria 1
 *               ruta_imagen2:
 *                 type: string
 *                 description: Ruta de la imagen secundaria 2
 *               ruta_imagen3:
 *                 type: string
 *                 description: Ruta de la imagen secundaria 3
 *             required:
 *               - id_eventos
 *               - nombre_paquete
 *               - ruta_imagen
 *               - descripcion
 *     responses:
 *       200:
 *         description: Paquete insertado correctamente
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 message:
 *                   type: string
 *                 id_paquete:
 *                   type: integer
 *       400:
 *         description: Campos obligatorios faltantes
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       500:
 *         description: Error del servidor
 */
router.post('/insertarPaquete', verificarToken, (req, res) => {
    const {
        id_eventos,
        nombre_paquete,
        ruta_imagen,
        descripcion,
        ruta_imagen1,
        ruta_imagen2,
        ruta_imagen3
    } = req.body;

    // Validar campos obligatorios
    if (!id_eventos || !nombre_paquete || !ruta_imagen || !descripcion) {
        return res.status(400).json({ error: "Todos los campos obligatorios deben ser proporcionados" });
    }

    const query = `
        INSERT INTO paquetes (id_eventos, id_usuarios, nombre_paquete, ruta_imagen, descripcion, ruta_imagen1, ruta_imagen2, ruta_imagen3) 
        VALUES (?, NULL, ?, ?, ?, ?, ?, ?)
    `;

    connection.query(
        query,
        [id_eventos, nombre_paquete, ruta_imagen, descripcion, ruta_imagen1, ruta_imagen2, ruta_imagen3],
        (err, results) => {
            if (err) {
                console.error("Error al insertar el paquete:", err);
                return res.status(500).json({ error: "Error interno del servidor" });
            }
            res.json({ message: "Paquete insertado correctamente", id_paquete: results.insertId });
        }
    );
});

/**
 * @swagger
 * /servicios-paquete/{id_paquete}:
 *   get:
 *     summary: Obtener servicios asociados a un paquete
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id_paquete
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del paquete para obtener sus servicios
 *     responses:
 *       200:
 *         description: Lista de servicios del paquete
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: false
 *                 servicios:
 *                   type: array
 *                   items:
 *                     type: object
 *                     properties:
 *                       id_servicio:
 *                         type: integer
 *                       nombre_servicio:
 *                         type: string
 *                       descripcion:
 *                         type: string
 *                       precio_servicio:
 *                         type: number
 *       401:
 *         description: No autorizado
 *       500:
 *         description: Error del servidor
 */
router.get('/servicios-paquete/:id_paquete', verificarToken, (req, res) => {
    try {
        const paqueteId = req.params.id_paquete;

        const query = `
            SELECT s.id_servicio, s.nombre_servicio, s.descripcion, s.precio_servicio 
            FROM servicios s
            INNER JOIN paquete_servicio ps ON s.id_servicio = ps.id_servicio
            WHERE ps.id_paquete = ?
        `;

        connection.query(query, [paqueteId], (error, results) => {
            if (error) {
                console.error('Error al obtener servicios del paquete:', error);
                return res.status(500).json({
                    error: true,
                    mensaje: 'Error al obtener los servicios del paquete'
                });
            }

            res.json({
                error: false,
                servicios: results
            });
        });
    } catch (error) {
        console.error('Error en el servidor:', error);
        res.status(500).json({
            error: true,
            mensaje: 'Error interno del servidor'
        });
    }
});

/**
 * @swagger
 * /usuarios-evento/{id_eventos}:
 *   get:
 *     summary: Obtener usuarios asociados a un evento
 *     tags: [Usuarios]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id_eventos
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento
 *     responses:
 *       200:
 *         description: Lista de usuarios del evento
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: false
 *                 usuarios:
 *                   type: array
 *                   items:
 *                     type: object
 *                     properties:
 *                       id_usuarios:
 *                         type: integer
 *                       nombre:
 *                         type: string
 *                       apellido:
 *                         type: string
 *                       correo:
 *                         type: string
 *       401:
 *         description: No autorizado
 *       500:
 *         description: Error del servidor
 */
router.get('/usuarios-evento/:id_eventos', verificarToken, (req, res) => {
    try {
        const eventoId = req.params.id_eventos;

        const query = `
            SELECT u.id_usuarios, u.nombre, u.apellido, u.correo 
            FROM usuarios u
            INNER JOIN paquetes p ON u.id_usuarios = p.id_usuarios
            WHERE p.id_eventos = ?
        `;

        connection.query(query, [eventoId], (error, results) => {
            if (error) {
                console.error('Error al obtener usuarios del evento:', error);
                return res.status(500).json({
                    error: true,
                    mensaje: 'Error al obtener los usuarios del evento'
                });
            }

            res.json({
                error: false,
                usuarios: results
            });
        });
    } catch (error) {
        console.error('Error en el servidor:', error);
        res.status(500).json({
            error: true,
            mensaje: 'Error interno del servidor'
        });
    }
});

/**
 * @swagger
 * /paquetes-por-evento-menu/{id_eventos}:
 *   get:
 *     summary: Obtener paquetes y sus servicios por evento
 *     tags: [Paquetes]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id_eventos
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento
 *     responses:
 *       200:
 *         description: Paquetes con sus servicios y totales
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: false
 *                 paquetes:
 *                   type: array
 *                   items:
 *                     type: object
 *                     properties:
 *                       id_paquete:
 *                         type: integer
 *                       nombre_paquete:
 *                         type: string
 *                       ruta_imagen:
 *                         type: string
 *                       descripcion:
 *                         type: string
 *                       ruta_imagen1:
 *                         type: string
 *                       ruta_imagen2:
 *                         type: string
 *                       ruta_imagen3:
 *                         type: string
 *                       servicios:
 *                         type: array
 *                         items:
 *                           type: object
 *                           properties:
 *                             id_servicio:
 *                               type: integer
 *                             nombre_servicio:
 *                               type: string
 *                             precio_servicio:
 *                               type: number
 *                       total_paquete:
 *                         type: number
 *                 total_evento:
 *                   type: number
 *       401:
 *         description: No autorizado
 *       500:
 *         description: Error del servidor
 */
router.get('/paquetes-por-evento-menu/:id_eventos', verificarToken, async (req, res) => {
    try {
        const eventoId = req.params.id_eventos;

        const query = `
            SELECT id_paquete, nombre_paquete, ruta_imagen, descripcion, 
                   ruta_imagen1, ruta_imagen2, ruta_imagen3 
            FROM paquetes 
            WHERE id_eventos = ?`;

        connection.query(query, [eventoId], async (error, paquetes) => {
            if (error) {
                console.error('Error al obtener paquetes:', error);
                return res.status(500).json({
                    error: true,
                    mensaje: 'Error al obtener los paquetes'
                });
            }

            try {
                // Para cada paquete, obtener sus servicios y calcular el total
                const paquetesConServicios = await Promise.all(paquetes.map(async (paquete) => {
                    // Obtener servicios usando el endpoint existente
                    const url = `${req.protocol}://${req.get('host')}/servicios-paquete/${paquete.id_paquete}`;
                    const serviciosResponse = await fetch(url, {
                        headers: { 'Authorization': req.headers.authorization }
                    });
                    const serviciosData = await serviciosResponse.json();
                    const servicios = serviciosData.servicios || [];

                    // Calcular total del paquete
                    const total_paquete = servicios.reduce((total, servicio) => 
                        total + servicio.precio_servicio, 0);

                    return {
                        id_paquete: paquete.id_paquete,
                        nombre_paquete: paquete.nombre_paquete,
                        ruta_imagen: paquete.ruta_imagen,
                        descripcion: paquete.descripcion,
                        ruta_imagen1: paquete.ruta_imagen1,
                        ruta_imagen2: paquete.ruta_imagen2,
                        ruta_imagen3: paquete.ruta_imagen3,
                        servicios: servicios,
                        total_paquete: total_paquete
                    };
                }));

                res.json({
                    error: false,
                    paquetes: paquetesConServicios,
                    total_evento: paquetesConServicios.reduce((total, paquete) => 
                        total + paquete.total_paquete, 0)
                });

            } catch (err) {
                console.error('Error al procesar paquetes:', err);
                res.status(500).json({
                    error: true,
                    mensaje: 'Error al procesar los paquetes'
                });
            }
        });

    } catch (error) {
        console.error('Error en el servidor:', error);
        res.status(500).json({
            error: true,
            mensaje: 'Error interno del servidor'
        });
    }
});
module.exports = router;
