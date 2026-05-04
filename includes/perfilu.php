<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil - CoffeeCol</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/perfil.css">
  <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">
</head>
<body>

  <a href="../index.php" class="btn-volver">
    <i class="fa-solid fa-arrow-left"></i> Volver
  </a>

  <div class="perfil-wrapper">

    <!-- SIDEBAR -->
    <div class="perfil-sidebar">
      <div class="perfil-avatar" id="perfil-avatar">
        <i class="fa-solid fa-user"></i>
      </div>
      <h3 id="perfil-nombre-display">Cargando...</h3>
      <p id="perfil-email-display" class="perfil-email"></p>

      <div class="perfil-puntos-box">
        <i class="fa-solid fa-star"></i>
        <div>
          <span id="perfil-puntos">100</span>
          <small>puntos acumulados</small>
        </div>
      </div>
    </div>

    <!-- CONTENIDO -->
    <div class="perfil-contenido">

      <!-- Datos personales -->
      <div class="perfil-card">
        <h4><i class="fa-solid fa-pen-to-square me-2"></i>Mis datos</h4>

        <div class="perfil-campo">
          <label>Celular</label>
          <input type="tel" id="inp-celular" placeholder="Ej: 3001234567">
        </div>

        <div class="perfil-campo">
          <label>Género</label>
          <div class="perfil-genero">
            <label class="genero-opcion">
              <input type="radio" name="genero" value="mujer"> Mujer
            </label>
            <label class="genero-opcion">
              <input type="radio" name="genero" value="hombre"> Hombre
            </label>
            <label class="genero-opcion">
              <input type="radio" name="genero" value="otro"> Otro
            </label>
          </div>
        </div>

        <div class="perfil-acciones">
          <button id="btn-actualizar" class="btn-primario">
            <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar datos
          </button>
          <button id="btn-eliminar" class="btn-peligro">
            <i class="fa-solid fa-trash me-1"></i> Eliminar cuenta
          </button>
        </div>
        <p id="msg-perfil" class="msg-perfil"></p>
      </div>

      <!-- Últimas órdenes -->
      <div class="perfil-card">
        <h4><i class="fa-solid fa-box me-2"></i>Mis órdenes</h4>
        <div id="ordenes-lista">
          <p class="text-muted text-center py-3">Cargando órdenes...</p>
        </div>
      </div>

    </div>
  </div>

  <script type="module">
    import { auth } from '../assets/conexion/firebase.js';
    import { onAuthStateChanged, deleteUser } from
      "https://www.gstatic.com/firebasejs/10.11.0/firebase-auth.js";
    import {
      obtenerPerfil, guardarPerfil,
      eliminarCuentaFirestore, obtenerOrdenes
    } from '../js/firestore.js';

    let usuarioActual = null;

    onAuthStateChanged(auth, async (user) => {
      if (!user) {
        window.location.href = '../includes/loginu.php';
        return;
      }
      usuarioActual = user;

      // Mostrar datos básicos
      document.getElementById('perfil-nombre-display').textContent =
        user.displayName || user.email;
      document.getElementById('perfil-email-display').textContent = user.email;

      if (user.photoURL) {
        document.getElementById('perfil-avatar').innerHTML =
          `<img src="${user.photoURL}" alt="foto">`;
      }

      // Cargar perfil de Firestore
      const perfil = await obtenerPerfil(user.uid);
      if (perfil) {
        document.getElementById('inp-celular').value = perfil.celular || '';
        document.getElementById('perfil-puntos').textContent = perfil.puntos || 0;
        if (perfil.genero) {
          const radio = document.querySelector(`input[name="genero"][value="${perfil.genero}"]`);
          if (radio) radio.checked = true;
        }
      }

      // Cargar órdenes
      cargarOrdenes(user.uid);
    });

    // Actualizar datos
    document.getElementById('btn-actualizar').addEventListener('click', async () => {
      if (!usuarioActual) return;
      const celular = document.getElementById('inp-celular').value.trim();
      const genero  = document.querySelector('input[name="genero"]:checked')?.value || '';
      const msg = document.getElementById('msg-perfil');

      try {
        await guardarPerfil(usuarioActual.uid, { celular, genero });
        msg.textContent = '✅ Datos actualizados correctamente.';
        msg.style.color = '#28040A';
      } catch (e) {
        msg.textContent = '❌ Error al actualizar.';
        msg.style.color = '#c0392b';
      }
    });

    // Eliminar cuenta
    document.getElementById('btn-eliminar').addEventListener('click', async () => {
      if (!usuarioActual) return;
      const confirmar = confirm('¿Estás seguro? Esta acción no se puede deshacer.');
      if (!confirmar) return;
      try {
        await eliminarCuentaFirestore(usuarioActual.uid);
        await deleteUser(usuarioActual);
        localStorage.removeItem('cc_usuario');
        window.location.href = '../index.php';
      } catch (e) {
        document.getElementById('msg-perfil').textContent =
          '❌ Error al eliminar. Vuelve a iniciar sesión e intenta de nuevo.';
      }
    });

    // Cargar órdenes
    async function cargarOrdenes(uid) {
      const lista = document.getElementById('ordenes-lista');
      try {
        const ordenes = await obtenerOrdenes(uid);
        if (ordenes.length === 0) {
          lista.innerHTML = '<p class="text-muted text-center py-3">No tienes órdenes aún.</p>';
          return;
        }
        lista.innerHTML = ordenes.map(o => `
          <div class="orden-item">
            <div class="orden-info">
              <span class="orden-id">#${o.id.slice(0,8).toUpperCase()}</span>
              <span class="orden-fecha">${o.fecha?.toDate
                ? o.fecha.toDate().toLocaleDateString('es-CO')
                : 'Fecha no disponible'}</span>
            </div>
            <div class="orden-detalle">
              ${(o.items || []).map(i =>
                `<span>${i.nombre} x${i.cantidad}</span>`
              ).join(', ')}
            </div>
            <div class="orden-footer">
              <span class="orden-total">$${(o.total || 0).toLocaleString('es-CO')}</span>
              <span class="orden-estado estado-${o.estado}">${o.estado}</span>
            </div>
          </div>
        `).join('');
      } catch (e) {
        lista.innerHTML = '<p class="text-muted text-center py-3">Error al cargar órdenes.</p>';
      }
    }
  </script>

</body>
</html>