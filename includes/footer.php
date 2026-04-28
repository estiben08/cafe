<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Footer CoffeeCol</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .footer-wrapper {
            display: flex;
            flex-direction: column;
        }

        .footer-coffeecol {
            background-image: url('assets/imagenes/banner17.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            overflow: hidden;
        }

        .footer-coffeecol::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: rgba(13, 43, 2, 0.9);
            /* superposición oscura */
            z-index: 0;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            z-index: 1;
            position: relative;
            /* ← para que esté encima de la capa oscura */
        }

        .footer-logo {
            width: 164px;
            height: 155px;
            object-fit: contain;
        }

        .footer-nav {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            margin: 10px 0;
        }

        .footer-nav a {
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 400;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-nav a:hover {
            color: #E8E8E8;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
        }


        .footer-social {
            display: flex;
            gap: 0px;
            margin-top: 5px;
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 19px;
            height: 19px;
            color: white;
        }

        .footer-social a,
        .footer-social a i {
            text-decoration: none !important;
            border: none !important;
            box-shadow: none !important;
        }


        /* Responsive */
        @media (max-width: 768px) {
            .footer-coffeecol {
                padding: 30px 15px;
            }

            .footer-content {
                gap: 20px;
            }

            .footer-logo {
                width: 60px;
                height: 60px;
            }

            .footer-company {
                font-size: 24px;
            }

            .footer-company span {
                font-size: 14px;
            }

            .footer-nav {
                gap: 25px;
            }

            .footer-nav a {
                font-size: 14px;
            }

            .footer-social {
                gap: 20px;
            }

            .footer-social a {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .footer-nav {
                flex-direction: column;
                gap: 15px;
            }

            .footer-social {
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="footer-wrapper">
        <footer class="footer-coffeecol">
            <div class="footer-content">
                <img src="assets/imagenes/banner1.png" alt="Logo de CoffeeCol" class="footer-logo" />

                <nav class="footer-nav">
                    <a href="../index.php">Inicio</a>
                    <a href="includes/nosotros.php">Nosotros</a>
                    <a href="includes/servicios.php">Productos</a>
                    <a href="includes/contacto.php">Contáctanos</a>
                </nav>

                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>