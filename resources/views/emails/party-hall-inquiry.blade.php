<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>body{font-family:Georgia,serif;color:#1b1c1c;background:#fcf9f8;padding:32px}h1{color:#690008;border-bottom:2px solid #690008;padding-bottom:8px}table{width:100%;border-collapse:collapse;margin-top:16px}td{padding:10px 12px;border-bottom:1px solid #e0dbd9;vertical-align:top}td:first-child{font-weight:bold;width:30%;color:#690008;text-transform:uppercase;font-size:12px;letter-spacing:.05em}.msg{background:#f5f0ef;padding:16px;border-left:3px solid #690008;margin-top:16px;white-space:pre-wrap}</style></head>
<body>
<h1>Party Hall Inquiry</h1>
<table>
  <tr><td>Name</td><td>{{ $data['name'] }}</td></tr>
  <tr><td>Email</td><td>{{ $data['email'] }}</td></tr>
  <tr><td>Phone</td><td>{{ $data['phone'] }}</td></tr>
  <tr><td>Event Date</td><td>{{ \Carbon\Carbon::parse($data['event_date'])->format('l, d F Y') }}</td></tr>
  <tr><td>Guests</td><td>{{ $data['guests'] }}</td></tr>
  <tr><td>Event Type</td><td>{{ $data['event_type'] }}</td></tr>
</table>
@if(!empty($data['message']))
<div class="msg">{{ $data['message'] }}</div>
@endif
<p style="margin-top:24px;font-size:12px;color:#666">Reply directly to this email to respond to {{ $data['name'] }}.</p>
</body>
</html>
