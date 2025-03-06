const express = require("express");
const swaggerJsdoc = require("swagger-jsdoc");
const swaggerUi = require("swagger-ui-express");
//aqui pueden revisar la documentacion de la api
//http://localhost:3000/api-docs/
const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const PORT = process.env.PORT || 3000;

const swaggerOptions = {
  definition: {
    openapi: "3.0.0",
    info: {
      title: "API de Mi Aplicación",
      version: "1.0.0",
      description: "Documentación para gestión de eventos, usuarios y servicios",
    },
    servers: [
      {
        url: `http://localhost:${PORT}`,
        description: "Servidor local",
      },
    ],
  },
  apis: ["./routes/*.js"], // Asegúrate que esta ruta sea correcta
};

const swaggerSpecs = swaggerJsdoc(swaggerOptions);

// Swagger UI
app.use("/api-docs", swaggerUi.serve, swaggerUi.setup(swaggerSpecs));

// Cargar rutas (elimina las definiciones duplicadas de abajo)
app.use(require("./routes/usuarios"));
app.use(require("./routes/servicios"));
app.use(require("./routes/eventos"));

// Iniciar servidor
app.listen(PORT, () => {
  console.log(`Servidor en puerto ${PORT}`);
});

module.exports = app;