const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken'); 
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
router.get('/paquetesEvento/:id',verificarToken, (req, res) => {
    const { id } = req.params;
    const sql = `
        SELECT id_paquete, nombre_paquete 
        FROM paquetes 
        WHERE id_eventos = ?`;

    connection.query(sql, [id], (err, results) => {
        if (err) {
            console.error("Error al obtener paquetes:", err);
            return res.status(500).json({ error: "Error interno del servidor" });
        }

        res.json(results);
    });
});
module.exports = router;