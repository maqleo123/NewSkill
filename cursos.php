<?php
include_once 'includes/session.php';
include_once 'includes/PDOdb.php';
include_once './includes/head.php';

$usuario = $_GET['user'];
$i = $_GET['i'];
$id_usuario = $_SESSION['idusuario'];

$modoTema = 'oscuro';
try {
  $stmt = $pdo->prepare("SELECT modo_tema FROM usuarios WHERE idusuario = ?");
  $stmt->execute([$id_usuario]);
  $modo = $stmt->fetchColumn();
  if ($modo && in_array($modo, ['claro', 'oscuro'])) {
    $modoTema = $modo;
  }
} catch (PDOException $e) {
  $modoTema = 'oscuro';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi Repositorio - NewSkill</title>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <link rel="stylesheet" href="css/miRepositorioo.css">
</head>

<body class="<?= $modoTema === 'claro' ? 'claro' : '' ?>">
  <?php include_once 'includes/header.php'; ?>


  <div id="miRepositorio">

    <h1>Mi Repositorio</h1>

    <button id="btnCrearClase">
      <a href="editar_clase.php" style="text-decoration: none; color:white">Crear Clase</a>
    </button>

    <div style="max-width:600px; width: 100%; margin-bottom: 20px;">
      <button class="toggle-btn active" onclick="mostrarLista('creadas', this)">Clases creadas</button>
      <button class="toggle-btn" onclick="mostrarLista('recibidas', this)">Clases recibidas</button>
    </div>

    <div id="clasesCreadas" class="clases-lista activo">
      <h2>📘 Clases que he creado</h2>
      <ul>
        <?php
        $stmt = $pdo->prepare("
    SELECT c.id_clase, c.titulo, c.fecha_creacion,
    CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS es_favorita
    FROM clases c
    LEFT JOIN favoritos f ON f.id_clase = c.id_clase AND f.id_usuario = ?
    WHERE c.id_creador = ?
    ORDER BY es_favorita DESC, c.fecha_creacion DESC
");
        $stmt->execute([$id_usuario, $id_usuario]);
        $clasesCreadas = $stmt->fetchAll();


        if ($clasesCreadas) {
          foreach ($clasesCreadas as $clase) {
            $titulo = htmlspecialchars($clase['titulo']);
            $fecha = htmlspecialchars($clase['fecha_creacion']);
            $id = $clase['id_clase'];

            $estrella = $clase['es_favorita'] ? '★' : '☆'; // llena o vacía

            echo "<li style='margin-bottom: 10px; position: relative;'>
  📘<a href='ver_clase.php?id=$id' style='color:#60a5fa; font-weight:bold; text-decoration:none;'>$titulo</a>
  <span style='color:#94a3b8; font-size: 0.9em;'> - $fecha</span>
  <button onclick=\"abrirModalCompartir($id, '$titulo')\" style='margin-left:10px; background:#3b82f6; color:white; padding:4px 8px; border:none; border-radius:4px; font-size:0.9em;'>Compartir</button>
  <span class='estrella-favorito' data-id='$id' style='position:absolute; right:10px; top:12px; cursor:pointer; font-size:18px;'>$estrella</span>
</li>";
          }
        } else {
          echo "<li>No has creado clases aún.</li>";
        }
        ?>
      </ul>
    </div>

    <div id="clasesRecibidas" class="clases-lista">
      <h2>📥 Clases que me han compartido</h2>
      <ul>
        <?php
        $stmt = $pdo->prepare("
    SELECT c.id_clase, c.titulo, u.usuario AS creador,
           CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS es_favorita
    FROM repositorio r
    JOIN clases c ON r.id_clase = c.id_clase
    JOIN usuarios u ON c.id_creador = u.idusuario
    LEFT JOIN favoritos f ON f.id_clase = c.id_clase AND f.id_usuario = ?
    WHERE r.id_usuario = ? AND c.id_creador != ?
    ORDER BY es_favorita DESC, r.fecha_agregado DESC
");
        $stmt->execute([$id_usuario, $id_usuario, $id_usuario]);
        $clasesRecibidas = $stmt->fetchAll();



        if ($clasesRecibidas) {
          foreach ($clasesRecibidas as $clase) {
            $id = $clase['id_clase'];
            $titulo = htmlspecialchars($clase['titulo']);
            $creador = htmlspecialchars($clase['creador']);

            $estrella = $clase['es_favorita'] ? '★' : '☆';

            echo "<li style='margin-bottom:10px; position: relative;'>
  📥 <a href='ver_clase.php?id=$id' style='color:#60a5fa; font-weight:bold; text-decoration:none;'>$titulo</a>
  <span style='color:#94a3b8; font-size: 0.9em;'> de $creador</span>
  <span class='estrella-favorito' data-id='$id' style='position:absolute; right:10px; top:12px; cursor:pointer; font-size:18px;'>$estrella</span>
</li>";
          }
        } else {
          echo "<li>Aún no has recibido clases de otros usuarios.</li>";
        }
        ?>
      </ul>
    </div>

    <div id="modalCompartir" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#000000cc; z-index:9999; align-items:center; justify-content:center;">
      <div style="background:#1e293b; padding:25px; border-radius:10px; width:90%; max-width:400px; color:#e2e8f0; position:relative;">
        <span onclick="cerrarModal()" style="position:absolute; top:10px; right:15px; cursor:pointer; color:#94a3b8;">✖</span>
        <h3 style="color:#60a5fa; margin-bottom:15px;">Compartir clase: <span id="tituloClaseModal"></span></h3>
        <form action="compartir_clase.php" method="POST">
          <input type="hidden" name="id_clase" id="inputIdClase">
          <label>Nombre del usuario:</label>
          <input type="search" name="nombre_usuario" required style="width:100%; padding:8px; border-radius:5px; margin-bottom:10px; border:none; background:#334155; color:#e2e8f0;" list="listadoUsuarios" placeholder="Buscar usuario..." />
          <datalist id="listadoUsuarios">
            <?php
            $query = "SELECT usuario FROM usuarios ORDER BY usuario ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo '<option value="' . htmlspecialchars($row['usuario'], ENT_QUOTES) . '"/>';
            }
            ?>
          </datalist>
          <label>Permiso:</label>
          <select name="permiso" style="width:100%; padding:8px; border-radius:5px; margin-bottom:20px; background:#334155; color:#e2e8f0; border:none;">
            <option value="lectura">Lectura</option>
            <option value="editable">Editable</option>
          </select>
          <button type="submit" style="background:#38bdf8; color:#1e293b; padding:10px; width:100%; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">
            Compartir clase
          </button>
        </form>
      </div>
    </div>

  </div>

  <script>
    const btnCrear = document.getElementById('btnCrearClase');
    const formCrear = document.getElementById('formularioCrearClase');

    if (btnCrear && formCrear) {
      btnCrear.addEventListener('click', () => {
        if (formCrear.classList.contains('active')) {
          formCrear.classList.remove('active');
          btnCrear.textContent = 'Crear nueva clase';
        } else {
          formCrear.classList.add('active');
          btnCrear.textContent = 'Cerrar formulario';
          window.scrollTo({
            top: formCrear.offsetTop - 20,
            behavior: 'smooth'
          });
        }
      });
    }

    function mostrarLista(tipo, btn) {
      document.getElementById('clasesCreadas').classList.remove('activo');
      document.getElementById('clasesRecibidas').classList.remove('activo');
      document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
      if (tipo === 'creadas') {
        document.getElementById('clasesCreadas').classList.add('activo');
      } else {
        document.getElementById('clasesRecibidas').classList.add('activo');
      }
      btn.classList.add('active');
    }

    function abrirModalCompartir(idClase, titulo) {
      document.getElementById('inputIdClase').value = idClase;
      document.getElementById('tituloClaseModal').textContent = titulo;
      document.getElementById('modalCompartir').style.display = 'flex';
    }

    function cerrarModal() {
      document.getElementById('modalCompartir').style.display = 'none';
    }
  </script>

  <script>
    document.querySelectorAll('.estrella-favorito').forEach(estrella => {
      estrella.addEventListener('click', () => {
        const idClase = estrella.dataset.id;

        fetch('toggle_favorito.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id_clase=' + encodeURIComponent(idClase)
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              estrella.textContent = data.es_favorita ? '★' : '☆';
            }
          });
      });
    });
  </script>


</body>

</html>