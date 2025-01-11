<?php
session_start();
require_once "./functions/database_functions.php";
require_once "./functions/cart_functions.php";

$conn = db_connect();

// Create purchase_history table if it doesn't exist
$query_create_table = "
CREATE TABLE IF NOT EXISTS purchase_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customerid INT NOT NULL,
    book_isbn VARCHAR(255) NOT NULL,
    book_title VARCHAR(255),
    book_author VARCHAR(255),
    book_price DECIMAL(10, 2),
    quantity INT,
    purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $query_create_table);

// Adding books to cart
if (isset($_POST['bookisbn'])) {
    $book_isbn = $_POST['bookisbn'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['total_items'] = 0;
        $_SESSION['total_price'] = '0.00';
    }
    if (!isset($_SESSION['cart'][$book_isbn])) {
        $_SESSION['cart'][$book_isbn] = 1;
    } else {
        $_SESSION['cart'][$book_isbn]++;
    }

    // Save to `mycart` table (optional, if you need persistent cart)
    $query = "SELECT * FROM books WHERE book_isbn = '$book_isbn'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $book_title = $row['book_title'];
        $book_author = $row['book_author'];
        $book_price = $row['book_price'];
        $book_image = $row['book_image'];

        $query_check = "SELECT * FROM mycart WHERE book_isbn = '$book_isbn'";
        $result_check = mysqli_query($conn, $query_check);

        if (mysqli_num_rows($result_check) > 0) {
            $query_update = "UPDATE mycart SET quantity = quantity + 1 WHERE book_isbn = '$book_isbn'";
            mysqli_query($conn, $query_update);
        } else {
            $query_insert = "INSERT INTO mycart (book_isbn, book_title, book_author, book_price, book_image, quantity) 
                             VALUES ('$book_isbn', '$book_title', '$book_author', '$book_price', '$book_image', 1)";
            mysqli_query($conn, $query_insert);
        }
    }
}

// Checkout and save purchase history
if (isset($_POST['checkout']) && isset($_SESSION['user'])) {
    $customer = getCustomerIdbyEmail($_SESSION['email']);
    $customerid = $customer['id'];

    $query_cart = "
        SELECT cartitems.quantity, books.book_isbn, books.book_title, books.book_author, books.book_price 
        FROM cart 
        JOIN cartitems ON cart.id = cartitems.cartid 
        JOIN books ON cartitems.productid = books.book_isbn 
        WHERE cart.customerid = '$customerid'
    ";
    $result_cart = mysqli_query($conn, $query_cart);

    if (mysqli_num_rows($result_cart) > 0) {
        while ($row = mysqli_fetch_assoc($result_cart)) {
            $query_insert_history = "
                INSERT INTO purchase_history (customerid, book_isbn, book_title, book_author, book_price, quantity) 
                VALUES ('$customerid', '{$row['book_isbn']}', '{$row['book_title']}', '{$row['book_author']}', '{$row['book_price']}', '{$row['quantity']}')
            ";
            mysqli_query($conn, $query_insert_history);
        }
        // Clear the cart
        $query_delete_cartitems = "DELETE FROM cartitems WHERE cartid IN (SELECT id FROM cart WHERE customerid = '$customerid')";
        $query_delete_cart = "DELETE FROM cart WHERE customerid = '$customerid'";
        mysqli_query($conn, $query_delete_cartitems);
        mysqli_query($conn, $query_delete_cart);

        echo "<p class='text-success'>Your order has been placed successfully!</p>";
    }
}

// Display cart items
$title = "Your Shopping Cart";
require "./template/header.php";
if (isset($_SESSION['cart']) && array_count_values($_SESSION['cart'])) {
    $_SESSION['total_price'] = total_price($_SESSION['cart']);
    $_SESSION['total_items'] = total_items($_SESSION['cart']);
    ?>
    <form method="post" action="cart.php">
        <table class="table">
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <?php
            foreach ($_SESSION['cart'] as $isbn => $qty) {
                $book = mysqli_fetch_assoc(getBookByIsbn($conn, $isbn));
                ?>
                <tr>
                    <td><?php echo $book['book_title'] . " by " . $book['book_author']; ?></td>
                    <td><?php echo $book['book_price']; ?></td>
                    <td><?php echo $qty; ?></td>
                    <td><?php echo $qty * $book['book_price']; ?></td>
                </tr>
            <?php } ?>
            <tr>
                <th colspan="2"></th>
                <th><?php echo $_SESSION['total_items']; ?></th>
                <th><?php echo $_SESSION['total_price']; ?></th>
            </tr>
        </table>
        <button type="submit" name="checkout" class="btn btn-success"><a href="checkout.php">Checkout</a></button>
    </form>
    <a href="books.php" class="btn btn-primary">Continue Shopping</a>
    <?php
} else {
    echo "<p class='text-warning'>Your cart is empty!</p>";
}

// Display purchase history
if (isset($_SESSION['user'])) {
    $customer = getCustomerIdbyEmail($_SESSION['email']);
    $customerid = $customer['id'];

    $query_history = "
        SELECT * FROM purchase_history WHERE customerid = '$customerid' ORDER BY purchase_date DESC
    ";
    $result_history = mysqli_query($conn, $query_history);

    if (mysqli_num_rows($result_history) > 0) {
        echo "<h4>Your Purchase History</h4>";
        echo "<table class='table'>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                   
                </tr>";
        while ($row = mysqli_fetch_assoc($result_history)) {
            echo "<tr>
                    <td>{$row['book_title']} by {$row['book_author']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['book_price']}</td>
                    
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No purchase history available.</p>";
    }
}
?>
<?php if (isset($conn)) mysqli_close($conn); ?>
