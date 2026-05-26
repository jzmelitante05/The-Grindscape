<?php
declare(strict_types=1);

function clean_input(string $value): string
{
    return trim(str_replace(["\r", "\n"], ' ', $value));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function render_page(string $title, string $content, bool $isError = false): void
{
    $accent = $isError ? '#8b2f24' : '#2f6b3c';
    $badge = $isError ? 'Submission Error' : 'Reservation Received';

    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . e($title) . ' | The Grindscape</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/custom.css">
  <style>
    body { background: #f8f1e8; font-family: "DM Sans", Arial, sans-serif; }
    .response-shell { min-height: 100vh; display: grid; place-items: center; padding: 32px 16px; }
    .response-card { width: min(760px, 100%); background: #fffaf4; border: 1px solid rgba(92, 58, 46, 0.16); border-radius: 18px; box-shadow: 0 18px 50px rgba(47, 30, 23, 0.12); padding: clamp(24px, 5vw, 48px); }
    .response-badge { color: ' . $accent . '; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; font-size: .76rem; }
    .response-title { font-family: "Playfair Display", Georgia, serif; color: #3f2a22; margin: 10px 0 16px; }
    .summary-list { margin: 22px 0; padding-left: 0; list-style: none; }
    .summary-list li { padding: 10px 0; border-bottom: 1px solid rgba(92, 58, 46, 0.12); }
    .summary-label { color: #70584f; font-weight: 700; display: inline-block; min-width: 120px; }
    .back-link { display: inline-flex; align-items: center; margin-top: 16px; color: #6f3f2d; font-weight: 700; text-decoration: none; }
    .back-link:hover { color: #3f2a22; }
  </style>
</head>
<body>
  <main class="response-shell">
    <section class="response-card">
      <div class="response-badge">' . $badge . '</div>
      ' . $content . '
    </section>
  </main>
</body>
</html>';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    render_page(
        'Reservation Form',
        '<h1 class="response-title">Please use the reservation form.</h1>
         <p>Reservation requests should be submitted from the contact page.</p>
         <a class="back-link" href="contact.html#reserve-form">Back to reservation form</a>',
        true
    );
    exit;
}

$name = clean_input($_POST['name'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$subject = clean_input($_POST['subject'] ?? '');
$partySize = clean_input($_POST['party_size'] ?? '');
$age = clean_input($_POST['age'] ?? '');
$visitDate = clean_input($_POST['visit_date'] ?? '');
$visitTime = clean_input($_POST['visit_time'] ?? '');
$message = trim($_POST['message'] ?? '');

$subjectLabels = [
    'table' => 'Reserve a Table',
    'event' => 'Private Event Inquiry',
    'birthday' => 'Birthday Celebration',
    'feedback' => 'Feedback or Suggestion',
    'other' => 'Other',
];

$timeLabels = [
    '10-12' => '10:00 AM - 12:00 PM',
    '12-14' => '12:00 PM - 2:00 PM',
    '14-17' => '2:00 PM - 5:00 PM',
    '17-20' => '5:00 PM - 8:00 PM',
    '20-22' => '8:00 PM - 10:00 PM',
];

$errors = [];

if (strlen($name) < 2) {
    $errors[] = 'Full name must be at least 2 characters.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
}

if (!array_key_exists($subject, $subjectLabels)) {
    $errors[] = 'Please select a valid subject.';
}

if (!filter_var($age, FILTER_VALIDATE_INT, ['options' => ['min_range' => 12, 'max_range' => 120]])) {
    $errors[] = 'Guests must be 12 years old or above.';
}

if (strlen(trim($message)) < 15) {
    $errors[] = 'Message must be at least 15 characters.';
}

if ($phone !== '' && !preg_match('/^[+0-9\s\-()]{7,20}$/', $phone)) {
    $errors[] = 'Please provide a valid phone number or leave it blank.';
}

if ($visitDate !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $visitDate);
    if (!$date || $date->format('Y-m-d') !== $visitDate) {
        $errors[] = 'Please provide a valid visit date.';
    }
}

if ($visitTime !== '' && !array_key_exists($visitTime, $timeLabels)) {
    $errors[] = 'Please select a valid time slot.';
}

if ($errors) {
    $items = '';
    foreach ($errors as $error) {
        $items .= '<li>' . e($error) . '</li>';
    }

    render_page(
        'Submission Error',
        '<h1 class="response-title">We could not submit your reservation yet.</h1>
         <p>Please review the following and try again:</p>
         <ul>' . $items . '</ul>
         <a class="back-link" href="contact.html#reserve-form">Back to reservation form</a>',
        true
    );
    exit;
}

$safeSubject = $subjectLabels[$subject];
$safeTime = $visitTime !== '' ? $timeLabels[$visitTime] : 'Not specified';
$safeDate = $visitDate !== '' ? $visitDate : 'Not specified';
$safePartySize = $partySize !== '' ? $partySize : 'Not specified';
$safePhone = $phone !== '' ? $phone : 'Not provided';

$summary = '<h1 class="response-title">Thank you, ' . e($name) . '.</h1>
  <p>Your reservation request has been received. Here is the sanitized summary of your submission:</p>
  <ul class="summary-list">
    <li><span class="summary-label">Email:</span> ' . e($email) . '</li>
    <li><span class="summary-label">Phone:</span> ' . e($safePhone) . '</li>
    <li><span class="summary-label">Subject:</span> ' . e($safeSubject) . '</li>
    <li><span class="summary-label">Party Size:</span> ' . e($safePartySize) . '</li>
    <li><span class="summary-label">Age:</span> ' . e($age) . '</li>
    <li><span class="summary-label">Visit Date:</span> ' . e($safeDate) . '</li>
    <li><span class="summary-label">Visit Time:</span> ' . e($safeTime) . '</li>
    <li><span class="summary-label">Message:</span> ' . nl2br(e($message)) . '</li>
  </ul>
  <p>Our team will confirm your reservation by email within 24 hours.</p>
  <a class="back-link" href="contact.html#reserve-form">Back to reservation form</a>';

render_page('Reservation Received', $summary);
