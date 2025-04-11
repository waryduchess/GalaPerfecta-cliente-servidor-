const express = require('express');
const router = express.Router();
const {connection} = require("../config/config.db");
const jwt = require('jsonwebtoken');
const SECRET_KEY = 'EsperemosQueEstaClaveSiFuncionePlease2013460';

/**
 * Middleware para verificar el token JWT
 * @middleware
 * @param {Object} req - Objeto de petición Express
 * @param {Object} res - Objeto de respuesta Express
 * @param {Function} next - Función para pasar al siguiente middleware
 * @returns {Object} Respuesta de error si el token no es válido
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
 * @api {post} /registrar-pago-contado Registrar un nuevo pago al contado
 * @apiName RegistrarPagoContado
 * @apiGroup Pagos
 * @apiDescription Endpoint para registrar un nuevo pago al contado en el sistema
 *
 * @apiHeader {String} Authorization Token JWT de autenticación (Bearer token)
 *
 * @apiParam {Number} idUsuarios ID del usuario que realiza el pago
 * @apiParam {Number} idPaquete ID del paquete que se está pagando
 * @apiParam {Number} montoTotal Monto total del pago
 * @apiParam {String} fechaPago Fecha en que se realiza el pago (YYYY-MM-DD)
 *
 * @apiSuccess {Boolean} error Indica si hubo un error (false si todo fue exitoso)
 * @apiSuccess {String} mensaje Mensaje de éxito
 * @apiSuccess {Number} idPago ID del pago registrado
 *
 * @apiError {Boolean} error Indica si hubo un error (true)
 * @apiError {String} mensaje Mensaje de error
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 400 Bad Request
 *     {
 *       "error": true,
 *       "mensaje": "Todos los campos son requeridos"
 *     }
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 201 Created
 *     {
 *       "error": false,
 *       "mensaje": "Pago al contado registrado exitosamente",
 *       "idPago": 1
 *     }
 */
