<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En Memoria de Pedro</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1c1c1c 0%, #2d2d2d 100%);
            font-family: 'Georgia', serif;
            color: #fff;
            text-align: center;
        }

        .memorial-container {
            max-width: 800px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .memorial-title {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .memorial-text {
            font-size: 1.2em;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #f0f0f0;
        }

        .memorial-quote {
            font-style: italic;
            font-size: 1.1em;
            color: #d4d4d4;
            margin: 30px 0;
            padding: 20px;
            border-left: 4px solid #fff;
        }

        .candle {
            font-size: 3em;
            margin: 20px 0;
            animation: flicker 1.5s infinite alternate;
        }

        @keyframes flicker {
            0%, 18%, 22%, 25%, 53%, 57%, 100% {
                text-shadow: 0 0 4px #fff,
                    0 0 11px #fff,
                    0 0 19px #fff,
                    0 0 40px #ffd700,
                    0 0 80px #ffd700,
                    0 0 90px #ffd700,
                    0 0 100px #ffd700,
                    0 0 150px #ffd700;
            }
            20%, 24%, 55% {
                text-shadow: none;
            }
        }

        .dates {
            font-size: 1.1em;
            color: #d4d4d4;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="memorial-container">
        <div class="candle">🕯️</div>
        <h1 class="memorial-title">En Memoria de Pedro</h1>
        
        <p class="memorial-text">
            Querido amigo y compañero:<br><br>
            Tu ausencia deja un vacío imposible de llenar, pero tu recuerdo vivirá siempre en nuestros corazones. 
            Fuiste más que un compañero de trabajo, fuiste un verdadero amigo que iluminó nuestros días con tu 
            sonrisa y tu inagotable espíritu de ayuda.
        </p>

        <div class="memorial-quote">
            "No es un adiós, sino un hasta luego. Tu legado perdurará en cada línea de código que escribamos, 
            en cada problema que resolvamos, y en cada momento que compartamos."
        </div>

        <p class="memorial-text">
            Gracias por cada momento compartido, por tu amistad sincera, por tus enseñanzas y por tu 
            incomparable compañerismo. Tu pasión por la programación y tu dedicación al trabajo en equipo 
            seguirán siendo una inspiración para todos nosotros.
        </p>

        <p class="memorial-text">
            Descansa en paz, querido amigo. Siempre serás recordado.
        </p>

        <div class="candle">🕯️</div>
        
        <p class="dates">
            En nuestros corazones por siempre<br>
            Tu equipo Los reales
        </p>
    </div>
</body>
</html>