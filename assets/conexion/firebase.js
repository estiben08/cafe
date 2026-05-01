import { initializeApp } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.11.0/firebase-auth.js";

const firebaseConfig = {
  apiKey: "AIzaSyD9JB8k6Q9s_EOT9rTp1TlJPU0lAdLpemQ",
  authDomain: "cafetantico.firebaseapp.com",
  projectId: "cafetantico",
  storageBucket: "cafetantico.firebasestorage.app",
  messagingSenderId: "737272686581",
  appId: "1:737272686581:web:ea063616c48c57dcab22bc"
};

const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);