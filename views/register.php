<?php
session_start();
require '../config/config.php';

$twilio_sid = 'AC63bdc7ff1d488743897df6eff491f338';
$twilio_token = '49732fc91cd13f54292138f4e17aedc4';
$twilio_phone_number = '+18283445828';

// Function to validate and format phone number to E.164
function formatPhoneNumber($phone) {
    // Remove any non-digit characters
    $phone = preg_replace('/\D+/', '', $phone);

    // Check if the phone number is already in E.164 format
    if (preg_match('/^\+?\d{11,15}$/', $phone)) {
        // If it starts with a plus sign, it's already in E.164 format
        if (strpos($phone, '+') === 0) {
            return $phone;
        }
        // Otherwise, add the country code (assuming US as default country code)
        return '+88' . $phone;
    }

    return false;
}

function sendVerificationCode($phone, $code, $twilio_sid, $twilio_token, $twilio_phone_number) {
    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $twilio_sid . '/Messages.json';
    $data = [
        'From' => $twilio_phone_number,
        'To' => $phone,
        'Body' => "Your verification code is: $code"
    ];

    $post = http_build_query($data);
    $x = curl_init($url);
    curl_setopt($x, CURLOPT_POST, true);
    curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($x, CURLOPT_USERPWD, $twilio_sid . ':' . $twilio_token);
    curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($x, CURLOPT_POSTFIELDS, $post);
    curl_exec($x);
    curl_close($x);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_code'])) {
        $phone = $_SESSION['phone'];
        $code = $_POST['verify_code'];

        // Check the code
        $stmt = $conn->prepare("SELECT * FROM phone_verifications WHERE phone = ? AND code = ? AND verified = 0");
        $stmt->bind_param("ss", $phone, $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $verification = $result->fetch_assoc();
        $stmt->close();

        if ($verification) {
            $stmt = $conn->prepare("UPDATE phone_verifications SET verified = 1 WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $stmt->close();

            // Complete the registration
            $first_name = $_SESSION['first_name'];
            $surname = $_SESSION['surname'];
            $email = $_SESSION['email'];
            $password = password_hash($_SESSION['password'], PASSWORD_BCRYPT);
            $phone = $_SESSION['phone'];

            $stmt = $conn->prepare("INSERT INTO users (first_name, surname, email, phone_number, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $surname, $email, $phone, $password);
            if ($stmt->execute()) {
                echo "Registration successful.";
                session_unset();
                session_destroy();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Invalid verification code.";
        }
    } else {
        $first_name = $_POST['first_name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];

        // Validate and format phone number
        $phone = formatPhoneNumber($phone);
        if (!$phone) {
            $error = "Invalid phone number format.";
        } else {
            // Save user data in session
            $_SESSION['first_name'] = $first_name;
            $_SESSION['surname'] = $surname;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            $_SESSION['phone'] = $phone;

            // Generate a verification code
            $code = rand(100000, 999999);

            // Save the verification code in the database
            $stmt = $conn->prepare("INSERT INTO phone_verifications (phone, code) VALUES (?, ?)");
            $stmt->bind_param("ss", $phone, $code);
            $stmt->execute();
            $stmt->close();

            // Send the verification code via Twilio
            sendVerificationCode($phone, $code, $twilio_sid, $twilio_token, $twilio_phone_number);

            $verification_sent = true;
        }
    }
}

$title = "Register";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Register</h1>
    <?php if (isset($error)) { echo "<div class='text-red-500 mb-4'>$error</div>"; } ?>
    <?php if (isset($verification_sent) && $verification_sent) { ?>
        <form action="register.php" method="post">
            <div class="mb-4">
                <label class="block text-gray-700">Verification Code</label>
                <input type="text" name="verify_code" class="w-full border rounded p-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Verify</button>
        </form>
    <?php } else { ?>
        <form action="register.php" method="post">
            <div class="mb-4">
                <label class="block text-gray-700">First Name</label>
                <input type="text" name="first_name" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Surname</label>
                <input type="text" name="surname" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Phone</label>
                <input type="text" name="phone" class="w-full border rounded p-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Register</button>
        </form>
    <?php } ?>
</div>
<?php include 'footer.php'; ?>