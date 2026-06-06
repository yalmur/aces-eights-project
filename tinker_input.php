$maxId = App\Models\Order::max("id") ?? 0;
$order = App\Models\Order::create([
  "user_id" => null,
  "customer_name" => "Sound Test",
  "customer_email" => "soundtest@test.com",
  "customer_phone" => "07700000000",
  "type" => "collection",
  "status" => "accepted",
  "subtotal" => 1000,
  "delivery_fee" => 0,
  "discount_amount" => 0,
  "total" => 1000,
  "payment_method" => "card",
  "stripe_session_id" => "test_sound_" . time(),
]);
echo "Created order #" . $order->id . " (prev max: " . $maxId . ")";
