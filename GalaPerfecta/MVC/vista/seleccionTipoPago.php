<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones de Pago</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 40px;
            font-size: 28px;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
        }

        h1:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2ecc71);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .button {
            border: none;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: 500;
            padding: 18px 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .button:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
        }

        .button:hover:before {
            left: 100%;
        }

        .button:active {
            transform: translateY(2px);
        }

        .pago-contado {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .pago-plazos {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }

        .regresar {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            margin-top: 10px;
        }

        .button::after {
            font-family: sans-serif;
            margin-left: 12px;
        }

        .pago-contado::after {
            content: "→";
        }

        .pago-plazos::after {
            content: "→";
        }

        .regresar::after {
            content: "←";
            margin-left: 0;
            margin-right: 12px;
        }

        .regresar {
            display: flex;
            flex-direction: row-reverse;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
                margin-bottom: 30px;
            }

            .button {
                font-size: 16px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Seleccione el Método de Pago</h1>
        <div class="buttons">
            <button class="button pago-contado" onclick="location.href='?c=contado';">Pago al Contado</button>
            <button class="button regresar" onclick="location.href='?c=pagos';">Regresar</button>
        </div>
    </div>
</body>
</html>
