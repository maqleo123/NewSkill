<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Próximamente - NewSkill</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="imagenes/logons50.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #0e121a;
            color: #eaeaea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 1.5rem;
        }

        .contenedor {
            max-width: 600px;
            padding: 2rem;
            border-radius: 16px;
            background-color: #181c25;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1.2s ease-in-out;
        }

        .logo {
            width: 100px;
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 2.2rem;
            color: #00bcd4;
            margin-bottom: 1rem;
        }

        .mensaje {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #ccc;
            margin-bottom: 2rem;
        }

        .mensaje .azul {
            color: #4db8ff;
            font-weight: bold;
        }

        .firma {
            font-style: italic;
            color: #888;
            font-size: 0.95rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .contenedor {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            .mensaje {
                font-size: 1rem;
            }

            .logo {
                width: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <img src="imagenes/LogoNS.png" alt="Logo NewSkill" class="logo" />
        <h1>¡Próximamente!</h1>
        <p class="mensaje">
            El equipo de <strong>NewSkill</strong> está trabajando en algo <span class="azul">impresionante</span> para ti.<br>
            Prepárate para una nueva forma de conectar, aprender y compartir habilidades.
        </p>
        <p class="firma">— Equipo NewSkill</p>
    </div>
</body>
</html>
