<?php 
require_once("DBConnection.php");
include("functions.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <title>Sign Up</title>
  <style>
    /* Style for the body */
body {
    background-color: #f4f4f4;
    font-family: 'Source Sans Pro', sans-serif;
}

/* Style for the registration form container */
.container {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    margin: 20px auto;
    padding: 20px;
    max-width: 600px;
}

/* Style for the form header */
h1 {
    text-align: center;
    font-size: 2.5em;
    font-weight: bold;
    padding-top: 1.2em;
}

/* Style for form labels */
label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}

/* Style for form input fields */
input[type="text"],
input[type="password"],
input[type="email"],
input[type="tel"],
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    outline: none;
    margin-bottom: 10px;
}

/* Style for form radio buttons */
input[type="radio"] {
    margin: 10px;
}

/* Style for form submit button */
.btn-success {
    background-color: #22cb5c;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
    font-size: 1.2em;
}

.btn-success:hover {
    background-color: #1ea847;
}

/* Style for error messages */
.error {
    color: #FF0000;
    font-weight: bold;
}

/* Style for the footer */
.footer {
    background: #404040 !important;
    color: white;
    text-align: center;
    padding: 10px 0;
}

/* Style for the navigation bar */
.navbar {
    background-color: #22cb5c;
    color: white;
}

.navbar a {
    color: white;
    text-decoration: none;
    margin-right: 20px;
}

/* Style for the form container background */
.form-container {
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    margin: 20px auto;
}

/* Style for the department select dropdown */
.form-container select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    outline: none;
    margin-bottom: 10px;
}

/* Additional styles for improved readability and aesthetics can be added as needed */

    </style>
  

</head>

<body>
<!-- php code -->
  <?php
  $nameErr = $emailErr = $phoneErr = $passwordErr = $repasswordErr = $genderErr = "";
  $fullname = $username = $email = $phone = $password = $repassword = $gender = "";
  global $validate;

  if(isset($_POST['submit'])){

    if(empty($_POST['fullname'])){
      $fullnameErr = "Please Enter Fullname";
      $validate = false;
    }
    else{
      $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
      $validate = true;
    }

    if(empty($_POST['username'])){
      $nameErr = "Please Enter Username";
      $validate = false;
    }
    else{
      $username = mysqli_real_escape_string($conn,$_POST['username']);
      $validate = true;
    }

    if(empty($_POST['email'])){
      $emailErr = "Please Enter Email";
      $validate = false;
    }
    else{
      $email = mysqli_real_escape_string($conn,$_POST['email']);
      $validate = true;
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $emailErr = "Please Enter valid email";
        $validate = false;
      }
    }

    if(empty($_POST['phone'])){
      $phoneErr = "Please Enter Phone Number";
      $validate = false;
    }
    else{
      $phone = mysqli_real_escape_string($conn,$_POST['phone']);
      $validate = true;
      if(strlen($phone) > 10 || strlen($phone) < 10 || !preg_match("/[0-9]/",$phone)){
        $phoneErr = "Please Enter valid Phone Number";
        $validate = false;
      }
    }

    if(empty($_POST['password'])){
      $passwordErr = "Please Enter Password";
      $validate = false;
    }
    else{
      $password = mysqli_real_escape_string($conn,$_POST['password']);
      $validate = true;
    }

    if(empty($_POST['repassword'])){
      $repasswordErr = "Please Enter re-password";
      $validate = false;
    }
    else{
      $repassword = mysqli_real_escape_string($conn,$_POST['repassword']);
      $validate = true;
      if($password !== $repassword){
        $repasswordErr = "Password and Confirm Password don't match";
        $validate = false;
      }
    }

    if(empty($_POST['gender'])){
      $genderErr = "Please Select Gender";
      $validate = false;
    }
    else{
      $gender = mysqli_real_escape_string($conn,$_POST['gender']);
      $validate = true;
    }

    $city = $_POST['city'];
    $dept = $_POST['Department'];
    $type = 'employee';
  
 
    if($validate){
      signup($fullname,$username,$email,$password,$phone,$repassword,$gender,$city,$dept,$type,$conn);
    }
  }

ini_set('display_errors', true);
error_reporting(E_ALL);
  ?>


  <!-- navbar -->
  <nav class="navbar header-nav navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Department Of Computer Applications Online Leave Application System </a>

      <a id="register" href="index.php">Home</a>
    </div>
  </nav>

  <h1>Registration Form</h1>

  <div class="container">
    <div class="alert alert-danger" id="err" role="alert">
    </div>
  
    <!--form-->
    <form method="POST" autocomplete="off">
  
      <!--Name-->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $fullname; ?>"placeholder="Fullname">
        <label for="Fullname">Fullname</label>
        <span class="error"><?php echo $nameErr; ?></span>
      </div>
  
      <!--username-->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>"placeholder="Username">
        <label for="username">Username</label>
        <span class="error"><?php echo $nameErr; ?></span>
      </div>
  
      <!--Email id-->
      <div class="form-floating mb-3">
        <input class="form-control" type="text" name="email" id="email" value="<?php echo $email; ?>" placeholder="Enter your email">
        <label for="email">Email address</label>
        <span class="error"><?php echo $emailErr; ?></span>
      </div>
  
      <!--Phone No.-->
      <div class="form-floating mb-3">
        <input class="form-control" type="tel" name="phone" id="phone" value="<?php echo $phone; ?>" placeholder="Enter your Phone no.">
        <label for="phone">Phone No.</label>
        <span class="error"><?php echo $phoneErr; ?></span>
      </div>
  
      <!--Password.-->
      <div class="form-floating mb-3">
        <input class="form-control" type="password" name="password" id="password" value="<?php echo $password; ?>" placeholder="Enter your password">
        <label for="password">Password</label>
        <span class="error"><?php echo $passwordErr; ?></span>
      </div>
  
      <!--Confirm Password.-->
      <div class="form-floating mb-3">
        <input class="form-control" type="password" name="repassword" id="confirmPassword" value="<?php echo $repassword ?>" placeholder="Re-Enter password">
        <label for="confirmPassword">Confirm Password</label>
        <span class="error"><?php echo $repasswordErr; ?></span>
      </div>
  
      <label for="gender">Gender:</label>
      <input type="radio" id="gender" name="gender" <?php if(isset($gender)&&$gender=="Male") echo "checked" ?> value="Male">Male
      <input type="radio" id="gender" name="gender" <?php if(isset($gender)&&$gender=="Female") echo "checked" ?> value="Female">Female
      
      <span class="error"><?php echo $genderErr; ?></span>
      <br>
  
      <div class="row">
  
      
  
      <div class="col-5">
      <label>Department : </label>
      <select name="Department">
        <option>MCA</option>
        <option>M.SC CYBER SECURITY</option>
        <option>M.SC DATA ANALYTICS</option>
       
      </select>
      </div>
  
      </div>
  
      
  
      <br>
  
      <input type="submit" name="submit" value="Submit" class="btn btn-success">
    </form>
  </div>



  


</body>

</html>