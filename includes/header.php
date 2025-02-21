<?php

// Cek apakah pengguna sudah login
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Header</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins';
            background-color: #FFC0CB;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            width: 100%;
            background-color: #FFC0CB;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .logo {
            width: 200px;
            height: auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
            padding: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: #56021F;
            font-weight: bold;
            font-size: 18px;
        }

        .nav-links a:hover {
            color: rgb(236, 162, 173);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #FFC0CB;
            min-width: 100px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            list-style: none;
            padding: 15px;
            top: 100%;
            left: 0;
            z-index: 1000;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
            color: #56021F;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                right: 0;
                background: #FFC0CB;
                width: 100%;
                padding: 10px 0;
                text-align: center;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>

<body>
    <header>
        <img class="logo" src="/ujikom/admin/uploads/logo.png" alt="Logo">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <nav>
            <ul class="nav-links">
                <li><a href="/ujikom/index.php">Home</a></li>
                <li><a href="/ujikom/users/katalog.php">Katalog</a></li>
                <li><a href="/ujikom/about_us.php">Tentang Kami</a></li>
                <li><a href="/ujikom/users/keranjang.php">Keranjang</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="dropdown">
                        <a href="#" class="dropbtn"><?php echo htmlspecialchars($username); ?> ▼</a>
                        <ul class="dropdown-content">
                            <li><a href="/ujikom/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/ujikom/login.php">Login</a></li>
                <?php endif; ?>


            </ul>
        </nav>
    </header>
    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>

</html>