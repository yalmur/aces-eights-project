<!DOCTYPE html>
<html>
<body style="font-family: monospace; background: #f9f9f9; padding: 32px; color: #1b1c1c;">
  <h2 style="margin:0 0 8px;">Contact Form Message</h2>
  <hr style="border:1px solid #ccc; margin:0 0 24px;">
  <p><strong>From:</strong> {{ $senderName }} &lt;{{ $senderEmail }}&gt;</p>
  <p><strong>Subject:</strong> {{ $subject }}</p>
  <hr style="border:1px solid #eee; margin:16px 0;">
  <p style="white-space:pre-wrap;">{{ $body }}</p>
  <hr style="border:1px solid #eee; margin:24px 0;">
  <p style="font-size:11px; color:#888;">Sent via the Aces &amp; Eights Pizza website contact form.</p>
</body>
</html>
