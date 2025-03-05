const express = require("express");
const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// cargamos el archivo de rutas
app.use(require('./routes/usuarios'));
app.get('/usuarios', (req, res) => {
  res.json(results);
});

app.use(require('./routes/servicios'));
app.get('/servicios', (req, res) => {
  res.json(results);
});

app.use(require('./routes/eventos'));
app.get('/eventos', (req, res) => {
  res.json(results);
});



const PORT = process.env.PORT;
app.listen(PORT, () => {
  console.log('EI servidor escucha en el puerto ' + PORT);
});

module.exports = app;