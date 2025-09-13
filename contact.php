<?php
// contact.php
// Einfache Server-Mail mit PHP mail()

// === KONFIG ===
$to      = 'info@hf-event25.ch';   // <- falls abweichend anpassen
$ok_url  = 'index.html#kontakt';   // nach Erfolg hierhin zurück
$err_url = 'index.html#kontakt';   // bei Fehler hierhin zurück

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: $err_url"); exit;
}

function clean($v){ return trim(filter_var($v, FILTER_SANITIZE_SPECIAL_CHARS)); }

$name    = clean($_POST['name'] ?? '');
$email   = clean($_POST['email'] ?? '');
$subject = clean($_POST['subject'] ?? 'Kontaktformular');
$message = clean($_POST['message'] ?? '');
$consent = isset($_POST['consent']);

if (!$name || !$email || !$message || !$consent || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: $err_url"); exit;
}

$ip   = $_SERVER['REMOTE_ADDR'] ?? '';
$ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
$body = "Neue Nachricht vom Kontaktformular\n\n".
        "Name:   $name\n".
        "E-Mail: $email\n".
        "Betreff: $subject\n\n".
        "Nachricht:\n$message\n\n".
        "---- Meta ----\n".
        "IP: $ip\nUA: $ua\n";

$headers = [];
$headers[] = "From: HF Event 2025 <no-reply@hf-event25.ch>";
$headers[] = "Reply-To: $name <$email>";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "X-Mailer: PHP/" . phpversion();

$sent = @mail($to, "Kontaktformular: $subject", $body, implode("\r\n", $headers));

if ($sent) {
  // kleine Dankeseite (kannst du frei anpassen)
  echo "<!doctype html><meta charset='utf-8'><meta http-equiv='refresh' content='2;url=$ok_url'>
        <style>body{font-family:system-ui;padding:2rem;text-align:center}</style>
        <h2>Danke! Ihre Nachricht wurde gesendet.</h2>
        <p>Sie werden gleich zurückgeleitet …</p>";
} else {
  echo "<!doctype html><meta charset='utf-8'><style>body{font-family:system-ui;padding:2rem;text-align:center;color:#b00020}</style>
        <h2>Leider konnte die Nachricht nicht gesendet werden.</h2>
        <p>Bitte versuchen Sie es später erneut oder schreiben Sie an <a href='mailto:$to'>$to</a>.</p>
        <p><a href='$err_url'>Zurück</a></p>";
}
