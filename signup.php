<?php
	$title = "User SignUp";
	require_once "./template/header.php";
?>
<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        display: flex; /* Use Flexbox for centering */
        justify-content: center; /* Horizontally center the content */
        align-items: center; /* Vertically center the content */
        height: 100vh; /* Full viewport height to center vertically */
        margin: 0; /* Remove any default margin */
    }

    .form-horizontal {
        max-width: 500px;
        margin: auto;
        width: 100%;
        padding: 20px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        height: 75vh;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .btn {
        width: 100%;
    }

    .error-message {
        color: red;
        text-align: center;
        margin-top: 15px;
    }
</style>

<form class="form-horizontal" method="post" action="user_signup.php" name="signupForm" onsubmit="return validateForm()">
    <div class="form-group">
        <label for="exampleInputEmail1">Firstname</label>
        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Firstname" name="firstname">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Lastname</label>
        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Lastname" name="lastname">
    </div>
    <div class="form-group">
        <label for="inputEmail4">Email</label>
        <input type="text" class="form-control" id="inputEmail4" placeholder="Email" name="email">
    </div>
    <div class="form-group">
        <label for="inputPassword4">Password</label>
        <input type="password" class="form-control" id="inputPassword4" placeholder="Password" name="password">
    </div>
    <div class="form-group">
        <label for="inputAddress">Address</label>
        <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St" name="address">
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputCity">City</label>
            <input type="text" class="form-control" id="inputCity" name="city">
        </div>
        <div class="form-group col-md-4">
            <label for="inputZip">Zip</label>
            <input type="text" class="form-control" id="inputZip" name="zipcode">
        </div>
    </div>
    <div class="form-group col-md-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>



<?php
    $fullurl="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(strpos($fullurl,"signup=empty")==true){
        echo '<P style="color:red">You did not fill in all the fields.</P>';
        exit();
    }
    if(strpos($fullurl,"signup=invalidemail")==true){
        echo '<P style="color:red">You did not enter a valid email address.</P>';
        exit();
    }
    
?>
</div>
<script>
    function validateForm() {
        // Get form fields
        const firstname = document.forms["signupForm"]["firstname"].value;
        const lastname = document.forms["signupForm"]["lastname"].value;
        const email = document.forms["signupForm"]["email"].value;
        const password = document.forms["signupForm"]["password"].value;
        const address = document.forms["signupForm"]["address"].value;
        const city = document.forms["signupForm"]["city"].value;
        const zipcode = document.forms["signupForm"]["zipcode"].value;

        // Check if any field is empty
        if (firstname === "" || lastname === "" || email === "" || password === "" || address === "" || city === "" || zipcode === "") {
            alert("All fields must be filled out.");
            return false; // Prevent form submission
        }
        
        // Email validation
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!email.match(emailPattern)) {
            alert("Please enter a valid email address.");
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }
</script>

<?php
	
?>