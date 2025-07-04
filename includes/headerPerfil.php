<?php
session_start();
include_once 'includes/PDOdb.php';

if (!isset($_SESSION['idusuario'])) {
    echo "<p>Acceso denegado</p>";
    exit();
}

$idusuario = $_SESSION['idusuario'];

$stmt = $pdo->prepare("SELECT usuario, modo_tema FROM usuarios WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datosUsuario) {
    echo "<p>Error al obtener datos del usuario.</p>";
    exit();
}

$usuario = $datosUsuario['usuario'];
$modo = $datosUsuario['modo_tema'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #ffffff;
            --header-bg: #262626;
            --menu-bg: #2a2a3a;
            --menu-hover: #3a3a4f;
            --menu-border: #444;
        }

        body.claro {
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --header-bg: #ffffff;
            --menu-bg: #ffffff;
            --menu-hover: #e9ecef;
            --menu-border: #ced4da;
        }

        body {
            margin: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nabar-perfil {
            background-color: var(--header-bg);
            position: fixed;
            top: 0;
            width: 100%;
            height: 50px;
            padding: 0 20px;
            z-index: 100;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--menu-border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            user-select: none;
        }

        .nombre-container {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
        }

        .Nombre-Usuario {
            font-size: 18px;
            font-weight: bold;
            color: var(--text-color);
            margin: 0;
        }

        .menu-container {
            margin-left: auto;
            position: relative;
        }

        .menu-icon {
            font-size: 24px;
            color: var(--text-color);
            cursor: pointer;
            padding: 5px 10px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background-color: var(--menu-bg);
            border: 1px solid var(--menu-border);
            border-radius: 8px;
            min-width: 100px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            overflow: hidden;
            flex-direction: column;
        }

        .menu-item {
            padding: 12px;
            text-align: center;
            color: var(--text-color);
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .menu-item:hover {
            background-color: var(--menu-hover);
        }

        .menu-item i {
            pointer-events: none;
        }
    </style>
</head>
<body class="<?= $modo === 'claro' ? 'claro' : '' ?>">
<nav class="nabar-perfil">
    <div class="nombre-container">
        <p class="Nombre-Usuario"><?= htmlspecialchars($usuario) ?></p>
    </div>
    <div class="menu-container">
        <div class="menu-icon" id="menuToggle" title="Abrir menú">
            <i class="fas fa-ellipsis-v"></i>
        </div>
        <div class="dropdown-menu" id="dropdownMenu">
            <div class="menu-item" id="modoBtn" title="Cambiar modo">
                <i class="fas <?= $modo === 'claro' ? 'fa-moon' : 'fa-sun' ?>"></i>
            </div>
            <div class="menu-item" id="cerrarSesionBtn" title="Cerrar sesión">
                <i class="fas fa-power-off"></i>
            </div>
        </div>
    </div>
</nav>

<script>
    const menuToggle = document.getElementById("menuToggle");
    const dropdownMenu = document.getElementById("dropdownMenu");

    menuToggle.addEventListener("click", () => {
        dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
    });

    window.addEventListener("click", (e) => {
        if (!menuToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.style.display = "none";
        }
    });

    document.getElementById("modoBtn").addEventListener("click", function () {
        const body = document.body;
        const icon = this.querySelector("i");
        const nuevoModo = body.classList.contains("claro") ? "oscuro" : "claro";

        body.classList.toggle("claro");
        icon.className = "fas " + (nuevoModo === "claro" ? "fa-moon" : "fa-sun");
        dropdownMenu.style.display = "none";

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "guardarModo.php", true); 
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status !== 200 || xhr.responseText.trim() !== "OK") {
                alert("Error al guardar el modo.");
            }
        };
        xhr.onerror = function () {
            alert("Error de conexión al guardar el modo.");
        };
        xhr.send("modo=" + encodeURIComponent(nuevoModo));
    });

    document.getElementById("cerrarSesionBtn").addEventListener("click", function () {
        window.location.href = "login.php";
    });
</script>
</body>
</html>
