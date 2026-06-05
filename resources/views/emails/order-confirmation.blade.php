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
  h2 { font-size: 16px; text-transform: uppercase; letter-spacing: 0.08em; color: #690008; margin: 0 0 16px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  td { padding: 8px 0; font-size: 14px; color: #1b1c1c; border-bottom: 1px solid #e0bfbc; }
  .total-row td { font-weight: bold; font-size: 15px; border-bottom: none; padding-top: 12px; }
  .footer { padding: 16px 32px; border-top: 2px solid #1b1c1c; font-size: 11px; color: #58413f; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Aces &amp; Eights Pizza</h1>
  </div>
  <div class="body">
    <p style="font-size:15px;margin:0 0 24px;">Hi {{ $order->customer_name }}, your order is confirmed!</p>

    <h2>Order #{{ $order->id }}</h2>
    <table>
      @foreach($order->items as $item)
      <tr>
        <td>{{ $item->qty }}× {{ $item->name }}
          @if($item->customisation_summary !== 'No extras')
            <br><small style="color:#58413f;">{{ $item->customisation_summary }}</small>
          @endif
        </td>
        <td style="text-align:right;">£{{ number_format($item->line_total, 2) }}</td>
      </tr>
      @endforeach
    </table>

    <table>
      <tr>
        <td>Subtotal</td>
        <td style="text-align:right;">£{{ number_format($order->subtotal, 2) }}</td>
      </tr>
      <tr>
        <td>Delivery Fee</td>
        <td style="text-align:right;">
          @if($order->delivery_fee > 0) £{{ number_format($order->delivery_fee, 2) }} @else FREE @endif
        </td>
      </tr>
      @if($order->discount_amount > 0)
      <tr>
        <td>Discount ({{ $order->promo_code }})</td>
        <td style="text-align:right;color:#2d6a2d;">−£{{ number_format($order->discount_amount, 2) }}</td>
      </tr>
      @endif
      <tr class="total-row">
        <td>Total</td>
        <td style="text-align:right;color:#690008;">£{{ number_format($order->total, 2) }}</td>
      </tr>
    </table>

    @if($order->type === 'delivery')
      <p style="font-size:13px;color:#58413f;">Delivering to: {{ $order->delivery_address }}, {{ $order->delivery_city }}, {{ $order->delivery_postcode }}</p>
    @else
      <p style="font-size:13px;color:#58413f;">Collection from: 156 &amp; 158 Fortess Road, Tufnell Park, London, NW5 2HP</p>
    @endif

    <p style="font-size:13px;margin-top:24px;">Questions? Call us on <strong>+44 020 7485 4033</strong> or email <strong>nw5pizza@gmail.com</strong></p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} Aces &amp; Eights Pizza · 156 &amp; 158 Fortess Road, London NW5 2HP
  </div>
</div>
</body>
</html>
