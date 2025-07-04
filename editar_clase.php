<?php
session_start();
if (!isset($_SESSION['idusuario'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Clase - NewSkill</title>

  <!-- Quill -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />

  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #0d1b2a;
      color: #cbd5e1;
    }

    .container {
      max-width: 900px;
      margin: auto;
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    h1 {
      color: #60a5fa;
      margin-bottom: 20px;
    }

    label {
      margin: 10px 0 6px;
      font-weight: bold;
      color: #93c5fd;
    }

    input[type="text"],
    select {
      padding: 10px;
      border-radius: 6px;
      border: none;
      width: 100%;
      background: #1e293b;
      color: #cbd5e1;
      font-size: 1rem;
    }

    #editor {
      height: 300px;
      background: white;
      border-radius: 6px;
      margin-top: 8px;
    }

    input[type="file"] {
      background: #1e293b;
      padding: 10px;
      border: none;
      color: #cbd5e1;
      width: 100%;
    }

    button {
      margin-top: 20px;
      padding: 12px;
      background: #2563eb;
      color: #e2e8f0;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background: #3b82f6;
    }

    #lista-archivos {
      margin-top: 8px;
      font-size: 0.95em;
      color: #60a5fa;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Crear nueva clase</h1>
    <form action="crear_clase.php" method="POST" enctype="multipart/form-data" id="formCrearClase">
      <label for="titulo">Título:</label>
      <input type="text" name="titulo" id="titulo" required>

      <label for="descripcion">Descripción:</label>
      <div id="editor"></div>
      <input type="hidden" name="descripcion" id="descripcionInput" required>

      <label for="visibilidad">Visibilidad:</label>
      <select name="visibilidad" id="visibilidad" required>
        <option value="privada">Privada</option>
        <option value="publica">Pública</option>
      </select>

      <label for="materiales">Materiales (PDF, imágenes, etc.):</label>
      <input type="file" name="materiales[]" id="materiales" multiple>
      <div id="lista-archivos">No has seleccionado archivos.</div>

      <button type="submit" name="crear_clase">Guardar clase</button>
    </form>
  </div>

  <!-- Quill JS -->
  <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
   <script>
  const toolbarOptions = [
    [{ 'header': [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    ['code-block'],
    ['clean']
  ];

  const quill = new Quill('#editor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions }
  });

  // Fijar estilos oscuros generales
  document.querySelector('.ql-editor').style.color = '#000';
  document.querySelector('.ql-editor').style.backgroundColor = '#fff';

  document.getElementById('formCrearClase').onsubmit = function() {
    document.getElementById('descripcionInput').value = quill.root.innerHTML.trim();
  };
</script>

</body>
</html>
