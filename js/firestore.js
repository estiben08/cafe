import { auth } from '../assets/conexion/firebase.js';
import {
  getFirestore,
  doc,
  getDoc,
  setDoc,
  updateDoc,
  deleteDoc,
  collection,
  query,
  where,
  orderBy,
  getDocs,
  addDoc,
  serverTimestamp
} from "https://www.gstatic.com/firebasejs/10.11.0/firebase-firestore.js";

export const db = getFirestore();

// ── Obtener perfil del usuario ───────────────────
export async function obtenerPerfil(uid) {
  const ref = doc(db, 'usuarios', uid);
  const snap = await getDoc(ref);
  return snap.exists() ? snap.data() : null;
}

// ── Guardar/actualizar perfil ────────────────────
export async function guardarPerfil(uid, datos) {
  const ref = doc(db, 'usuarios', uid);
  await setDoc(ref, datos, { merge: true });
}

// ── Eliminar cuenta ──────────────────────────────
export async function eliminarCuentaFirestore(uid) {
  await deleteDoc(doc(db, 'usuarios', uid));
}

// ── Obtener órdenes del usuario ──────────────────
export async function obtenerOrdenes(uid) {
  const q = query(
    collection(db, 'ordenes'),
    where('uid', '==', uid),
    orderBy('fecha', 'desc')
  );
  const snap = await getDocs(q);
  return snap.docs.map(d => ({ id: d.id, ...d.data() }));
}

// ── Crear orden ──────────────────────────────────
export async function crearOrden(uid, items, total) {
  await addDoc(collection(db, 'ordenes'), {
    uid,
    items,
    total,
    estado: 'pendiente',
    fecha: serverTimestamp()
  });
}

// ── Obtener productos ────────────────────────────
export async function obtenerProductos() {
  const snap = await getDocs(collection(db, 'productos'));
  return snap.docs.map(d => ({ id: d.id, ...d.data() }));
}

// ── Obtener todos los usuarios (admin) ───────────
export async function obtenerTodosUsuarios() {
  const snap = await getDocs(collection(db, 'usuarios'));
  return snap.docs.map(d => ({ id: d.id, ...d.data() }));
}

// ── Obtener todas las órdenes (admin) ────────────
export async function obtenerTodasOrdenes() {
  const q = query(collection(db, 'ordenes'), orderBy('fecha', 'desc'));
  const snap = await getDocs(q);
  return snap.docs.map(d => ({ id: d.id, ...d.data() }));
}

// ── Actualizar estado de orden (admin) ───────────
export async function actualizarEstadoOrden(id, estado) {
  await updateDoc(doc(db, 'ordenes', id), { estado });
}