const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken'); // Agregar esta importación
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
 *   name: Usuarios
 *   description: Gestión de usuarios
 */

/**
 * @swagger
 * /usuarios:
 *   get:
 *     summary: Obtener todos los usuarios
 *     tags: [Usuarios]
 *     security:
 *       - bearerAuth: []
 *     responses:
 *       200:
 *         description: Lista de usuarios
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 $ref: '#/components/schemas/Usuario'
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 */
router.get('/usuarios', verificarToken, (req, res) => {
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
 *     security:
 *       - bearerAuth: []
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
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Usuario no encontrado
 */
router.get('/usuarios/:id', verificarToken, (req, res) => {
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
 *     security:
 *       - bearerAuth: []
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
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       500:
 *         description: Error del servidor
 */
    router.post('/usuarios', (req, res) => {
        const { nombre, apellido, correo, numero_telefono, password, id_tipo_user } = req.body;
        
        if (!nombre || !apellido || !correo || !numero_telefono || !password || !id_tipo_user) {
            return res.status(400).json({ error: "Todos los campos son obligatorios" });
        }

        connection. query(
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
 *     security:
 *       - bearerAuth: []
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
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Usuario no encontrado
 *       500:
 *         description: Error del servidor
 */
router.put('/usuarios/:id', verificarToken, (req, res) => {
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
 *     security:
 *       - bearerAuth: []
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
 *       401:
 *         description: No autorizado, token no proporcionado o inválido
 *       404:
 *         description: Usuario no encontrado
 *       500:
 *         description: Error del servidor
 */
router.delete('/usuarios/:id', verificarToken, (req, res) => {
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
 *   securitySchemes:
 *     bearerAuth:
 *       type: http
 *       scheme: bearer
 *       bearerFormat: JWT
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
 */router.get('/correo/:correo', (req, res) => {
    const { correo } = req.params;
  
    if (!correo) {
      return res.status(400).json({ error: "El parámetro 'correo' es requerido en la URL." });
    }
  
    connection.query(
      `SELECT 
        u.id_usuarios, 
        u.nombre, 
        u.apellido, 
        u.correo, 
        u.numero_telefono, 
        u.password, 
        t.id_tipo_user
      FROM usuarios u
      LEFT JOIN tipo_user t ON t.id_tipo_user = u.id_tipo_user
      WHERE u.correo = ?`,
      [correo],
      (err, rows) => {
        if (err) {
          console.error("Error en la consulta SQL:", err);
          return res.status(500).json({ error: "Error interno del servidor." });
        }
  
        if (rows.length === 0) {
          return res.status(404).json({ error: "Usuario no encontrado." });
        }

        // Generar el token JWT
        const usuario = rows[0];
        const token = jwt.sign(
          { id: usuario.id_usuarios, correo: usuario.correo, tipo_user: usuario.id_tipo_user },
          SECRET_KEY,
          { expiresIn: '1h' } // El token expira en 1 hora
        );

        // Retornar el usuario junto con el token
        res.json({ usuario, token });
      }
    );
});
/**
 * @swagger
 * /pagos/usuario/{id_usuario}:
 *   get:
 *     summary: Obtiene todos los pagos de un usuario específico
 *     tags: [Pagos]
 *     security:
 *       - bearerAuth: []
 *     parameters:
 *       - in: path
 *         name: id_usuario
 *         required: true
 *         schema:
 *           type: integer
 *         description: ID del usuario para consultar sus pagos
 *     responses:
 *       200:
 *         description: Lista de pagos del usuario obtenida exitosamente
 *         content:
 *           application/json:
 *             schema:
 *               type: object
 *               properties:
 *                 error:
 *                   type: boolean
 *                   example: false
 *                 pagos:
 *                   type: array
 *                   items:
 *                     type: object
 *                     properties:
 *                       id_pago:
 *                         type: integer
 *                       id_paquete:
 *                         type: integer
 *                       monto_total:
 *                         type: number
 *                       tipo_pago:
 *                         type: string
 *                         enum: [contado, plazos]
 *                       fecha_pago:
 *                         type: string
 *                         format: date
 *                       nombre_paquete:
 *                         type: string
 *                       nombre_evento:
 *                         type: string
 *       404:
 *         description: No se encontraron pagos para este usuario
 *       401:
 *         description: No autorizado
 *       500:
 *         description: Error del servidor
 */
router.get('/pagos/usuario/:id_usuario', verificarToken, (req, res) => {
    const { id_usuario } = req.params;
    
    const query = `
        SELECT 
            p.id_pago,
            p.id_paquete,
            p.monto_total,
            p.tipo_pago,
            DATE_FORMAT(p.fecha_pago, '%Y-%m-%d') as fecha_pago,
            pq.nombre_paquete,
            e.nombre_evento
        FROM pagos p
        LEFT JOIN paquetes pq ON p.id_paquete = pq.id_paquete
        LEFT JOIN eventos e ON pq.id_eventos = e.id_eventos
        WHERE p.id_usuarios = ?
        ORDER BY p.fecha_pago DESC
    `;

    connection.query(query, [id_usuario], (error, results) => {
        if (error) {
            console.error('Error al consultar pagos del usuario:', error);
            return res.status(500).json({
                error: true,
                mensaje: "Error al obtener los pagos del usuario"
            });
        }

        if (results.length === 0) {
            return res.status(404).json({
                error: true,
                mensaje: "No se encontraron pagos para este usuario"
            });
        }

        res.json({
            error: false,
            pagos: results
        });
    });
});
module.exports = router;