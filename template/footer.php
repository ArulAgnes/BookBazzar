<?php
require "./functions/database_functions.php";

// Connect to the database
$conn = db_connect();

// Query to get books with type "Donating"
$query = "SELECT title, author, publisher, category FROM dynamic_books_table WHERE type = 'Donating'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error retrieving data: " . mysqli_error($conn);
    exit;
}

// Start collecting the books' details for display
$bookList = "";
while ($book = mysqli_fetch_assoc($result)) {
    $bookList .= '<div class="book-entry">';
    $bookList .= '<strong>Title:</strong> ' . htmlspecialchars($book['title']) . ' <span>&rarr;</span> ';
    $bookList .= '<strong>Author:</strong> ' . htmlspecialchars($book['author']) . ' <span>&rarr;</span> ';
    $bookList .= '<strong>Publisher:</strong> ' . htmlspecialchars($book['publisher']) . ' <span>&rarr;</span> ';
    $bookList .= '<strong>Category:</strong> ' . htmlspecialchars($book['category']);
    $bookList .= '</div><br>'; // Adding a line break for spacing
}

if (isset($conn)) {
    mysqli_close($conn); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Footer with Book List</title>
    <style>
        /* CSS Styles for the footer with structured book list */
        .footer-book-list {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            text-align: left;
            border-top: 3px solid #3498db;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .footer-book-list h4 {
            margin-bottom: 15px; /* Space below the heading */
            font-size: 20px;
            color: #ecf0f1; /* Light color for the heading */
        }
        .book-entry {
            margin-bottom: 10px; /* Space between book entries */
            line-height: 1.5; /* Improved line height for readability */
        }
        .book-entry span {
            color: #3498db; /* Color for the arrows */
            margin: 0 5px; /* Spacing around arrows */
        }
    </style>
</head>
<body>
    <div class="footer-book-list">
        <h4>Donating Books (If You Want to Get a Donating Books contact Agram Charity [ Number : +91 1234567890 ])</h4>
        <?php echo $bookList; ?>
    </div>
</body>
</html>
