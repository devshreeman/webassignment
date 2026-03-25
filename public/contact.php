<?php
include('../config/db.php');

$pageTitle  = 'Contact Us';
$activePage = 'contact';
$cssBase    = '../';
$rootBase   = '../';
$successMsg = '';
$errors     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $honeypot = $_POST['_hp'] ?? '';

    if (empty($name))    $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (empty($subject)) $errors[] = 'Please select a subject.';
    if (strlen($message) < 20) $errors[] = 'Message must be at least 20 characters.';
    if (!empty($honeypot)) $errors[] = 'Spam detected.';

    if (empty($errors)) {
        try {
            // make sure contact messages table exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                subject VARCHAR(200) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_read TINYINT(1) DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $successMsg = 'Thank you for your message. Our team will respond within 2 working days.';
        } catch (PDOException $e) {
            $errors[] = 'Unable to send message. Please try again later.';
        }
    }
}

include('../includes/header.php');

// contact info for the university
$contactDetails = [
    [
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'label' => 'Address',
        'value' => 'University of Liverpool<br>Foundation Building<br>Brownlow Hill<br>Liverpool, L69 7ZX<br>United Kingdom',
        'raw'   => true,
    ],
    [
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.59 3.43 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.75a16 16 0 0 0 6.34 6.34l1.81-1.81a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'label' => 'Admissions',
        'value' => '+44 (0)151 794 2000',
    ],
    [
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'label' => 'General Enquiries',
        'value' => 'enquiries@liverpool.ac.uk',
    ],
    [
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
        'label' => 'Student Services',
        'value' => 'studentservices@liverpool.ac.uk',
    ],
    [
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'label' => 'Office Hours',
        'value' => 'Monday – Friday: 09:00 – 17:00<br>Closed on Bank Holidays',
        'raw'   => true,
    ],
];
?>

<section style="background:linear-gradient(135deg,var(--color-primary-dark) 0%,var(--color-primary) 100%);padding:var(--space-12) 0;color:#fff;text-align:center;">
  <div class="container">
    <span style="display:inline-block;background:rgba(197,169,106,0.2);border:1px solid rgba(197,169,106,0.4);color:#dfc48a;font-size:0.7rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:0.25rem 0.75rem;border-radius:9999px;margin-bottom:1rem;">Get in Touch</span>
    <h1 style="font-family:'Merriweather',serif;font-size:clamp(1.75rem,4vw,2.5rem);color:#fff;margin-bottom:1rem;">Contact Us</h1>
    <p style="color:rgba(255,255,255,0.7);max-width:560px;margin:0 auto;font-size:1.05rem;">Our admissions and student services team are here to help. We aim to respond to all enquiries within two working days.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="two-col-layout">

      <!-- Enquiry Form -->
      <div>
        <?php if ($successMsg): ?>
          <div class="alert alert--success" role="alert"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
          <div class="alert alert--error" role="alert">
            <ul style="margin:0;padding-left:1.25rem;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
          </div>
        <?php endif; ?>
        <?php if (!$successMsg): ?>
          <div class="form-card">
            <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-6);color:var(--color-primary);">Send an Enquiry</h2>
            <form method="POST" action="" novalidate>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="name">Full Name <span class="form-required">*</span></label>
                  <input class="form-input" type="text" id="name" name="name" required
                         value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Jane Smith" autocomplete="name">
                </div>
                <div class="form-group">
                  <label class="form-label" for="email">Email Address <span class="form-required">*</span></label>
                  <input class="form-input" type="email" id="email" name="email" required
                         value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="jane@example.com" autocomplete="email">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="subject">Subject <span class="form-required">*</span></label>
                <select class="form-select" id="subject" name="subject" required>
                  <option value="">Select a topic…</option>
                  <?php foreach (['Admissions Enquiry','Programme Information','Open Day Registration','Fees & Funding','General Enquiry'] as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>" <?= ($_POST['subject'] ?? '') === $opt ? 'selected' : '' ?>>
                      <?= htmlspecialchars($opt) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="message">Message <span class="form-required">*</span></label>
                <textarea class="form-textarea" id="message" name="message" rows="5" required
                          placeholder="Please include as much detail as possible…"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              </div>
              <div style="display:none;" aria-hidden="true"><input type="text" name="_hp" tabindex="-1"></div>
              <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
          </div>
        <?php endif; ?>
      </div>

      <!-- Contact Details -->
      <div>
        <h2 style="font-size:var(--text-xl);margin-bottom:var(--space-6);color:var(--color-primary);">University Details</h2>
        <?php foreach ($contactDetails as $c): ?>
          <div style="display:flex;gap:var(--space-4);padding:var(--space-4) 0;border-bottom:1px solid var(--color-border);align-items:flex-start;">
            <div style="color:var(--color-primary);flex-shrink:0;margin-top:2px;"><?= $c['icon'] ?></div>
            <div>
              <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-text-muted);margin-bottom:0.25rem;"><?= htmlspecialchars($c['label']) ?></div>
              <div style="font-size:0.9rem;color:var(--color-text);line-height:1.6;">
                <?= !empty($c['raw']) ? $c['value'] : htmlspecialchars($c['value']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
