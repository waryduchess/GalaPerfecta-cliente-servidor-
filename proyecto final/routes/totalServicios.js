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

module.exports = router;