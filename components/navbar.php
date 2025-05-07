<!-- navbar.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Navbar Styles */
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            margin: 0;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar {
            background-color: #014421; /* Dark green */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            list-style: none;
            align-items: center;
        }

        .nav-links li {
            margin: 0 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #e6e6e6;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            outline: none;
        }

        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: white;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: none;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }

        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            color: #333;
        }

        .suggestion-item:hover {
            background-color: #f0f0f0;
        }

        /* User profile dropdown styles */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-icon {
            width:25px;
            height: 25px;
            border-radius: 50%;
            background-color: #ffffff;
            color: #014421;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color:rgb(200, 200, 200);
            color: rgb(55, 55, 55);
        }

        .user-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Mobile responsive styles */
        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 24px;
        }

        @media screen and (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 40px;
                left: -100%;
                width: 60%;
                height: calc(100vh - 60px);
                flex-direction: column;
                background-color: #014421;
                padding: 20px;
                transition: left 0.3s ease;
                z-index: 1000;
            }

            .nav-links.active {
                left: 0;
            }

            .nav-links li {
                margin: 15px 0;
            }

            .search-container {
                width: 60%;
            }

            .dropdown-content {
                position: relative;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">WildAlert</div>
        
        <div class="menu-toggle" id="menuToggle">
            <i class="fa fa-bars"></i>
        </div>
        
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php">Home</a></li>
            <li><a href="library.php">Library</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="upload-image.php">Upload-Image</a></li>
            <li class="user-dropdown">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-icon">
                        <?php 
                        // Display first letter of username as icon
                        $username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'U';
                        echo strtoupper(substr($username, 0, 1)); 
                        ?>
                    </div>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a href="logout.php">Log out</a>
                    </div>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>

    <script>
        // Toggle mobile menu
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });
    </script>
</body>
</html>