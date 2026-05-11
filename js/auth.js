import { auth } from '../assets/conexion/firebase.js';
import {
  GoogleAuthProvider,
  signInWithPopup,
  sendSignInLinkToEmail,
  isSignInWithEmailLink,
  signInWithEmailLink,
  onAuthStateChanged,
  signOut
} from "https://www.gstatic.com/firebasejs/10.11.0/firebase-auth.js";

// ── URL base ─────────────────────────────────────
const BASE = 'http://localhost/cafe';

// ── Configuración Magic Link ─────────────────────
const actionCodeSettings = {
  url: BASE + '/includes/login.php',
  handleCodeInApp: true
};

// ── Guardar usuario en sessionStorage ───────────
function guardarUsuario(user) {
  sessionStorage.setItem('cc_usuario', JSON.stringify({
    nombre: user.displayName || user.email,
    email:  user.email,
    foto:   user.photoURL
  }));
}

// ── Guardar usuario en MySQL ─────────────────────
async function guardarUsuarioMySQL(token) {
  try {
    await fetch('/cafe/includes/guardar_usuario.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ token })
    });
  } catch (e) {
    console.error('Error guardando usuario en MySQL:', e);
  }
}

// ── Verificar rol y redirigir ────────────────────
async function verificarRolYRedirigir(user) {
  const token = await user.getIdToken();
  console.log('Token obtenido:', token ? 'SI' : 'NO');

  // ✅ Guardar/actualizar usuario en MySQL
  await guardarUsuarioMySQL(token);

  const res  = await fetch('/cafe/includes/verificar_rol.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify({ token })
  });

  const text = await res.text();
  console.log('Respuesta PHP:', text);

  const data = JSON.parse(text);
  window.location.href = data.redirect;
}

// ── Login con Google ─────────────────────────────
export async function loginGoogle() {
  try {
    const result = await signInWithPopup(auth, new GoogleAuthProvider());
    guardarUsuario(result.user);
    await verificarRolYRedirigir(result.user);
  } catch (e) {
    return { error: e.message };
  }
}

// ── Enviar Magic Link ────────────────────────────
export async function enviarMagicLink(email) {
  try {
    await sendSignInLinkToEmail(auth, email, actionCodeSettings);
    localStorage.setItem('emailForSignIn', email);
    return { ok: true };
  } catch (e) {
    return { error: e.message };
  }
}

// ── Completar Magic Link al regresar ────────────
export async function completarMagicLink() {
  if (!isSignInWithEmailLink(auth, window.location.href)) return;
  let email = localStorage.getItem('emailForSignIn') || prompt('Confirma tu correo:');
  try {
    const result = await signInWithEmailLink(auth, email, window.location.href);
    guardarUsuario(result.user);
    localStorage.removeItem('emailForSignIn');
    await verificarRolYRedirigir(result.user);
  } catch (e) {
    console.error(e);
  }
}

// ── Cerrar sesión (botón) ────────────────────────
export async function cerrarSesion() {
  await signOut(auth);
  sessionStorage.removeItem('cc_usuario');
  await fetch('/cafe/includes/cerrar_sesion.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify({ action: 'logout' })
  });
  window.location.href = BASE + '/index.php';
}

// ── Observar estado usuario ──────────────────────
export function observarUsuario(callbackLogueado, callbackNoLogueado) {
  onAuthStateChanged(auth, (user) => {
    if (user) callbackLogueado(user);
    else callbackNoLogueado();
  });
}