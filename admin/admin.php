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
            .product-list {
                margin-top: 30px;
                padding: 25px;
                background: white;
                border-radius: 8px;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
                width: 90%;
                max-width: 900px;
                text-align: center;
            }
            .product-list h2{
                margin-bottom: 15px;
            }
            .product-item{
                padding: 10px;
                border-bottom: 1px solid #ddd;
            }
            .product-item:last-child{
                border-bottom: none;
            }
            .no-products{
                padding: 20px;
                color: #888;
            }
            .product-item:last-child{
                border-bottom: none;
            }
            .product-item{
                display: flex;
                gap: 240px;
            }
            .product-actions{
                display: flex;
                gap: 10px;
            }
            .btn-update{
                background-color: #ffc107;
                color: black;
                padding: 5px 10px;
                text-decoration: none;
                border-radius: 5px;
            }
            .btn-delete{
                background-color: #dc3545;
                color: white;
                padding: 5px 10px;
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

            <section class="product-list">
                <h2> Your Products</h2>
                <?php 
                include '../config.php';
                if(!$conn){
                    die("Database connection failed: " . mysqli_connect_error());
                }

                $result = mysqli_query($conn, "SELECT * FROM products");
                if (mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<div class='product-item'>";
                        echo "<strong>" . htmlspecialchars($row['name']) . "</strong>  Rp " . number_format($row['price'], 0, ',','.');
                        echo "<div class='product-actions'>";
                        echo "<a href='edit_product.php?id=" . $row['id'] . "' class='btn-update' >Update</a>";
                        echo "<a href='delete_product.php?id=" . $row['id'] . "' class='btn-delete' onclick ='return confirm (\"Are You sure?\")'> Delete </a>";
                        echo "</div>";
                        echo "</div>";
                }
                } else{
                    echo"<div class='no-products'> No Products Avaiable.</div>";
                }
                ?>
            </section>
        </div>
    </body>
    </html>