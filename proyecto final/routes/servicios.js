const express = require('express');
const router = express.Router();
const {connection} = require("../config/config.db");

// GET: Obtener todos los usuarios
router.get('/servicios', (req, res) => {
    connection.query('SELECT * FROM servicios', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

// GET: Obtener un servicio por ID
router.get('/servicios/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM servicios WHERE id_servicio = ?', [id], (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else if (results.length === 0) {
            res.status(404).json({ message: 'servicio no encontrado' });
        } else {
            res.json(results[0]);
        }
    });
});

// POST: Agregar un nuevo servicio
router.post('/servicios', (req, res) => {
    const { descripcion, nombre_servicio, precio_servicio } = req.body;
    
    // Verificar que todos los campos estén presentes y no sean nulos
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

// PUT: Editar un servicio por ID
router.put('/servicios/:id', (req, res) => {
    const { id } = req.params;
    const { descripcion, nombre_servicio, precio_servicio } = req.body;
    connection.query(
        'UPDATE servicios SET descripcion = ?, nombre_servicio = ?, precio_servicio = ? WHERE id_servicio = ?',
        [descripcion, nombre_servicio, precio_servicio,id],
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

// DELETE: Eliminar un alumno por ID
router.delete('/servicios/:id', (req, res) => {
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




module.exports = router;