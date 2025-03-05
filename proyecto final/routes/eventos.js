const express = require('express');
const router = express.Router();
const {connection} = require("../config/config.db");

// GET: Obtener todos los eventos
router.get('/eventos', (req, res) => {
    connection.query('SELECT * FROM eventos', (err, results) => {
        if (err) {
            res.status(500).json({ error: err.message });
        } else {
            res.json(results);
        }
    });
});

// GET: Obtener un eventos por ID
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

// POST: Agregar un nuevo evento
router.post('/eventos', (req, res) => {
    const { nombre_evento } = req.body;
    
    // Verificar que todos los campos estén presentes y no sean nulos
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

// PUT: Editar un evento por ID
router.put('/eventos/:id', (req, res) => {
    const { id } = req.params;
    const { nombre_evento } = req.body;
    connection.query(
        'UPDATE eventos SET nombre_evento = ? WHERE id_eventos = ?',
        [nombre_evento,id],
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

// DELETE: Eliminar un alumno por ID
router.delete('/eventos/:id', (req, res) => {
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