<?php
// Determinar si se incluye desde la raíz (index.php) o desde la carpeta includes/
$isRoot = !str_contains($_SERVER['PHP_SELF'] ?? '', '/includes/');
$basePath = $isRoot ? '' : '../';
$incPath  = $isRoot ? 'includes/' : '';
?>
<link rel="stylesheet" href="<?= $basePath ?>css/footer.css">

<footer class="tantico-global-footer">
    <div class="footer-main-container">

        <!-- Logo & Marca -->
        <div class="footer-brand-wrap">
            <a href="<?= $basePath ?>index.php">
                <img src="<?= $basePath ?>assets/imagenes/tantico.png" alt="Tantico Café de Especialidad" class="footer-brand-logo" />
            </a>
            <p class="footer-brand-slogan">Café de Especialidad · Origen Huila · Neiva, Colombia</p>
        </div>

        <!-- Enlaces de Navegación -->
        <nav class="footer-links-nav">
            <a href="<?= $basePath ?>index.php">Inicio</a>
            <a href="<?= $incPath ?>nosotros.php">Nosotros</a>
            <a href="<?= $incPath ?>servicios.php">Productos</a>
            <a href="<?= $incPath ?>contacto.php">Contáctanos</a>
        </nav>

        <!-- Redes Sociales -->
        <div class="footer-social-row">
            <a href="#" class="footer-social-btn" aria-label="Instagram" target="_blank" rel="noopener">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="#" class="footer-social-btn" aria-label="Facebook" target="_blank" rel="noopener">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="#" class="footer-social-btn" aria-label="WhatsApp" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
        </div>

        <div class="footer-divider-line"></div>

        <!-- Badges & Copyright -->
        <div class="footer-bottom-info">
            <div class="footer-badges-pill-row">
                <span class="footer-badge-item"><i class="fa-solid fa-mountain"></i> 100% Café Huilense</span>
                <span class="footer-badge-item"><i class="fa-solid fa-fire-burner"></i> Tueste Artesanal</span>
                <span class="footer-badge-item"><i class="fa-solid fa-handshake-angle"></i> Trato Directo</span>
                <span class="footer-badge-item"><i class="fa-solid fa-award"></i> Calidad SCA</span>
            </div>
            <p class="footer-copyright-text">
                © <?= date('Y') ?> Tantico Café de Especialidad. Todos los derechos reservados.
            </p>
        </div>

    </div>
</footer>