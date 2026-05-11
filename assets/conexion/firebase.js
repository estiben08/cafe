import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-app.js";
import { 
  getAuth,
  setPersistence,
  browserSessionPersistence
} from "https://www.gstatic.com/firebasejs/10.11.0/firebase-auth.js";

const firebaseConfig = {
  apiKey:            "AIzaSyD9JB8k6Q9s_EOT9rTp1TlJPU0lAdLpemQ",
  authDomain:        "cafetantico.firebaseapp.com",
  projectId:         "cafetantico",
  storageBucket:     "cafetantico.firebasestorage.app",
  messagingSenderId: "737272686581",
  appId:             "1:737272686581:web:ea063616c48c57dcab22bc"
};

const app  = initializeApp(firebaseConfig);
export const auth = getAuth(app);

// Con browserSessionPersistence Firebase guarda la sesión
// solo mientras la pestaña esté abierta.
// Al cerrar el navegador o la pestaña, Firebase olvida al usuario
// automáticamente — sin necesitar beforeunload ni sendBeacon.
await setPersistence(auth, browserSessionPersistence);