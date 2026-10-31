<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = $_SESSION['flash_message'] ?? '';
$messageType = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

$old = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'age' => '',
    'destination' => '',
    'travel_date' => '',
    'travellers' => '1',
    'requirements' => ''
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $value) {
        $old[$key] = trim((string)($_POST[$key] ?? ''));
    }

    $token = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    }

    if ($old['full_name'] === '' || mb_strlen($old['full_name']) > 100) {
        $errors[] = 'Enter your name (maximum 100 characters).';
    }

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || strlen($old['email']) > 100) {
        $errors[] = 'Enter a valid email address.';
    }

    if (!preg_match('/^[0-9+\-\s]{7,15}$/', $old['phone'])) {
        $errors[] = 'Enter a valid phone number (7–15 characters).';
    }

    $age = filter_var($old['age'], FILTER_VALIDATE_INT);
    if ($age === false || $age < 1 || $age > 120) {
        $errors[] = 'Age must be between 1 and 120.';
    }

    $allowedDestinations = ['Manali', 'Goa', 'Jaipur', 'Kerala', 'Rishikesh', 'Varanasi'];
    if (!in_array($old['destination'], $allowedDestinations, true)) {
        $errors[] = 'Choose a destination from the list.';
    }

    $date = DateTime::createFromFormat('Y-m-d', $old['travel_date']);
    $dateValid = $date && $date->format('Y-m-d') === $old['travel_date'];
    if (!$dateValid || $old['travel_date'] < date('Y-m-d')) {
        $errors[] = 'Choose today or a future travel date.';
    }

    $travellers = filter_var($old['travellers'], FILTER_VALIDATE_INT);
    if ($travellers === false || $travellers < 1 || $travellers > 20) {
        $errors[] = 'Number of travellers must be between 1 and 20.';
    }

    if (mb_strlen($old['requirements']) > 1000) {
        $errors[] = 'Additional requirements must be 1000 characters or fewer.';
    }

    if (!$errors) {
        $stmt = $conn->prepare(
            'INSERT INTO registrations
            (full_name, email, phone, age, destination, travel_date, travellers, requirements)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->bind_param(
            'sssissis',
            $old['full_name'],
            $old['email'],
            $old['phone'],
            $age,
            $old['destination'],
            $old['travel_date'],
            $travellers,
            $old['requirements']
        );

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = 'Your trip registration has been submitted successfully!';
            $_SESSION['flash_type'] = 'success';
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: index.php');
            exit;
        }

        $errors[] = 'Registration could not be saved. Please try again.';
        $stmt->close();
    }

    if ($errors) {
        $message = implode(' ', $errors);
        $messageType = 'error';
    }
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register your next trip with WanderDesk.">
    <title>WanderDesk | Trip Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a class="brand" href="index.php"><span class="brand-mark">W</span> WanderDesk<span class="brand-dot">.</span></a>
        <span class="nav-caption">Make room for a new story.</span>
    </nav>

    <header class="hero">
        <div class="hero-inner">
            <p class="eyebrow">YOUR NEXT CHAPTER BEGINS HERE</p>
            <h1>Go somewhere<br><span>you've never been.</span></h1>
            <p class="hero-copy">Choose a destination, set your dates, and take the first step toward your next adventure.</p>
            <div class="hero-pills"><span>✦ Thoughtful trips</span><span>✦ Simple registration</span></div>
        </div>
        <div class="hero-art" aria-hidden="true">
            <div class="sun"></div><div class="mountain mountain-back"></div><div class="mountain mountain-front"></div>
            <div class="art-label">FIND YOUR<br>NEXT VIEW ↗</div>
        </div>
    </header>

    <main class="page-wrap">
        <section class="form-card">
            <div class="section-heading">
                <div>
                    
                    <p class="eyebrow dark">LET'S GET YOU GOING</p>
                    <h2>Trip registration</h2>
                    <p class="muted">A few details and you're on your way.</p>
                </div>
                <span class="step-badge">01 <i>/ 01</i></span>
            </div>

            <?php if ($message !== ''): ?>
                <div class="notice <?= e($messageType) ?>" role="status"><?= e($message) ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST" id="tripForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

                <div class="form-grid">
                    <div class="field">
                        <label for="full_name">Full name <span>*</span></label>
                        <input id="full_name" name="full_name" type="text" maxlength="100" placeholder="e.g. Alex Morgan" value="<?= e($old['full_name']) ?>" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="email">Email address <span>*</span></label>
                        <input id="email" name="email" type="email" maxlength="100" placeholder="you@example.com" value="<?= e($old['email']) ?>" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="phone">Phone number <span>*</span></label>
                        <input id="phone" name="phone" type="tel" maxlength="15" placeholder="+91 98765 43210" value="<?= e($old['phone']) ?>" required autocomplete="tel">
                    </div>
                    <div class="field">
                        <label for="age">Age <span>*</span></label>
                        <input id="age" name="age" type="number" min="1" max="120" placeholder="Your age" value="<?= e($old['age']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="destination">Destination <span>*</span></label>
                        <select id="destination" name="destination" required>
                            <option value="">Choose your destination</option>
                            <?php foreach (['Manali', 'Goa', 'Jaipur', 'Kerala', 'Rishikesh', 'Varanasi'] as $place): ?>
                                <option value="<?= e($place) ?>" <?= $old['destination'] === $place ? 'selected' : '' ?>><?= e($place) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="travel_date">Travel date <span>*</span></label>
                        <input id="travel_date" name="travel_date" type="date" min="<?= date('Y-m-d') ?>" value="<?= e($old['travel_date']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="travellers">Number of travellers <span>*</span></label>
                        <input id="travellers" name="travellers" type="number" min="1" max="20" value="<?= e($old['travellers']) ?>" required>
                    </div>
                </div>

                <div class="field field-full">
                    <label for="requirements">Anything else we should know? <small>(Optional)</small></label>
                    <textarea id="requirements" name="requirements" rows="4" maxlength="1000" placeholder="Share preferences or special requirements..."><?= e($old['requirements']) ?></textarea>
                    <div class="char-count"><span id="charCount">0</span>/1000</div>
                </div>

                <button class="submit-btn" type="submit">Submit registration <span aria-hidden="true">↗</span></button>
                <p class="form-note">Your details are used only to process this trip registration.</p>
            </form>
        </section>
    </main>

    <footer>© <?= date('Y') ?> WanderDesk <span>·</span> Collect moments, not just miles.</footer>
    <script src="script.js"></script>
</body>
</html>
