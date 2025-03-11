const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken'); // Importación necesaria
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
 * tags:
 *   name: Servicios
 *   description: Gestión de servicios
 */

/**
 * @swagger
 * /servicios:
 *   get:
 *     summary: Obtener todos los servicios
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     responses:
 *       200:
 *         description: Lista de servicios
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 $ref: '#/components/schemas/Servicio'
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 */
router.get('/servicios', verificarToken, (req, res) => {
    connection.query('SELECT * FROM servicios', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

/**
 * @swagger
 * /servicios/{id}:
 *   get:
 *     summary: Obtener un servicio por ID
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del servicio
 *     responses:
 *       200:
 *         description: Servicio encontrado
 *         content:
 *           application/json:
 *             schema:
 *               $ref: '#/components/schemas/Servicio'
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Servicio no encontrado
 */
router.get('/servicios/:id', verificarToken, (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM servicios WHERE id_servicio = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.length === 0) {
            res.status(404).json({ message: 'Servicio no encontrado' });
        } else {
            res.json(results[0]);
        }
    });
});

/**
 * @swagger
 * /servicios:
 *   post:
 *     summary: Agregar un nuevo servicio
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Servicio'
 *     responses:
 *       200:
 *         description: Servicio agregado correctamente
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
router.post('/servicios', verificarToken, (req, res) => {
    const { descripcion, nombre_servicio, precio_servicio } = req.body;
    
    if (!descripcion || !nombre_servicio || !precio_servicio) {
        return res.status(400).json({ error: "Todos los campos son obligatorios" });
    }

    connection.query(
        'INSERT INTO servicios (descripcion, nombre_servicio, precio_servicio) VALUES (?, ?, ?)',
        [descripcion, nombre_servicio, precio_servicio],
        (err, results) => {
            if (err) {
                return res.status(500).json({ error: err.message });
            }
            res.json({ message: 'Servicio agregado correctamente', id: results.insertId });
        }
    );
});

/**
 * @swagger
 * /servicios/{id}:
 *   put:
 *     summary: Editar un servicio por ID
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del servicio
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Servicio'
 *     responses:
 *       200:
 *         description: Servicio actualizado correctamente
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Servicio no encontrado
 *       500:
 *         description: Error del servidor
 */
router.put('/servicios/:id', verificarToken, (req, res) => {
    const { id } = req.params;
    const { descripcion, nombre_servicio, precio_servicio } = req.body;
    connection.query(
        'UPDATE servicios SET descripcion = ?, nombre_servicio = ?, precio_servicio = ? WHERE id_servicio = ?',
        [descripcion, nombre_servicio, precio_servicio, id],
        (err, results) => {
            if (err) {
                res.status(500).json({ error: err.message });
            } else if (results.affectedRows === 0) {
                res.status(404).json({ message: 'Servicio no encontrado' });
            } else {
                res.json({ message: 'Servicio actualizado correctamente' });
            }
        }
    );
});

/**
 * @swagger
 * /servicios/{id}:
 *   delete:
 *     summary: Eliminar un servicio por ID
 *     tags: [Servicios]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del servicio
 *     responses:
 *       200:
 *         description: Servicio eliminado correctamente
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Servicio no encontrado
 *       500:
 *         description: Error del servidor
 */
router.delete('/servicios/:id', verificarToken, (req, res) => {
    const { id } = req.params;
    connection.query('DELETE FROM servicios WHERE id_servicio = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.affectedRows === 0) {
            res.status(404).json({ message: 'Servicio no encontrado' });
        } else {
            res.json({ message: 'Servicio eliminado correctamente' });
        }
    });
});

/**
 * @swagger
 * components:
 *   securitySchemes:
 *     bearerAuth:
 *       type: http
 *       scheme: bearer
 *       bearerFormat: JWT
 *   schemas:
 *     Servicio:
 *       type: object
 *       properties:
 *         id_servicio:
 *           type: integer
 *           description: ID del servicio
 *         descripcion:
 *           type: string
 *           description: Descripción del servicio
 *         nombre_servicio:
 *           type: string
 *           description: Nombre del servicio
 *         precio_servicio:
 *           type: number
 *           format: float
 *           description: Precio del servicio
 *       example:
 *         id_servicio: 1
 *         descripcion: "Servicio de reparación"
 *         nombre_servicio: "Reparación de equipos"
 *         precio_servicio: 50.0
 */

module.exports = router;