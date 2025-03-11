const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken'); // Importación necesaria
const { connection } = require("../config/config.db");
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
 * tags:
 *   name: Eventos
 *   description: Gestión de eventos
 */

/**
 * @swagger
 * components:
 *   securitySchemes:
 *     bearerAuth:
 *       type: http
 *       scheme: bearer
 *       bearerFormat: JWT
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

/**
 * @swagger
 * /eventos:
 *   get:
 *     summary: Obtener todos los eventos
 *     tags: [Eventos]
 *     security:
 *       - bearerAuth: []
 *     responses:
 *       200:
 *         description: Lista de eventos
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 $ref: '#/components/schemas/Evento'
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 */
router.get('/eventos', verificarToken, (req, res) => {
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
 *     security:
 *       - bearerAuth: []
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
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Evento no encontrado
 */
router.get('/eventos/:id', verificarToken, (req, res) => {
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

/**
 * @swagger
 * /eventos:
 *   post:
 *     summary: Agregar un nuevo evento
 *     tags: [Eventos]
 *     security:
 *       - bearerAuth: []
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Evento'
 *     responses:
 *       200:
 *         description: Evento agregado correctamente
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 message:
 *                   type: string
 *                 id:
 *                   type: integer
 *       400:
 *         description: Campos obligatorios faltantes
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       500:
 *         description: Error del servidor
 */
router.post('/eventos', verificarToken, (req, res) => {
    const { nombre_evento } = req.body;
    if (!nombre_evento) {
        return res.status(400).json({ error: "Todos los campos son obligatorios" });
    }
    connection.query(
        'INSERT INTO eventos (nombre_evento) VALUES (?)',
        [nombre_evento],
        (err, results) => {
            if (err) {
                return res.status(500).json({ error: err.message });
            }
            res.json({ message: 'Evento agregado correctamente', id: results.insertId });
        }
    );
});

/**
 * @swagger
 * /eventos/{id}:
 *   put:
 *     summary: Editar un evento por ID
 *     tags: [Eventos]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Evento'
 *     responses:
 *       200:
 *         description: Evento actualizado correctamente
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Evento no encontrado
 *       500:
 *         description: Error del servidor
 */
router.put('/eventos/:id', verificarToken, (req, res) => {
    const { id } = req.params;
    const { nombre_evento } = req.body;
    connection.query(
        'UPDATE eventos SET nombre_evento = ? WHERE id_eventos = ?',
        [nombre_evento, id],
        (err, results) => {
            if (err) {
                res.status(500).json({ error: err.message });
            } else if (results.affectedRows === 0) {
                res.status(404).json({ message: 'Evento no encontrado' });
            } else {
                res.json({ message: 'Evento actualizado correctamente' });
            }
        }
    );
});

/**
 * @swagger
 * /eventos/{id}:
 *   delete:
 *     summary: Eliminar un evento por ID
 *     tags: [Eventos]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del evento
 *     responses:
 *       200:
 *         description: Evento eliminado correctamente
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Evento no encontrado
 *       500:
 *         description: Error del servidor
 */
router.delete('/eventos/:id', verificarToken, (req, res) => {
    const { id } = req.params;
    connection.query('DELETE FROM eventos WHERE id_eventos = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.affectedRows === 0) {
            res.status(404).json({ message: 'Evento no encontrado' });
        } else {
            res.json({ message: 'Evento eliminado correctamente' });
        }
    });
});

module.exports = router;