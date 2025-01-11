<?php
	session_start();
	require_once "./functions/database_functions.php";
	$conn = db_connect();

	$name = trim($_POST['username']);
	$pass = trim($_POST['password']);
	
require_once('SMTP.php');
require_once('PHPMailer.php');
require_once('Exception.php');

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

$mail=new PHPMailer(true); // Passing `true` enables exceptions

try {
    //settings
    $mail->SMTPDebug=2; // Enable verbose debug output
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host='smtp.gmail.com';
    $mail->SMTPAuth=true; // Enable SMTP authentication	
	$mail->Username='bookland143@gmail.com'; // SMTP username
    $mail->Password='wtmsdjpjcglkgybt'; // SMTP password
    $mail->SMTPSecure='ssl';
    $mail->Port=465;
    $mail->setFrom('bookland143@gmail.com', 'BookBazaar');


    //recipient
    $mail->addAddress($name,'You');     // Add a recipient

    //content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject='Login Alert';
    $mail->Body='Hi , Welcome to BOOKBAZAAR ,You sucessfully Loggined BookBazzar';
    $mail->AltBody='This is the body in plain text for non-HTML mail clients';

    $mail->send();

    header('location: index.php');
} 
catch(Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: '.$mail->ErrorInfo;
}






	
	

	if(empty($name) || empty($pass)){
		header("Location:../onlinebookstore/signin.php?signin=empty");
	}else{ 
				$query = "SELECT name,pass from manager";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_assoc($result);
				 if($name == $row['name'] && $pass == $row['pass'] ){
					$_SESSION['manager'] = true;
					$_SESSION['username'] = $name; 
					unset($_SESSION['expert']);
					unset($_SESSION['user']);
					unset($_SESSION['email']);
				
					header("Location: admin_book.php");
				}
				else{
					//check if it is expert
					$query = "SELECT name,pass from expert";
					$result = mysqli_query($conn, $query);
					$row = mysqli_fetch_assoc($result);
					if($name == $row['name'] && $pass == $row['pass'] ){
						$_SESSION['expert'] = true;
						$_SESSION['username'] = $name;
						unset($_SESSION['manager']);
						unset($_SESSION['user']);
						unset($_SESSION['email']);
						header("Location: admin_book.php");
					}
				else{
						//check if it is customer
						$name = mysqli_real_escape_string($conn, $name);
						$pass = mysqli_real_escape_string($conn, $pass);

						$query = "SELECT email,password from customers";
						$result = mysqli_query($conn, $query);
						for($i = 0; $i < mysqli_num_rows($result); $i++){
							$row = mysqli_fetch_assoc($result);
							if($name == $row['email'] && $pass == $row['password'] ){ 
								$_SESSION['user'] = true;	
								$_SESSION['username'] = $name;
								$_SESSION['email'] = $name;
								unset($_SESSION['manager']);
								unset($_SESSION['expert']);
								header("Location: email.php");
								header("Location: index.php");
							}

						}
					}
				}
			}
	

	if(isset($conn)) {mysqli_close($conn);}
	
?>