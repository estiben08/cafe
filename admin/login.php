<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-dark">

    <div class="card p-4" style="width:300px;">
        <h4>Admin Login</h4>

        <input id="user" class="form-control mb-2" placeholder="Usuario">
        <input id="pass" type="password" class="form-control mb-2" placeholder="Password">

        <button class="btn btn-dark w-100" onclick="login()">Entrar</button>
    </div>

    <script>
        function login() {
            fetch('../includes/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    usuario: document.getElementById('user').value,
                    password: document.getElementById('pass').value
                })
            })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        localStorage.setItem('token', d.token);
                        location.href = 'dashboard.php';
                    } else {
                        alert(d.error); // 👈 muestra el mensaje real
                    }
                });
        }

    </script>

</body>

</html>