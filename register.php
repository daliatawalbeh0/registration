<?php
session_start();

require 'config2.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    // $mobile = $_POST['mobile'];
    $name = $_POST['name']; // Assuming 'name' corresponds to first, middle, last, and family names
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    // // File upload handling
    // $image = $_FILES['image']['name'];
    // $image_temp = $_FILES['image']['tmp_name'];
    // $image_path = "uploads/" . $image; // Example path where uploaded files are stored

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // if (!preg_match('/^\d{10}$/', $mobile)) {
    //     $errors['mobile'] = "Mobile number must be 10 digits.";
    // }

    if (empty($name)) {
        $errors['name'] = "Full name is required.";
    } else {
        // Validate if name contains at least 4 parts
        $name_parts = explode(' ', $name);
        if (count($name_parts) < 4) {
            $errors['name'] = "Full name must contain at least 4 parts.";
        }
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $errors['password'] = "Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.";
    }

    if ($password !== $password_confirmation) {
        $errors['password_confirmation'] = "Passwords do not match.";
    }

    // Check if file was uploaded and move it to designated directory
    // if (empty($image)) {
    //     $errors['image'] = "Please upload an image.";
    // } else {
    //     $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    //     $file_extension = pathinfo($image, PATHINFO_EXTENSION);

    //     if (!in_array(strtolower($file_extension), $allowed_extensions)) {
    //         $errors['image'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    //     } else {
    //         move_uploaded_file($image_temp, $image_path);
    //     }
    // }

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute SQL insert statement
        $sql = "INSERT INTO usersss(name, email, password) VALUES('$name', '$email','$hashed_password')";
        $conn->query($sql);
        if ($conn) {
            echo "<div class='alert alert-success'>Registration successful.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
        $conn->close();
        header("Location: login.php");
    } else {
        $_SESSION['errors'] = $errors;
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error {
            color: red;
            font-size: 0.875em;
        }
    </style>
    <script>
        function validateForm() {
            var email = document.forms["signupForm"]["email"].value;
            // var mobile = document.forms["signupForm"]["mobile"].value;
            var name = document.forms["signupForm"]["name"].value;
            var password = document.forms["signupForm"]["password"].value;
            var password_confirmation = document.forms["signupForm"]["password_confirmation"].value;
            // var image = document.forms["signupForm"]["image"].value;

            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var mobilePattern = /^\d{10}$/;
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            var isValid = true;

            if (!emailPattern.test(email)) {
                document.getElementById('emailError').innerText = "Invalid email format.";
                isValid = false;
            } else {
                document.getElementById('emailError').innerText = "";
            }
            // if (!mobilePattern.test(mobile)) {
            //     document.getElementById('mobileError').innerText = "Mobile number must be 10 digits.";
            //     isValid = false;
            // } else {
            //     document.getElementById('mobileError').innerText = "";
            // }
            if (!name) {
                document.getElementById('nameError').innerText = "Full name is required.";
                isValid = false;
            } else {
                document.getElementById('nameError').innerText = "";
            }
            if (!passwordPattern.test(password)) {
                document.getElementById('passwordError').innerText = "Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.";
                isValid = false;
            } else {
                document.getElementById('passwordError').innerText = "";
            }
            if (password !== password_confirmation) {
                document.getElementById('passwordConfirmationError').innerText = "Passwords do not match.";
                isValid = false;
            } else {
                document.getElementById('passwordConfirmationError').innerText = "";
            }
            // if (!image) {
            //     document.getElementById('imageError').innerText = "Please upload an image.";
            //     isValid = false;
            // } else {
            //     document.getElementById('imageError').innerText = "";
            // }

            return isValid;
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Sign Up</h2>
        <form name="signupForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <small id="emailError" class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></small>
            </div>
            <!-- <div class="form-group">
                <label for="mobile">Mobile:</label>
                <input type="text" class="form-control" id="mobile" name="mobile" required>
                <small id="mobileError" class="error"><?php echo isset($errors['mobile']) ? $errors['mobile'] : ''; ?></small>
            </div> -->
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <small id="nameError" class="error"><?php echo isset($errors['name']) ? $errors['name'] : ''; ?></small>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small id="passwordError" class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></small>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                <small id="passwordConfirmationError" class="error"><?php echo isset($errors['password_confirmation']) ? $errors['password_confirmation'] : ''; ?></small>
            </div>
            <!-- <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                <small id="imageError" class="error">
                 
                </small> -->
            </div> 
            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>
    </div>
</body>
</html>
