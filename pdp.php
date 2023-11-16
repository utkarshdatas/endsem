<?php
session_start();

$message = '';
$formSubmitted = false;
$cacheNumber = rand(10000, 99999);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $name = $_POST["name"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"]; // Added line for confirm password
    $rollNo = $_POST["rollNo"];
    $address = $_POST["address"];
    $gender = $_POST["gender"];
    $cache = $_POST["cache"];
    $email = $_POST["email"];
    $phoneNumber = $_POST["phoneNumber"];

    if ($password != $confirmPassword) { // Added line for confirm password validation
        $message = "Error: Password and Confirm Password do not match.";
    } elseif ($cache != $_SESSION["cacheNumber"]) {
        $message = "Error: Incorrect cache number.";
    } elseif (isset($_SESSION["usernames"][$username])) {
        $message = "Error: Username has already been used.";
        header('Content-Type: application/json');
        echo json_encode($_SESSION["usernames"][$username]);
        exit;
    } else {
        $_SESSION["usernames"][$username] = true;
        $message = "Form submitted successfully.<br>";
        $message .= "Username: " . $username . "<br>";
        $message .= "name: " . $name . "<br>";
        $message .= "Roll No: " . $rollNo . "<br>";
        $message .= "Address: " . $address . "<br>";
        $message .= "Gender: " . $gender . "<br>";
        $message .= "Cache: " . $cache . "<br>";
        $formSubmitted = true;

        $_SESSION["usernames"][$username] = [
            'username' => $username,
            'name' => $name,
            'address' => $address,
            'Roll No' => $rollNo
        ];
        file_put_contents('users.json', json_encode($_SESSION["usernames"], JSON_PRETTY_PRINT));
    }
}

$_SESSION["cacheNumber"] = $cacheNumber;

// Check if the username exists asynchronously
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["checkUsername"])) {
    $checkUsername = $_GET["checkUsername"];

    if (isset($_SESSION["usernames"][$checkUsername])) {
        header('Content-Type: application/json');
        echo json_encode($_SESSION["usernames"][$checkUsername]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <script>
        async function checkUsernameExists(username) {
            try {
                const response = await fetch(`your_php_file.php?checkUsername=${encodeURIComponent(username)}`);
                const userDetails = await response.json();

                if (userDetails.hasOwnProperty('username')) {
                    console.log("User already exists:", userDetails);
                } else {
                    console.log("Username is available.");
                }
            } catch (error) {
                console.error("Error checking username:", error);
            }
        }

        function validateForm() {
            var username = document.forms["myForm"]["username"].value;
            var name = document.forms["myForm"]["name"].value;
            var password = document.forms["myForm"]["password"].value;
            var confirmPassword = document.forms["myForm"]["confirmPassword"].value; // Added line for confirm password
            var rollNo = document.forms["myForm"]["rollNo"].value;
            var address = document.forms["myForm"]["address"].value;
            var gender = document.forms["myForm"]["gender"].value;
            var cache = document.forms["myForm"]["cache"].value;
            var email = document.forms["myForm"]["email"].value;
            var phoneNumber = document.forms["myForm"]["phoneNumber"].value;

            if (username == "" || name == "" || password == "" || confirmPassword == "" || rollNo == "" || address == "" || gender == "" || cache == "" || email == "" || phoneNumber == "") {
                alert("All fields must be filled out");
                return false;
            }

            var passwordRegex = /^(?=.*\d.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if (!passwordRegex.test(password)) {
                alert("Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit");
                return false;
            }

            if (password !== confirmPassword) { // Added line for confirm password validation
                alert("Password and Confirm Password do not match.");
                return false;
            }

            var emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
            if (!emailRegex.test(email)) {
                alert("Email must contain '@' and a domain name");
                return false;
            }

            var phoneNumberRegex = /^[960]\d{9}$/;
            if (!phoneNumberRegex.test(phoneNumber)) {
                alert("Phone number must start with 9, 6, or 0 and be 10 digits long");
                return false;
            }

            if (username.length != 10) {
                alert("Username must be at least 10 characters long and contain no special characters or digits");
                return false;
            }

            var nameRegex = /^[A-Za-z]{10,}$/;
            if (!nameRegex.test(name)) {
                alert("Name must contain only alphabets and be at least 10 letters long");
                return false;
            }

            checkUsernameExists(username);

            return true;
        }
    </script>
</head>
<body>

<?php if (!$formSubmitted): ?>
<form name="myForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return validateForm()" method="post">
    Username: <input type="text" name="username"><br>
    name: <input type="text" name="name"><br>
    Password: <input type="password" name="password"><br>
    Confirm Password: <input type="password" name="confirmPassword"><br> <!-- Added line for confirm password -->
    Roll No: <input type="text" name="rollNo"><br>
    Address: <input type="text" name="address"><br>
    Gender:
    <select name="gender">
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="neutral">Neutral</option>
        <option value="none">None</option>
    </select><br>
    Email: <input type="text" name="email"><br>
    Phone Number: <input type="text" name="phoneNumber"><br>
    Cache: <input type="number" name="cache"><br>
    <input type="submit">
</form>
<?php endif; ?>

<div>
    <?php echo $message; ?>
</div>

<p>Your cache number is: <?php echo $cacheNumber; ?></p>

<?php if (isset($_SESSION["usernames"]) && is_array($_SESSION["usernames"])): ?>
    <h2>Logged in users:</h2>
    <ul>
        <?php foreach ($_SESSION["usernames"] as $username => $value): ?>
            <li><?php echo $username; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>
