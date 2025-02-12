    <?php 
    include '../config.php';

    session_start();
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== "admin") {
        header("Location: login.php");
        exit();
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
        <title>Admin Dashboard</title>
        <style>
            body{
                margin: 0;
                font-family: Poppins;
                display: flex;
            }
            .sidebar{
                width: 250px;
                background: #333;
                color: wheat;
                padding: 20px;
                height: 100vh;
                position: fixed;
                flex-direction: column;
                justify-content: space-between;
            }
            .sidebar h2{
                text-align: center;
                margin-bottom: 20px;
            }
            .sidebar ul{
                list-style: none;
                padding: 0;
                flex-grow: 1;
            }
            .sidebar  ul li{
                padding: 15px 0;
            }
            .sidebar ul li a{
                color: white;
                text-decoration: none;
                display: block;
            }
            .sidebar ul li a :hover{
                background-color: #575757;
                padding-left: 10px;
            }
            .main-content {
                margin-left: 250px;
                padding: 40px;
                flex-grow: 1;
                background: #f8f9fa;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            header {
                background: #34495e;
                color: white;
                padding: 15px;
                text-align: center;
                width: 100%;
                border-radius: 8px;
            }
            .btn{
                display:inline-block ;
                padding: 10px 15px;
                background-color: #28a745;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin-top: 10px;
            }
            .btn:hover{
                background-color: #218838;
            }
            table {
                width: 80%;
                margin-top: 40px;
                background-color: white;
                border-radius: 15px;
                overflow: hidden;
                border-collapse: collapse;
            }
            th, td {
                padding: 15px;
                text-align: center;
                border-bottom: 2px solid #ddd;
            }   
            tr:last-child td{
                border-bottom: none; /* Hilangkan border bawah pada baris terakhir */
            }
            th{
                background-color: #2c3e50;
                color: white;
            }
            td{
                font-size: 16px;
                color:black;
            }
            
            .btn-update{
                display: inline-block;
                background-color: #ffc107;
                color: black;
                padding: 8px 12px;
                margin: 5px;
                text-decoration: none;
                border-radius: 5px;
                font-size: 14px;
             }
            .btn-delete {
                display: inline-block;
                background-color: #dc3545;
                color: white;
                padding: 8px 12px;
                margin: 5px;
                border-radius: 5px;
                font-size: 14px;
                text-decoration: none;
                border-radius: 5px;
             }
            .btn-update:hover{
                background-color: #e0a800;
            }
            .btn-delete:hover{
                background-color: #c82333;
            }
        </style>
    </head>
    <body>
        <div class="sidebar">
            <h2> Admin Panel </h2>
            <ul> 
                <li><a href=""> Dashboard</a></li>
                <li><a href="form_product.php"> Add a new product</a></li>
                <li><a href="logout.php"> Logout</a></li>
            </ul>
        </div>
            <div class="main-content">
            <h2> Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <p> This is your page</p>
            <h3> Manage ur Products</h3>
            <a href="form_product.php" class="btn"> Add New product</a>
            
            <table>
                <tr>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>

                <?php 
                include '../config.php';

                $result = mysqli_query($conn, "SELECT * FROM products");

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                        echo "<td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>";
                        echo "<td>
                                <a href='edit.php?id=" . $row['id'] . "' class='btn-update'>Update</a>
                                <a href='delete.php?id=" . $row['id'] . "' class='btn-delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>No Products Available.</td></tr>";
                }
                ?>
            </table>

        </div>
    </body>
    </html>