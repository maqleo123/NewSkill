<?php
session_start();

if (isset($_SESSION['access']) && isset($_GET['user'])) {
  $sesion = $_SESSION['access'];
  $usuario = $_GET['user'];

  if ($sesion !== md5($usuario)) {
    header("Location: login.php");
    exit();
  }
}

include_once 'includes/head.php';
include_once 'includes/PDOdb.php';
include_once 'includes/headerPerfil.php';

if (!isset($_GET['i'])) {
  echo "<p>Error: No se especificó el ID del usuario.</p>";
  exit();
}

$idUsuarioHash = $_GET["i"];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE MD5(idusuario) = ?");
$stmt->execute([$idUsuarioHash]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$f) {
  echo "<p>No se encontró el usuario.</p>";
  exit();
}

$idusuarioLimpio = $f["idusuario"];
$Nombre = $f["usuario"];
$rutaimagen = $f["img_perfil"];
$descripcion = $f["descripcion"];
$nivel = $f["nivel"];
$telefono = $f["numero_telefono"];
$modo_tema = $f["modo_tema"]; // ← Aquí se obtiene el modo

$yo = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;



// Verificamos si el usuario logueado ya sigue a este perfil
$seguirConsulta = $pdo->prepare("SELECT 1 FROM seguidores WHERE id_usuario = ? AND id_seguido = ?");
$seguirConsulta->execute([$yo, $idusuarioLimpio]);
$ya_sigue = $seguirConsulta->fetchColumn() !== false;

// Contadores seguidores y seguidos
$seguidoresStmt = $pdo->prepare("SELECT COUNT(*) FROM seguidores WHERE id_seguido = ?");
$seguidoresStmt->execute([$idusuarioLimpio]);
$seguidores_count = $seguidoresStmt->fetchColumn();

$seguidosStmt = $pdo->prepare("SELECT COUNT(*) FROM seguidores WHERE id_usuario = ?");
$seguidosStmt->execute([$idusuarioLimpio]);
$seguidos_count = $seguidosStmt->fetchColumn();

// Obtener insignias del usuario
$insigniasStmt = $pdo->prepare("SELECT i.nombre, i.descripcion, i.imagen FROM usuarios_insignias ui JOIN insignias i ON ui.id_insignia = i.id_insignia WHERE ui.id_usuario = ?");
$insigniasStmt->execute([$idusuarioLimpio]);
$insignias = $insigniasStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="css/perfill.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css" />
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/glide.min.js"></script>

<body class="body-perfil <?= $modo_tema === 'claro' ? 'claro' : '' ?>">
  <main class="main-perfil">
    <div class="perfil-wrapper">

      <div class="foto-perfi-contenedor">
        <img src="<?= htmlspecialchars($rutaimagen) ?>" id="perfil-foto" class="perfil-img" alt="Foto de perfil">
      </div>

      <!-- Modal para ampliar imagen -->
      <div id="modal-foto" class="modal-foto">
        <span class="cerrar-modal">&times;</span>
        <img class="modal-contenido" id="img-ampliada" alt="Imagen ampliada">
      </div>

      <div class="datos-perfil_contenedor">
        <p><a href="verSeguidores.php?id=<?= $idusuarioLimpio ?>&user=<?= urlencode($_GET['user']) ?>&i=<?= urlencode($_GET['i']) ?>" class="link-seguidores" style="text-decoration:none">Seguidores <?= $seguidores_count ?></a></p>
        <p><a href="verSeguidos.php?id=<?= $idusuarioLimpio ?>&user=<?= urlencode($_GET['user']) ?>&i=<?= urlencode($_GET['i']) ?>" class="link-seguidos" style="text-decoration:none">Seguidos <?= $seguidos_count ?></a></p>
        <p>Publicaciones 0</p>
        <p>Habilidades 3</p>
        <p>Cursos 2</p>
      </div>

      <div class="descripcion">
        <p><i class="fas fa-align-left"></i> "<?= htmlspecialchars($descripcion) ?>"</p>
        <p><i class="fas fa-trophy"></i> Nivel: <?= htmlspecialchars($nivel) ?></p>
        <p><i class="fas fa-phone"></i> Teléfono: <?= htmlspecialchars($telefono) ?></p>
      </div>

      <div class="perzonalizar-contenedor">
        <div class="Editar-perfil">
          <p><a href="editarPerfil.php?user=<?= urlencode($_GET["user"]) ?>&i=<?= urlencode($_GET["i"]) ?>">Editar Perfil</a></p>
        </div>
        <div class="Compartir-perfil">
          <p><a href="proximamente.php">Boletas</a></p>
        </div>
        <?php if ($yo != $idusuarioLimpio): ?>
          <div class="Editar-perfil">
            <button id="boton-seguir" data-id="<?= $idusuarioLimpio ?>" class="boton-seguir">
              <?= $ya_sigue ? "Siguiendo" : "Seguir" ?>
            </button>
          </div>
        <?php endif; ?>
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
      <!-- Aqui van las insignias -->
      <div class="insignias-contenedor" style="max-width: 500px; margin: 20px auto; text-align:center;">

        <h3 style="color:white; margin-bottom: 10px;">Insignias</h3>

        <?php if ($insignias): ?>

          <?php
          $maxVisible = 4; // número máximo a mostrar en la fila visible
          $totalInsignias = count($insignias);
          ?>

          <div style="display:flex; justify-content:center; gap:10px; overflow:hidden;">
            <?php for ($i = 0; $i < min($maxVisible, $totalInsignias); $i++): ?>
              <div style="text-align:center; margin-right:10px; ">
                <img
                  src="<?= htmlspecialchars($insignias[$i]['imagen']) ?>"
                  alt="<?= htmlspecialchars($insignias[$i]['nombre']) ?>"
                  style="width:100%; height:100px; object-fit:contain; background:white; border-radius:8px; cursor:pointer;"
                  title="<?= htmlspecialchars($insignias[$i]['nombre']) ?>">
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
              <div style="text-align:center; flex: 0 0 auto; border-radius: 10%; justify-content:center;">
                <img
                  src="<?= htmlspecialchars($insignia['imagen']) ?>"
                  alt="<?= htmlspecialchars($insignia['nombre']) ?>"
                  style="width:100px; height:100px; object-fit:contain; background:white; border-radius:8px; box-shadow: 4px 4px 4px #dacb6a; cursor:pointer; margin-right:10px;">
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





    </div>
  </main>

  <?php include_once 'includes/header.php'; ?>

  <script>
    const modal = document.getElementById("modal-foto");
    const img = document.getElementById("perfil-foto");
    const modalImg = document.getElementById("img-ampliada");
    const cerrar = document.getElementsByClassName("cerrar-modal")[0];

    img.onclick = function() {
      modal.style.display = "block";
      modalImg.src = this.src;
    }

    cerrar.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    }

    // AJAX para botón seguir
    document.getElementById('boton-seguir')?.addEventListener('click', function() {
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

          document.querySelector('.link-seguidores').textContent = 'Seguidores ' + data.seguidores_count;
          document.querySelector('.link-seguidores').href = 'verSeguidores.php?id=' + idUsuario;

          document.querySelector('.link-seguidos').textContent = 'Seguidos ' + data.seguidos_count;
          document.querySelector('.link-seguidos').href = 'verSeguidos.php?id=' + idUsuario;
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