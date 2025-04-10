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

module.exports = router;
