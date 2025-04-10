//si ves esto rivas esto no se documenta
//  *               mensaje:
const express = require('express');
const jwt = require('jsonwebtoken');
const router = express.Router();
const { connection } = require("../config/config.db");

const SECRET_KEY = 'EsperemosQueEstaClaveSiFuncionePlease2013460';

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

router.get('/token/:id', (req, res) => {
    const userData = req.params.id;
    
    connection.query(
        'SELECT * FROM usuarios WHERE id_usuarios = ?', 
        [userData],
        (err, results) => {
            if (err) {
                return res.status(500).json({ 
                    error: err.message 
                });
            }

            if (results.length === 0) {
                return res.status(404).json({
                    mensaje: 'Usuario no encontrado'
                });
            }

            const user = results[0];
            
            try {
                const token = jwt.sign(
                    {
                        id: user.id_usuarios,
                        nombre: user.nombre,
                        apellido: user.apellido,
                        email: user.correo,
                        telefono: user.numero_telefono,
                        contra: user.password,
                        tipo_usuario: user.id_tipo_user
                    },
                    SECRET_KEY,
                    { expiresIn: '1h' }
                );

                res.json({
                    mensaje: 'Token generado exitosamente',
                    user: {
                       
                        id: user.id_usuarios,
                        nombre: user.nombre,
                        apellido: user.apellido
                    },
                    token
                });
            } catch (error) {
                res.status(500).json({ 
                    error: 'Error al generar el token',
                    details: error.message 
                });
            }
        }
    );
});
/*
router.get('/tokens/all', (req, res) => {
    connection.query(
        'SELECT * FROM tbl_user WHERE UNAL_NOMBRE IS NOT NULL',
        (err, results) => {
            if (err) {
                return res.status(500).json({ 
                    error: err.message 
                });
            }

            try {
                const usersConTokens = results.map(user => {
                    const token = jwt.sign(
                        {
                            id: user.UNAL_ID,
                            nombre: user.UNAL_NOMBRE,
                            apellido: user.UNAL_APELLIDO,
                            email: user.UNAL_EMAIL
                        },
                        SECRET_KEY,
                        { expiresIn: '1h' }
                    );

                    return {
                        id: user.UNAL_ID,
                        nombre: user.UNAL_NOMBRE,
                        apellido: user.UNAL_APELLIDO,
                        token
                    };
                });

                res.json({
                    mensaje: 'Tokens generados exitosamente',
                    users: usersConTokens
                });
            } catch (error) {
                res.status(500).json({ 
                    error: 'Error al generar los tokens',
                    details: error.message 
                });
            }
        }
    );
});
*/
router.get('/verificar', verificarToken, (req, res) => {
    res.json({
        mensaje: 'Token válido',
        usuario: req.usuario
    });
});

module.exports = router;