<?php
include_once 'includes/PDOdb.php';
include_once 'includes/session.php';
include_once 'includes/head.php';

$UsuarioBuscado = $_GET['UsuarioB'];
$idUsuarioB = $_GET['idUsuarioB'];

// Preparar y ejecutar consulta para buscar usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE idusuario = ?");
$stmt->execute([$idUsuarioB]);

// Verificar si se encontró el usuario
if ($stmt->rowCount() === 0) {
    echo "<p>Usuario no encontrado.</p>";
    exit();
}

// Obtener datos del usuario
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$usuarioencontrado = $row["usuario"];
$nombreencontrado = $row["nombre"];
$fotoperfil = $row["img_perfil"];
$descripcion = $row["descripcion"];
$nivel = $row["nivel"];
$telefono = $row["numero_telefono"];

// Verificar si el usuario logueado ya sigue al usuario buscado
$yo = $_SESSION['idusuario'];
$ya_sigue = false;

$seguirStmt = $pdo->prepare("SELECT 1 FROM seguidores WHERE id_usuario = ? AND id_seguido = ?");
$seguirStmt->execute([$yo, $idUsuarioB]);

if ($seguirStmt->rowCount() > 0) {
    $ya_sigue = true;
}


$stmtSeguidores = $pdo->prepare("SELECT COUNT(*) as total FROM seguidores WHERE id_seguido = ?");
$stmtSeguidores->execute([$idUsuarioB]);
$seguidores_count = $stmtSeguidores->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener insignias del usuario
$insigniasStmt = $pdo->prepare("SELECT i.nombre, i.descripcion, i.imagen FROM usuarios_insignias ui JOIN insignias i ON ui.id_insignia = i.id_insignia WHERE ui.id_usuario = ?");
$insigniasStmt->execute([$idUsuarioB]);
$insignias = $insigniasStmt->fetchAll(PDO::FETCH_ASSOC);




?>




<link rel="stylesheet" href="css/perfilUsuario.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<header class="perfil-header">
    <nav class="navbar-perfil">
        <p class="Nombre-Usuario"><?php echo htmlspecialchars($usuarioencontrado); ?></p>
    </nav>
</header>

<body class="body-perfil">
    <main class="main-perfil">
        <div class="perfil-wrapper">
            <div class="foto-perfi-contenedor">
                <img src="<?= $fotoperfil ?>" alt="Foto de perfil" onclick="expandirImagen('<?= $fotoperfil ?>')">
            </div>
            <div class="datos-perfil_contenedor">
                <p><a href="verSeguidores.php?id=<?= $idUsuarioB ?>&user=<?=$_GET['user']?>&i=<?=$_GET ['i']?>" class="link-seguidores" style="text-decoration:none">Seguidores <?= $seguidores_count ?></a></p>
                <p>Publicaciones 0</p>
                <p>Habilidades 3</p>
                <p>Cursos 2</p>
            </div>
            <div class="descripcion">
                <p style="color: black;">
                    <i class="fas fa-align-left"></i> "<?= $descripcion ?>"
                </p>
                <p style="color: black;">
                    <i class="fas fa-trophy"></i> Nivel: <?= $nivel ?>
                </p>
                <p style="color: black;">
                    <i class="fas fa-phone"></i> Teléfono: <?= $telefono ?>
                </p>
            </div>
            <div class="perzonalizar-contenedor">
                <div class="Editar-perfil">
                    <p><a href="chat.php?user=<?= $_GET['user'] ?>&id=<?= $idUsuarioB ?>&i=<?= $_GET['i'] ?>">Contactar</a></p>
                </div>
                <div class="Editar-perfil">
                    <?php if ($yo != $idUsuarioB): ?>
                    <button id="boton-seguir" data-id="<?= $idUsuarioB ?>" class="boton-seguir">
                        <?= $ya_sigue ? "Siguiendo" : "Seguir" ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="habilidades-contenedor">
                <p>HABILIDADES</p>

                <div class="habilidad-card" style="background-image: url('imagenes/Guitarra.png');">
                    <p>Guitarra: Avanzado</p>
                </div>

                <div class="habilidad-card" style="background-image: url('imagenes/programacion.png');">
                    <p>Programación: Intermedio</p>
                </div>

                <div class="habilidad-card" style="background-image: url('imagenes/matematicas.png');">
                    <p>Matemáticas: Amateur</p>
                </div>
            </div>

            <h3 style="color:white; margin-bottom: 10px;">Insignias</h3>
          <!-- Aqui van las insignias -->
