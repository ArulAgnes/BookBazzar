<?php
session_start();
require "./functions/database_functions.php";

$title = "Checking out";
require "./template/header.php";

if (!isset($_SESSION['user'])) {
    echo '<div class="alert alert-danger" role="alert">
            You Need to <a href="Signin.php">Sign In</a> First! 
          </div>';
    exit; // Stop further processing if the user is not signed in
}

// Check if form is submitted
if (isset($_POST['add'])) {
    $conn = db_connect();

    // Sanitize input fields
    $isbn = trim($_POST['isbn']);
    $isbn = mysqli_real_escape_string($conn, $isbn);
    
    $bookTitle = trim($_POST['title']);
    $bookTitle = mysqli_real_escape_string($conn, $bookTitle);

    $type = trim($_POST['type']);
    $type = mysqli_real_escape_string($conn, $type);

    $author = trim($_POST['author']);
    $author = mysqli_real_escape_string($conn, $author);
    
    $descr = trim($_POST['descr']);
    $descr = mysqli_real_escape_string($conn, $descr);
    
    $price = floatval(trim($_POST['price']));
    $price = mysqli_real_escape_string($conn, $price);
    
    $publisher = trim($_POST['publisher']);
    $publisher = mysqli_real_escape_string($conn, $publisher);
    
    $category = trim($_POST['category']);
    $category = mysqli_real_escape_string($conn, $category);
    
    // New fields for Gmail and phone number
    $gmail = trim($_POST['gmail']);
    $gmail = mysqli_real_escape_string($conn, $gmail);
    
    $phone = trim($_POST['phone']);
    $phone = mysqli_real_escape_string($conn, $phone);

    // Handling image upload
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];
        $directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
        $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . "bootstrap/img/";
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory . $image);
    } else {
        $image = null; // Handle the case where no image is provided
    }

    // Create a new table to store the book data
    $newTableName = "dynamic_books_table";
    $createTableQuery = "CREATE TABLE IF NOT EXISTS $newTableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        isbn VARCHAR(255) NOT NULL,
        type VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        image VARCHAR(255),
        descr TEXT,
        price FLOAT,
        publisher VARCHAR(255),
        category VARCHAR(255)
    )";

    if (!mysqli_query($conn, $createTableQuery)) {
        echo "Error creating table: " . mysqli_error($conn);
        exit;
    }

    // Alter the table to add new columns if they do not exist
    $alterTableQuery = "ALTER TABLE $newTableName 
                        ADD COLUMN IF NOT EXISTS gmail VARCHAR(255),
                        ADD COLUMN IF NOT EXISTS phone VARCHAR(20)";
    
    if (!mysqli_query($conn, $alterTableQuery)) {
        echo "Error updating table: " . mysqli_error($conn);
        exit;
    }

    // Insert the new book into the new table
    $insertQuery = "INSERT INTO $newTableName (isbn, type, title, author, image, descr, price, publisher, category, gmail, phone) 
                    VALUES ('$isbn', '$type', '$bookTitle', '$author', '$image', '$descr', '$price', '$publisher', '$category', '$gmail', '$phone')";

    $result = mysqli_query($conn, $insertQuery);
    if (!$result) {
        echo "Can't add new data: " . mysqli_error($conn);
        exit;
    } else {
        echo '<div class="alert alert-success" role="alert">New book added successfully to the table!</div>';
        header("Location: admin_book.php"); // Redirect to another page after successful submission
    }
}
?>

<form method="post" action="" enctype="multipart/form-data">
    <table class="table">
        <!-- Form fields for book details -->
        <tr>
            <th>ISBN</th>
            <td><input type="text" name="isbn" required></td>
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <select name="type" id="TextBox1" onchange="updatePrice()">
                    <option value="Donating">Donating</option>
                    <option value="Resale">Resale</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Title</th>
            <td><input type="text" name="title" required></td>
        </tr>
        <tr>
            <th>Author</th>
            <td><input type="text" name="author" required></td>
        </tr>
        <tr>
            <th>Image</th>
            <td><input type="file" name="image"></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><textarea name="descr" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <th>Price</th>
            <td><input id="TextBox4" type="text" name="price" required></td>
        </tr>
        <tr>
            <th>Publisher</th>
            <td><input type="text" name="publisher" required></td>
        </tr>
        <tr>
            <th>Category</th>
            <td><input type="text" name="category" required></td>
        </tr>
        <tr>
            <th>Gmail</th>
            <td><input type="email" name="gmail" required></td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td><input type="tel" name="phone" required></td>
        </tr>
    </table>
    <input type="submit" name="add" value="Add new book" class="btn btn-primary">
    <input type="reset" value="Cancel" class="btn btn-default">
</form>
<br/>
<?php
require_once "./template/footer.php";
?>

<script>
    function updatePrice() {
        var type = document.getElementById("TextBox1").value;
        document.getElementById("TextBox4").value = type == 'Donating' ? 0 : '';
    }
</script>
