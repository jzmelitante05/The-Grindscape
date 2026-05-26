<?php
// ============================================================
//  contact.php — The Grindscape Reservation Form Handler
//  Receives POST data from contact.html, validates, sanitizes,
//  and returns a JSON response consumed by main.js.
// ============================================================

// --- 1. HEADERS ------------------------------------------------
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// --- 2. HELPER FUNCTIONS ---------------------------------------

/**
 * Sanitize a plain-text field:
 * strips leading/trailing whitespace and escapes HTML special chars.
 */
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Collect errors into an array and return them all at once
 * so the user sees every problem in a single response.
 */
$errors = [];

// --- 3. COLLECT & SANITIZE INPUTS ------------------------------

$name       = sanitize($_POST['name']        ?? '');
$email      = sanitize($_POST['email']       ?? '');
$phone      = sanitize($_POST['phone']       ?? '');   // optional
$subject    = sanitize($_POST['subject']     ?? '');
$partySize  = sanitize($_POST['party_size']  ?? '');   // optional
$age        = trim($_POST['age']             ?? '');   // validated as int below
$date       = sanitize($_POST['date']        ?? '');   // optional
$time       = sanitize($_POST['time']        ?? '');   // optional
$message    = sanitize($_POST['message']     ?? '');

// --- 4. SERVER-SIDE VALIDATION ---------------------------------

// 4a. Full Name — required, at least 2 characters
if ($name === '') {
    $errors[] = 'Full name is required.';
} elseif (mb_strlen($name) < 2) {
    $errors[] = 'Full name must be at least 2 characters.';
} elseif (mb_strlen($name) > 100) {
    $errors[] = 'Full name must not exceed 100 characters.';
}

// 4b. Email — required, must be a valid email format
if ($email === '') {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address (e.g. name@domain.com).';
}

// 4c. Phone — optional, but if provided must match the expected format
if ($phone !== '' && !preg_match('/^[+0-9\s\-()\\/]{7,20}$/', $phone)) {
    $errors[] = 'Phone number format is invalid. Use digits, spaces, +, -, or parentheses.';
}

// 4d. Subject — required, must be one of the allowed dropdown values
$allowedSubjects = ['table', 'event', 'birthday', 'feedback', 'other'];
if ($subject === '') {
    $errors[] = 'Please select a subject.';
} elseif (!in_array($subject, $allowedSubjects, true)) {
    $errors[] = 'Invalid subject selected.';
}

// 4e. Age — required, must be an integer between 12 and 120
//     The café's caffeine policy requires guests to be 12 years old and above.
if ($age === '') {
    $errors[] = 'Age is required.';
} else {
    $ageInt = filter_var($age, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 12, 'max_range' => 120]
    ]);
    if ($ageInt === false) {
        $errors[] = 'Please enter a valid age (12 years and above, up to 120).';
    }
}

// 4f. Party size — optional, validate if provided
$allowedPartySizes = ['1', '2', '3-4', '5-8', '9+'];
if ($partySize !== '' && !in_array($partySize, $allowedPartySizes, true)) {
    $errors[] = 'Invalid party size selected.';
}

// 4g. Preferred date — optional, validate format (YYYY-MM-DD) if provided
if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $errors[] = 'Preferred date format is invalid.';
}

// 4h. Preferred time — optional, validate if provided
$allowedTimes = ['10-12', '12-14', '14-17', '17-20', '20-22'];
if ($time !== '' && !in_array($time, $allowedTimes, true)) {
    $errors[] = 'Invalid time slot selected.';
}

// 4i. Message — required, at least 15 characters, max 2000
if ($message === '') {
    $errors[] = 'Message / special requests field is required.';
} elseif (mb_strlen($message) < 15) {
    $errors[] = 'Message must be at least 15 characters.';
} elseif (mb_strlen($message) > 2000) {
    $errors[] = 'Message must not exceed 2000 characters.';
}

// --- 5. RETURN ERRORS IF ANY -----------------------------------
if (!empty($errors)) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode([
        'success' => false,
        'message' => 'Please fix the following errors:',
        'errors'  => $errors,
    ]);
    exit;
}

// --- 6. PROCESS THE VALID SUBMISSION ---------------------------
//
//  At this point all inputs are validated and sanitized.
//  In a real deployment you would do one or more of:
//    (a) Save to a database (e.g. INSERT INTO reservations ...)
//    (b) Send a confirmation email via mail() or PHPMailer
//    (c) Log the entry to a file
//
//  For this academic project we simply return a success response.
//  Uncomment and extend the block below when you're ready.

/*
// --- OPTIONAL: Log submission to a text file ---
$logLine = implode(' | ', [
    date('Y-m-d H:i:s'),
    $name,
    $email,
    $phone,
    $subject,
    $partySize,
    isset($ageInt) ? $ageInt : '',
    $date,
    $time,
    str_replace(["\r", "\n"], ' ', $message),
]) . PHP_EOL;

file_put_contents(__DIR__ . '/reservations.log', $logLine, FILE_APPEND | LOCK_EX);
*/

/*
// --- OPTIONAL: Send a confirmation email ---
$to      = $email;
$subject_line = 'Your Grindscape Reservation Request';
$body    = "Hi {$name},\n\nThank you for reaching out! We received your reservation request "
         . "and will confirm within 24 hours.\n\nSee you at The Grindscape!\n\n"
         . "— The Grindscape Team\n589 Marcos Highway, Antipolo, Rizal 1870";
$headers = "From: thegrindscape@gmail.com\r\nReply-To: thegrindscape@gmail.com";

mail($to, $subject_line, $body, $headers);
*/

// --- 7. SUCCESS RESPONSE ---------------------------------------
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => "Thank you, {$name}! 🎉 Your reservation request has been received. "
               . "We'll confirm via email at {$email} within 24 hours. See you soon!",
]);
exit;