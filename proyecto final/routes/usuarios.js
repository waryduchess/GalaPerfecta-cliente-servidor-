const express = require('express');
const router = express.Router();
const {connection} = require("../config/config.db");

// GET: Obtener todos los usuarios
router.get('/usuarios', (req, res) => {
    connection.query('SELECT * FROM usuarios', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

// GET: Obtener un usuario por ID
router.get('/usuarios/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM usuarios WHERE id_usuarios = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.length === 0) {
            res.status(404).json({ message: 'usuario no encontrado' });
        } else {
            res.json(results[0]);
        }
    });
});

// POST: Agregar un nuevo usuarios
router.post('/usuarios', (req, res) => {
    const { nombre, apellido, correo, numero_telefono, password, id_tipo_user } = req.body;
    
    // Verificar que todos los campos estén presentes y no sean nulos
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
            res.json({ message: 'Usurio agregado correctamente', id: results.insertId });
        }
    );
});

// PUT: Editar un usuario por ID
router.put('/usuarios/:id', (req, res) => {
    const { id } = req.params;
    const { nombre, apellido, correo, numero_telefono, password, id_tipo_user } = req.body;
    connection.query(
        'UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, numero_telefono = ?, password = ?, id_tipo_user = ? WHERE id_usuarios = ?',
        [nombre, apellido, correo, numero_telefono, password, id_tipo_user,id],
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

// DELETE: Eliminar un alumno por ID
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



module.exports = router;