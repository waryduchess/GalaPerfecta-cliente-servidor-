const express = require('express');
const router = express.Router();
const {connection} = require("../config/config.db");

/**
 * @swagger
 * tags:
 *   name: Usuarios
 *   description: Gestión de usuarios
 */

/**
 * @swagger
 * /usuarios:
 *   get:
 *     summary: Obtener todos los usuarios
 *     tags: [Usuarios]
 *     responses:
 *       200:
 *         description: Lista de usuarios
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 $ref: '#/components/schemas/Usuario'
 */
router.get('/usuarios', (req, res) => {
    connection.query('SELECT * FROM usuarios', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

/**
 * @swagger
 * /usuarios/{id}:
 *   get:
 *     summary: Obtener un usuario por ID
 *     tags: [Usuarios]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del usuario
 *     responses:
 *       200:
 *         description: Usuario encontrado
 *         content:
 *           application/json:
 *             schema:
 *               $ref: '#/components/schemas/Usuario'
 *       404:
 *         description: Usuario no encontrado
 */
router.get('/usuarios/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM usuarios WHERE id_usuarios = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.length === 0) {
            res.status(404).json({ message: 'Usuario no encontrado' });
        } else {
            res.json(results[0]);
        }
    });
});

/**
 * @swagger
 * /usuarios:
 *   post:
 *     summary: Agregar un nuevo usuario
 *     tags: [Usuarios]
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Usuario'
 *     responses:
 *       200:
 *         description: Usuario agregado correctamente
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
 *       500:
 *         description: Error del servidor
 */
router.post('/usuarios', (req, res) => {
    const { nombre, apellido, correo, numero_telefono, password, id_tipo_user } = req.body;
    
    if (!nombre || !apellido || !correo || !numero_telefono || !password || !id_tipo_user) {
        return res.status(400).json({ error: "Todos los campos son obligatorios" });
    }

    connection.query(
        'INSERT INTO usuarios (nombre, apellido, correo, numero_telefono, password, id_tipo_user) VALUES (?, ?, ?, ?, ?, ?)',
        [nombre, apellido, correo, numero_telefono, password, id_tipo_user],
        (err, results) => {
            if (err) {
                return res.status(500).json({ error: err.message });
            }
            res.json({ message: 'Usuario agregado correctamente', id: results.insertId });
        }
    );
});

/**
 * @swagger
 * /usuarios/{id}:
 *   put:
 *     summary: Editar un usuario por ID
 *     tags: [Usuarios]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del usuario
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Usuario'
 *     responses:
 *       200:
 *         description: Usuario actualizado correctamente
 *       404:
 *         description: Usuario no encontrado
 *       500:
 *         description: Error del servidor
 */
router.put('/usuarios/:id', (req, res) => {
    const { id } = req.params;
    const { nombre, apellido, correo, numero_telefono, password, id_tipo_user } = req.body;
    connection.query(
        'UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, numero_telefono = ?, password = ?, id_tipo_user = ? WHERE id_usuarios = ?',
        [nombre, apellido, correo, numero_telefono, password, id_tipo_user, id],
        (err, results) => {
            if (err) {
                res.status(500).json({ error: err.message });
            } else if (results.affectedRows === 0) {
                res.status(404).json({ message: 'Usuario no encontrado' });
            } else {
                res.json({ message: 'Usuario actualizado correctamente' });
            }
        }
    );
});

/**
 * @swagger
 * /usuarios/{id}:
 *   delete:
 *     summary: Eliminar un usuario por ID
 *     tags: [Usuarios]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del usuario
 *     responses:
 *       200:
 *         description: Usuario eliminado correctamente
 *       404:
 *         description: Usuario no encontrado
 *       500:
 *         description: Error del servidor
 */
router.delete('/usuarios/:id', (req, res) => {
    const { id } = req.params;
    connection.query('DELETE FROM usuarios WHERE id_usuarios = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.affectedRows === 0) {
            res.status(404).json({ message: 'Usuario no encontrado' });
        } else {
            res.json({ message: 'Usuario eliminado correctamente' });
        }
    });
});

/**
 * @swagger
 * components:
 *   schemas:
 *     Usuario:
 *       type: object
 *       properties:
 *         id_usuarios:
 *           type: integer
 *           description: ID del usuario
 *         nombre:
 *           type: string
 *           description: Nombre del usuario
 *         apellido:
 *           type: string
 *           description: Apellido del usuario
 *         correo:
 *           type: string
 *           description: Correo del usuario
 *         numero_telefono:
 *           type: string
 *           description: Número de teléfono del usuario
 *         password:
 *           type: string
 *           description: Contraseña del usuario
 *         id_tipo_user:
 *           type: integer
 *           description: ID del tipo de usuario
 *       example:
 *         id_usuarios: 1
 *         nombre: "Juan"
 *         apellido: "Pérez"
 *         correo: "juan.perez@example.com"
 *         numero_telefono: "1234567890"
 *         password: "password123"
 *         id_tipo_user: 1
 */

module.exports = router;