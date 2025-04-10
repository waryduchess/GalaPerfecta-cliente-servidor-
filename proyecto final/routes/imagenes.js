const express = require('express');
const router = express.Router();
const { connection } = require('../config/config.db'); 

/**
 * @swagger
 * /carrusel:
 *   get:
 *     summary: Obtener paquetes sin usuario asignado para el carrusel
 *     tags: [Carrusel]
 *     responses:
 *       200:
 *         description: Lista de paquetes sin usuario asignado
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
 *                   ruta_imagen:
 *                     type: string
 *                     description: Ruta de la imagen principal del paquete
 *                   descripcion:
 *                     type: string
 *                     description: Descripción del paquete
 *       404:
 *         description: No se encontraron paquetes sin usuario asignado
 *       500:
 *         description: Error interno del servidor
 */

router.get('/carrusel', async (req, res) => {
    const query = "SELECT id_paquete,nombre_paquete, ruta_imagen, descripcion FROM paquetes WHERE id_usuarios IS NULL";

    try {
        connection.query(query, (err, results) => {
            if (err) {
                console.error("Error al obtener paquetes sin usuario:", err);
                return res.status(500).json({ error: "Error interno del servidor" });
            }

            if (results.length === 0) {
                return res.status(404).json({ mensaje: "No se encontraron paquetes sin usuario" });
            }

            res.json(results);
        });
    } catch (error) {
        console.error("Error al procesar la solicitud:", error);
        res.status(500).json({ error: "Error interno del servidor" });
    }
});

module.exports = router;