<div class="insignias-contenedor" style="max-width: 500px; margin: 20px auto; text-align:center;">

  

  <?php if ($insignias): ?>

    <?php 
    $maxVisible = 4; // número máximo a mostrar en la fila visible
    $totalInsignias = count($insignias);
    ?>

    <div style="display:flex; justify-content:center; gap:10px; overflow:hidden;">
      <?php for ($i = 0; $i < min($maxVisible, $totalInsignias); $i++): ?>
        <div style="text-align:center; justify-content:center; margin-right:10px;">
          <img 
            src="<?= htmlspecialchars($insignias[$i]['imagen']) ?>" 
            alt="<?= htmlspecialchars($insignias[$i]['nombre']) ?>" 
            style="width:100%; height:100px; object-fit:contain; background:white; border-radius:8px; cursor:pointer; "
            title="<?= htmlspecialchars($insignias[$i]['nombre']) ?>"
          >
        </div>
      <?php endfor; ?>
    </div>

    <?php if ($totalInsignias > $maxVisible): ?>
      <button id="btnVerTodas" 
              style="margin-top:15px; background:#4a90e2; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:bold;">
        Ver todas las insignias (<?= $totalInsignias ?>)
      </button>
    <?php endif; ?>

  <?php else: ?>
    <p style="color:#94a3b8;">Este usuario aún no tiene insignias.</p>
  <?php endif; ?>

</div>

<!-- Modal para todas las insignias -->
<div id="modalInsignias" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; 
     background: rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:1000;">
  <div style="background:#1e1e2f; padding:20px; border-radius:10px; max-width:90vw; max-height:80vh; overflow:auto;">

    <h2 style="text-align:center; margin-bottom:20px;">Todas las Insignias</h2>
    <div style="display:flex; flex-wrap: nowrap; overflow-x: auto; gap:15px;">

      <?php foreach ($insignias as $insignia): ?>
        <div style="text-align:center; flex: 0 0 auto;  border-radius: 10%; justify-content:center;" >
          <img 
            src="<?= htmlspecialchars($insignia['imagen']) ?>" 
            alt="<?= htmlspecialchars($insignia['nombre']) ?>" 
            style="width:100px; height:100px; object-fit:contain; background:white; border-radius:8px; box-shadow: 4px 4px 4px #dacb6a; cursor:pointer; margin-right:10px;"
          >
        </div>
      <?php endforeach; ?>

    </div>

    <button id="cerrarModal" 
            style="margin-top:20px; display:block; margin-left:auto; margin-right:auto; background:#4a90e2; color:#fff; 
                   border:none; padding:8px 20px; border-radius:6px; cursor:pointer; font-weight:bold;">
      Cerrar
    </button>

  </div>
</div>

            <div class="contenedor-recomendaciones">
                <p>Críticas y recomendaciones</p>
                <div class="critica-contenedor">
                    <p>DyyBautista:</p>
                    <p>Muy buenas explicaciones</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para mostrar la imagen expandida -->
    <div id="modalFoto" class="modal-foto" onclick="cerrarModal(event)">
        <span class="cerrar-modal" onclick="cerrarModal(event)">&times;</span>
        <img class="modal-contenido" id="imgExpandidaPerfil">
    </div>

    <script>
        function expandirImagen(src) {
            const modal = document.getElementById("modalFoto");
            const img = document.getElementById("imgExpandidaPerfil");
            modal.style.display = "flex";
            img.src = src;
        }

        function cerrarModal(e) {
            if (e.target.id === "modalFoto" || e.target.classList.contains("cerrar-modal")) {
                document.getElementById("modalFoto").style.display = "none";
            }
        }

        document.getElementById('boton-seguir')?.addEventListener('click', function () {
            const boton = this;
            const idUsuario = boton.getAttribute('data-id');

            fetch('seguir_ajax.php?id=' + idUsuario)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                boton.textContent = data.accion === 'siguiendo' ? 'Siguiendo' : 'Seguir';

                const linkSeguidores = document.querySelector('.link-seguidores');
                if (linkSeguidores) {
                    linkSeguidores.textContent = 'Seguidores ' + data.seguidores_count;
                    linkSeguidores.href = 'verSeguidores.php?id=' + idUsuario;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud.');
            });
        });
    </script>
    <script>
  document.getElementById('btnVerTodas')?.addEventListener('click', () => {
    document.getElementById('modalInsignias').style.display = 'flex';
  });

  document.getElementById('cerrarModal')?.addEventListener('click', () => {
    document.getElementById('modalInsignias').style.display = 'none';
  });

  // Cerrar modal si se hace clic fuera del contenido
  document.getElementById('modalInsignias')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) {
      document.getElementById('modalInsignias').style.display = 'none';
    }
  });
</script>

</body>

<?php include_once 'includes/header.php'; ?>
