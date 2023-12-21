<?php 
    require_once("DBConnection.php");
?>

<?php



    function encryption ($password){
        $BlowFishFormate = "$2y$10$";
        $salt = generateSalt(22);
        $BlowFish_Plus_Salt = $BlowFishFormate . $salt;
        $Hash = crypt($password, $BlowFish_Plus_Salt);

        return $Hash;
    }

    function generateSalt($length){
        $uniqueRandomString = md5(uniqid(mt_rand(), true));
        $base64String = base64_encode($uniqueRandomString);
        $modifiedBase64String = str_replace('+','.',$base64String);
        $salt = substr($modifiedBase64String,0,$length);

        return $salt;
    }

    function passwordCheck($password, $existingHash){
        $Hash = crypt($password, $existingHash);
        if($Hash === $existingHash)
            return true;
        else
            return false;
    }

    function login($username, $password, $conn) {
        // Create a prepared statement
        $stmt = mysqli_stmt_init($conn);
    
        if (mysqli_stmt_prepare($stmt, "SELECT username, password, type, id FROM users WHERE username = ?")) {
            // Bind the username parameter
            mysqli_stmt_bind_param($stmt, "s", $username);
    
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Bind the result variables
                mysqli_stmt_bind_result($stmt, $dbusername, $dbpassword, $type, $id);
    
                // Fetch the result
                mysqli_stmt_fetch($stmt);
    
                if ($username == $dbusername && passwordCheck($password, $dbpassword)) {
                    $_SESSION['sess_user'] = $username;
                    $_SESSION['sess_eid'] = $id;
    
                    if ($type == "admin") {
                        header("Location:admin.php");
                    } else {
                        header("Location: sample.html");
                    }
                    exit;
                } else {
                    // Invalid Username or Password
                    return false;
                }
            } else {
                // Query execution failed
                echo "Query execution error: " . mysqli_stmt_error($stmt);
            }
            
            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // Prepared statement creation failed
            echo "Prepared statement error: " . mysqli_error($conn);
        }
    
        return false; // Return false by default for any error case
    }
    

    
    function signup($fullname, $username, $email, $password, $phone, $repassword, $gender, $city, $dept, $type, $conn) {
        $hashedPassword = encryption($password);
    
        // Create and execute the query
        $query = "INSERT INTO users (fullname, username, email, phone, password, gender, city, department, type) 
                  VALUES ('$fullname', '$username', '$email', '$phone', '$hashedPassword', '$gender', '$city', '$dept', '$type')";
    
        $result = mysqli_query($conn, $query);
    
        if ($result) {
            // The INSERT query was successful, now fetch the id
            $query1 = "SELECT id FROM users WHERE username='$username'";
            $result1 = mysqli_query($conn, $query1);
    
            if ($result1) {
                $eid = mysqli_fetch_assoc($result1);
                if ($eid) {
                    echo 'Registration successful!';
                    $_SESSION['sess_user'] = $username;
                    $_SESSION['sess_eid'] = $eid['id'];
                    header("Location: index.php");
                    exit;
                } else {
                    echo 'Failed to fetch the user ID.';
                }
            } else {
                echo 'Failed to fetch the user ID: ' . mysqli_error($conn);
            }
        } else {
            echo 'Query Error: ' . $query . '<br>' . mysqli_error($conn);
        }
    }
    

?>