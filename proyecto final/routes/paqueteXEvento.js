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

module.exports = router;
