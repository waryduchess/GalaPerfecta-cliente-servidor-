<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Tarjeta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7ed 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            padding: 20px;
        }
        
        .confirmation-card {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
            max-width: 500px;
            width: 90%;
            transform: translateY(0);
            animation: float 6s ease-in-out infinite;
            transition: all 0.3s ease;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            }
            50% {
                transform: translateY(-10px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            }
            100% {
                transform: translateY(0px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            }
        }
        
        .success-message {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 4px 6px rgba(40, 167, 69, 0.2));
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        h2 {
            color: #28a745;
            margin-bottom: 1.2rem;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        p {
            color: #555;
            margin-bottom: 1rem;
            line-height: 1.6;
            font-size: 1.1rem;
        }
        
        .redirect-message {
            margin-top: 2rem;
            font-style: italic;
            color: #888;
            font-size: 0.9rem;
            position: relative;
            display: inline-block;
        }
        
        .redirect-message::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(40, 167, 69, 0.5), transparent);
            animation: loading 2s infinite;
        }
        
        @keyframes loading {
            0% {
                width: 0%;
                left: 0;
            }
            50% {
                width: 100%;
            }
            100% {
                width: 0%;
                left: 100%;
            }
        }
        
        /* Efectos decorativos */
        .decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(40, 167, 69, 0.1);
            z-index: -1;
        }
        
        .decoration-1 {
            width: 60px;
            height: 60px;
            top: -20px;
            right: -20px;
        }
        
        .decoration-2 {
            width: 40px;
            height: 40px;
            bottom: -15px;
            left: -15px;
        }
    </style>
    <script>
        // Redirección automática después de 3 segundos
        setTimeout(function() {
            window.location.href = 'http://localhost:3001/MVC/index.php?c=mandarTipoPago';
        }, 3000); // 3000 milisegundos = 3 segundos
    </script>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="decoration decoration-1"></div>
            <div class="decoration decoration-2"></div>
            <div class="success-message">
                <img src="../../img/check.png" alt="Éxito" class="success-icon">
                <h2>¡Tarjeta Registrada con Éxito!</h2>
                <p>Tu tarjeta ha sido registrada correctamente en nuestro sistema.</p>
                <p class="redirect-message">Serás redirigido en unos segundos...</p>
            </div>
        </div>
    </div>
</body>
</html>
