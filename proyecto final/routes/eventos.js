const express = require('express');
const router = express.Router();
const {connection} = require("../config/config.db");

/**
 * @swagger
 * tags:
 *   name: Eventos
 *   description: Gestión de eventos
 */

/**
 * @swagger
 * /eventos:
 *   get:
 *     summary: Obtener todos los eventos
 *     tags: [Eventos]
 *     responses:
 *       200:
 *         description: Lista de eventos
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 $ref: '#/components/schemas/Evento'
 */
router.get('/eventos', (req, res) => {
    connection.query('SELECT * FROM eventos', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

/**
 * @swagger
 * /eventos/{id}:
 *   get:
 *     summary: Obtener un evento por ID
 *     tags: [Eventos]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento
 *     responses:
 *       200:
 *         description: Evento encontrado
 *         content:
 *           application/json:
 *             schema:
 *               $ref: '#/components/schemas/Evento'
 *       404:
 *         description: Evento no encontrado
 */
router.get('/eventos/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM eventos WHERE id_eventos = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.length === 0) {
            res.status(404).json({ message: 'Evento no encontrado' });
        } else {
            res.json(results[0]);
        }
    });
});

// Definición del esquema Evento
/**
 * @swagger
 * components:
 *   schemas:
 *     Evento:
 *       type: object
 *       properties:
 *         id_eventos:
 *           type: integer
 *           description: ID del evento
 *         nombre_evento:
 *           type: string
 *           description: Nombre del evento
 *       example:
 *         id_eventos: 1
 *         nombre_evento: "Boda"
 */

module.exports = router;