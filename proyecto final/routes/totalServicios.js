const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken'); 
const {connection} = require("../config/config.db");
const SECRET_KEY = 'EsperemosQueEstaClaveSiFuncionePlease2013460';

/**
 * Middleware para verificar el token JWT
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
 * /total/{id}:
 *   get:
 *     summary: Calcular el total de servicios de un evento
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento para calcular el total de servicios
 *     responses:
 *       200:
 *         description: Total de servicios calculado correctamente
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 total_servicios:
 *                   type: number
 *                   description: Total de los precios de los servicios asociados al evento
 *       400:
 *         description: ID del evento no proporcionado o inválido
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       500:
 *         description: Error interno del servidor
 */
router.get('/total/:id',verificarToken, (req, res) => {
    const { id } = req.params;
    const sql = `
        SELECT SUM(s.precio_servicio) AS total_servicios
        FROM servicios s
        INNER JOIN paquete_servicio ps ON s.id_servicio = ps.id_servicio
        INNER JOIN paquetes p ON ps.id_paquete = p.id_paquete
        WHERE p.id_eventos = ?`;

    connection.query(sql, [id], (err, results) => {
        if (err) {
            console.error("Error al calcular el total de servicios:", err);
            return res.status(500).json({ error: "Error interno del servidor" });
        }

        res.json({ total_servicios: results[0].total_servicios || 0 });
    });
});
/**
 * @swagger
 * /eventos/{id}/paquetes:
 *   get:
 *     summary: Obtener paquetes de un evento específico
 *     tags: [Eventos]
 *     parameters:
 *       - name: id
 *         in: path
 *         required: true
 *         description: ID del evento
 *         schema:
 *           type: integer
 *     responses:
 *       200:
 *         description: Lista de paquetes del evento
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 type: object
 *                 properties:
 *                   id_paquete:
 *                     type: integer
 *                   nombre_paquete:
 *                     type: string
 */
// Obtener paquetes de un evento
/**
 * @swagger
 * /paquetes/{id_paquete}/servicios:
 *   post:
 *     summary: Registrar servicios en un paquete
 *     tags: [Paquetes]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id_paquete
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del paquete al que se asociarán los servicios
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             properties:
 *               servicios:
 *                 type: array
 *                 items:
 *                   type: integer
 *                 description: Lista de IDs de servicios a asociar con el paquete
 *             required:
 *               - servicios
 *     responses:
 *       200:
 *         description: Servicios registrados correctamente en el paquete
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 message:
 *                   type: string
 *       400:
 *         description: Campos obligatorios faltantes
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       500:
 *         description: Error del servidor
 */
router.post('/serviciosXpaquete/:id_paquete', verificarToken, (req, res) => {
    const { id_paquete } = req.params;
    const { servicios } = req.body;

    if (!id_paquete || !Array.isArray(servicios) || servicios.length === 0) {
        return res.status(400).json({ error: "El ID del paquete y la lista de servicios son obligatorios" });
    }

    // Preparar valores para inserción múltiple
    const values = servicios.map(id_servicio => [id_paquete, id_servicio]);

    const query = `
        INSERT INTO paquete_servicio (id_paquete, id_servicio) 
        VALUES ?
        ON DUPLICATE KEY UPDATE id_servicio = VALUES(id_servicio)
    `;
    
    connection.query(query, [values], (err, results) => {
        if (err) {
            console.error("Error al registrar servicios en el paquete:", err);
            return res.status(500).json({ error: "Error interno del servidor" });
        }
        res.json({ message: "Servicios registrados correctamente en el paquete." });
    });
});

module.exports = router;