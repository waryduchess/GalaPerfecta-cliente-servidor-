const express = require('express');
const router = express.Router();
const { connection } = require('../config/config.db'); 
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