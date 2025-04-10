const express = require("express");
const swaggerJsdoc = require("swagger-jsdoc");
const swaggerUi = require("swagger-ui-express");
//aqui pueden revisar la documentacion de la api
//http://localhost:3000/api-docs/
const app = express();
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
const PORT = process.env.PORT || 3002;
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
//
const swaggerSpecs = swaggerJsdoc(swaggerOptions);
// Swagger UI
app.use("/api-docs", swaggerUi.serve, swaggerUi.setup(swaggerSpecs));

// Agregar endpoint para acceder a Swagger Docs (especificación JSON)
app.get("/swagger.json", (req, res) => {
  res.setHeader("Content-Type", "application/json");
  res.send(swaggerSpecs);
});

// Cargar rutas (elimina las definiciones duplicadas de abajo)
app.use(require("./routes/usuarios"));
app.use(require("./routes/servicios"));
app.use(require("./routes/eventos"));
app.use(require("./routes/jsonwt"));
app.use(require("./routes/imagenes"));
app.use(require("./routes/totalServicios"));
app.use(require("./routes/paqueteXEvento"));
app.use(require("./routes/tarjetas"));
// Iniciar servidor
app.listen(PORT, () => {
  console.log(`Servidor en puerto ${PORT}`);
  console.log(`Documentación UI disponible en: http://localhost:${PORT}/api-docs`);
  console.log(`Especificación JSON disponible en: http://localhost:${PORT}/swagger.json`);
  console.log("ayuda con todo esto please")
});
module.exports = app;