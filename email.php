<?php
session_start();
require_once('SMTP.php');
require_once('PHPMailer.php');
require_once('Exception.php');
require_once('./functions/database_functions.php'); // Include your database functions

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->SMTPDebug = 0; // Disable verbose debug output in production
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'bookland143@gmail.com';
    $mail->Password = 'wtmsdjpjcglkgybt';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('bookland143@gmail.com', 'BookBazaar');

    // Retrieve the email of the user from the session
    if (isset($_SESSION['email'])) {
        $recipientEmail = $_SESSION['email'];
    } else {
        $recipientEmail = 'default@gmail.com'; // Fallback if session email is not set
    }

    $mail->addAddress($recipientEmail, 'Customer');

    // Prepare email content
    $mail->isHTML(true);
    $mail->Subject = 'Order Confirmation and Purchase History';

    // Retrieve purchase history for the logged-in user
    $conn = db_connect();
    $customer = getCustomerIdbyEmail($recipientEmail);
    $customerid = $customer['id'];


    $query = "
        SELECT books.book_title, books.book_author, books.book_price, books.book_image, cartitems.quantity 
        FROM cart
        JOIN cartitems ON cart.id = cartitems.cartid
        JOIN books ON cartitems.productid = books.book_isbn
        WHERE cart.customerid = '$customerid'

        
    ";

    $result = mysqli_query($conn, $query);

    // Generate the email body
    $mail->Body = '<h3>Hi, Your order has been successfully placed!</h3>';
    $mail->Body .= '<h4>Here is your purchase history:</h4>';

    if (mysqli_num_rows($result) > 0) {
        $mail->Body .= '<table border="1" cellpadding="5" cellspacing="0">';
        $mail->Body .= '<tr>
                            <th>Book</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            
                        </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            $mail->Body .= '<tr>
                                <td>' . $row['book_title'] . '</td>
                                <td>' . $row['book_author'] . '</td>
                                <td>' . $row['book_price'] . '</td>
                                <td>' . $row['quantity'] . '</td>
                               
                            </tr>';
        }
        $mail->Body .= '</table>';
        $mail->Body .= '<h3>please fill confirm the order details below the Link</h3>';
        $mail->Body .= '<a href="https://docs.google.com/forms/d/e/1FAIpQLSfKRbVyQb44vjeyZu-AwKryemGT2m4gDyd5MpT6mcVln571Rg/viewform?usp=header">LINK IS HERE</a>';
    } else {
        $mail->Body .= '<p>No purchase history available.</p>';
    }

    // Send the email
    $mail->send();

    // Close the database connection
    if (isset($conn)) {
        mysqli_close($conn);
    }

    // Redirect after sending the email
    header('location: index.php');
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}
?>