router.post('/registrar-pago-contado', verificarToken, async (req, res) => {
    try {
        const { idUsuarios, idPaquete, montoTotal, fechaPago } = req.body;
        
        // Validar que todos los campos necesarios estén presentes
        if (!idUsuarios || !idPaquete || !montoTotal || !fechaPago) {
            return res.status(400).json({
                error: true,
                mensaje: 'Todos los campos son requeridos'
            });
        }

        const query = `
            INSERT INTO pagos (id_usuarios, id_paquete, monto_total, tipo_pago, fecha_pago)
            VALUES (?, ?, ?, 'contado', ?)
        `;

        connection.query(
            query,
            [idUsuarios, idPaquete, montoTotal, fechaPago],
            (error, results) => {
                if (error) {
                    console.error('Error al registrar el pago:', error);
                    return res.status(500).json({
                        error: true,
                        mensaje: 'Error al registrar el pago al contado'
                    });
                }

                res.status(201).json({
                    error: false,
                    mensaje: 'Pago al contado registrado exitosamente',
                    idPago: results.insertId
                });
            }
        );
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
 * /insertar-tarjeta:
 *   post:
 *     summary: Inserta una nueva tarjeta de crédito/débito
 *     tags: [Tarjetas]
 *     security:
 *       - bearerAuth: []
 *     description: Permite registrar una nueva tarjeta asociada a un usuario. Incluye validaciones de formato y fecha de vencimiento.
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             required:
 *               - idUsuario
 *               - nombreTitular
 *               - numeroTarjeta
 *               - fechaVencimiento
 *               - cvv
 *             properties:
 *               idUsuario:
 *                 type: integer
 *                 description: ID del usuario al que se asociará la tarjeta
 *                 example: 1
 *               nombreTitular:
 *                 type: string
 *                 maxLength: 100
 *                 description: Nombre completo del titular de la tarjeta
 *                 example: "Juan Pérez González"
 *               numeroTarjeta:
 *                 type: string
 *                 pattern: ^\d{16}$
 *                 description: Número de tarjeta (16 dígitos)
 *                 example: "4111111111111111"
 *               fechaVencimiento:
 *                 type: string
 *                 pattern: ^(0[1-9]|1[0-2])\/\d{4}$
 *                 description: Fecha de vencimiento en formato MM/AAAA
 *                 example: "12/2025"
 *               cvv:
 *                 type: string
 *                 pattern: ^\d{3}$
 *                 description: Código de seguridad (3 dígitos)
 *                 example: "123"
 *     responses:
 *       201:
 *         description: Tarjeta registrada exitosamente
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: false
 *                 mensaje:
 *                   type: string
 *                   example: "Tarjeta registrada exitosamente"
 *                 idTarjeta:
 *                   type: integer
 *                   example: 1
 *       400:
 *         description: Error de validación en los datos proporcionados
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: true
 *                 mensaje:
 *                   type: string
 *                   example: "El formato de fecha debe ser MM/AAAA"
 *       401:
 *         description: Token no proporcionado o inválido
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 mensaje:
 *                   type: string
 *                   example: "Token no proporcionado"
 *       500:
 *         description: Error interno del servidor
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: true
 *                 mensaje:
 *                   type: string
 *                   example: "Error interno del servidor"
 */
router.post('/insertar-tarjeta', verificarToken, async (req, res) => {
    try {
        const { idUsuario, nombreTitular, numeroTarjeta, fechaVencimiento, cvv } = req.body;

        // Validaciones mejoradas según la estructura de la tabla
        if (!idUsuario || !nombreTitular || !numeroTarjeta || !fechaVencimiento || !cvv) {
            return res.status(400).json({
                error: true,
                mensaje: 'Todos los campos son requeridos'
            });
        }

        // Validar longitud del nombre titular (máximo 100 caracteres)
        if (nombreTitular.length > 100) {
            return res.status(400).json({
                error: true,
                mensaje: 'El nombre del titular no puede exceder los 100 caracteres'
            });
        }

        // Validar formato de número de tarjeta (exactamente 16 dígitos)
        if (!/^\d{16}$/.test(numeroTarjeta)) {
            return res.status(400).json({
                error: true,
                mensaje: 'El número de tarjeta debe tener exactamente 16 dígitos'
            });
        }

        // Validar formato de fecha (MM/AAAA)
        if (!/^(0[1-9]|1[0-2])\/\d{4}$/.test(fechaVencimiento)) {
            return res.status(400).json({
                error: true,
                mensaje: 'El formato de fecha debe ser MM/AAAA'
            });
        }

        // Validación adicional para año válido (actual o futuro)
        const [mes, anio] = fechaVencimiento.split('/');
        const fechaActual = new Date();
        const anioActual = fechaActual.getFullYear();
        const mesActual = fechaActual.getMonth() + 1;

        if (parseInt(anio) < anioActual || 
            (parseInt(anio) === anioActual && parseInt(mes) < mesActual)) {
            return res.status(400).json({
                error: true,
                mensaje: 'La tarjeta está vencida'
            });
        }

        // Validar CVV (exactamente 3 dígitos)
        if (!/^\d{3}$/.test(cvv)) {
            return res.status(400).json({
                error: true,
                mensaje: 'El CVV debe tener exactamente 3 dígitos'
            });
        }

        const query = `
            INSERT INTO tarjetas (id_usuarios, nombre_titular, numero_tarjeta, fecha_vencimiento, cvv)
            VALUES (?, ?, ?, ?, ?)
        `;

        connection.query(
            query,
            [idUsuario, nombreTitular, numeroTarjeta, fechaVencimiento, cvv],
            (error, results) => {
                if (error) {
                    console.error('Error al insertar tarjeta:', error);
                    return res.status(500).json({
                        error: true,
                        mensaje: 'Error al registrar la tarjeta'
                    });
                }

                res.status(201).json({
                    error: false,
                    mensaje: 'Tarjeta registrada exitosamente',
                    idTarjeta: results.insertId
                });
            }
        );
    } catch (error) {
        console.error('Error en el servidor:', error);
        res.status(500).json({
            error: true,
            mensaje: 'Error interno del servidor'
        });
    }
});

module.exports = router;
