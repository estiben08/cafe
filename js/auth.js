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

const BASE = (typeof window !== 'undefined' && window.location.origin) 
  ? window.location.origin + '/cafe' 
  : 'http://localhost/cafe';

const actionCodeSettings = {
  url: BASE + '/includes/loginu.php',
  handleCodeInApp: true
};

function guardarUsuario(user) {
  sessionStorage.setItem('cc_usuario', JSON.stringify({
    nombre: user.displayName || user.email,
    email:  user.email,
    foto:   user.photoURL,
    uid:    user.uid
  }));
}

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

async function verificarRolYRedirigir(user) {
  try {
    const token = await user.getIdToken();
    await guardarUsuarioMySQL(token);
    const res  = await fetch('/cafe/includes/verificar_rol.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ token })
    });
    const data = await res.json();
    if (data?.redirect) {
      window.location.href = data.redirect;
    } else {
      window.location.href = '/cafe/index.php';
    }
  } catch (err) {
    console.error('Error en verificarRolYRedirigir:', err);
    window.location.href = '/cafe/index.php';
  }
}

export async function loginGoogle() {
  try {
    const result = await signInWithPopup(auth, new GoogleAuthProvider());
    guardarUsuario(result.user);
    await verificarRolYRedirigir(result.user);
  } catch (e) {
    return { error: e.message };
  }
}

export async function enviarMagicLink(email) {
  try {
    await sendSignInLinkToEmail(auth, email, actionCodeSettings);
    localStorage.setItem('emailForSignIn', email);
    return { ok: true };
  } catch (e) {
    return { error: e.message };
  }
}

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

export async function cerrarSesion() {
  try {
    // 1. Cierra la sesión en Firebase (limpia IndexedDB y memoria)
    await signOut(auth);
  } catch (e) {
    console.error('Error signOut Firebase:', e);
  }

  // 2. Limpia sessionStorage
  sessionStorage.removeItem('cc_usuario');

  // 3. Borra la cookie fb_token desde el cliente como respaldo
  const cookiePaths = ['/', '/cafe', '/cafe/includes'];
  cookiePaths.forEach(path => {
    document.cookie = `fb_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path};`;
  });

  // 4. Llama al backend para destruir la cookie httpOnly y la sesión PHP
  try {
    await fetch('/cafe/includes/cerrar_sesion.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ action: 'logout' })
    });
  } catch (e) {
    console.error('Error cerrando sesión en backend:', e);
  }

  // 5. Redirige al login
  window.location.href = BASE + '/includes/loginu.php';
}

export function observarUsuario(callbackLogueado, callbackNoLogueado) {
  // Esperamos a que Firebase resuelva el estado real antes de ejecutar callbacks
  // Esto evita el parpadeo del menú al cargar la página tras el logout
  let resuelto = false;

  onAuthStateChanged(auth, (user) => {
    resuelto = true;
    if (user) {
      callbackLogueado(user);
    } else {
      // Limpieza defensiva: si Firebase dice que no hay sesión, borramos todo
      sessionStorage.removeItem('cc_usuario');
      callbackNoLogueado();
    }
  });

  // Timeout de seguridad: si Firebase tarda más de 3s, forzamos estado sin sesión
  setTimeout(() => {
    if (!resuelto) {
      sessionStorage.removeItem('cc_usuario');
      callbackNoLogueado();
    }
  }, 3000);
}