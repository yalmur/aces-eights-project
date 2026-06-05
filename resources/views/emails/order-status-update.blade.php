<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
  .wrap { max-width: 600px; margin: 32px auto; background: #fcf9f8; border: 2px solid #1b1c1c; }
  .header { background: #690008; padding: 24px 32px; }
  .header h1 { color: #fff; font-size: 22px; margin: 0; letter-spacing: 0.05em; text-transform: uppercase; }
  .body { padding: 32px; }
  .status-badge { display: inline-block; background: #690008; color: #fff; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em; padding: 8px 16px; margin-bottom: 24px; }
  .footer { padding: 16px 32px; border-top: 2px solid #1b1c1c; font-size: 11px; color: #58413f; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Aces &amp; Eights Pizza</h1>
  </div>
  <div class="body">
    <p style="font-size:15px;margin:0 0 16px;">Hi {{ $order->customer_name }},</p>
    <p style="font-size:15px;margin:0 0 24px;">Your order <strong>#{{ $order->id }}</strong> has been updated:</p>

    <div class="status-badge">{{ $order->status_label }}</div>

    @if($order->status === 'cooking')
      <p style="font-size:14px;color:#58413f;">Your order is in the kitchen. Estimated time: 20–25 minutes.</p>
    @elseif($order->status === 'ready')
      <p style="font-size:14px;color:#58413f;">Your order is ready{{ $order->type === 'collection' ? ' for collection' : '' }}!</p>
    @elseif($order->status === 'out_for_delivery')
      <p style="font-size:14px;color:#58413f;">Your order is on its way to you!</p>
    @elseif($order->status === 'delivered')
      <p style="font-size:14px;color:#58413f;">Your order has been delivered. Enjoy!</p>
    @elseif($order->status === 'collected')
      <p style="font-size:14px;color:#58413f;">Your order has been collected. Enjoy!</p>
    @elseif($order->status === 'cancelled')
      <p style="font-size:14px;color:#ba1a1a;">Your order has been cancelled. Please contact us if you have any questions.</p>
    @endif

    <p style="font-size:13px;margin-top:24px;">Questions? Call us on <strong>+44 020 7485 4033</strong> or email <strong>nw5pizza@gmail.com</strong></p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} Aces &amp; Eights Pizza · 156 &amp; 158 Fortess Road, London NW5 2HP
  </div>
</div>
</body>
</html>
