<?php
// Simple web UI for entering Binance credentials and strategy options
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = $_POST['api_key'] ?? '';
    $secret = $_POST['api_secret'] ?? '';
    $symbol = $_POST['symbol'] ?? 'BTCUSDT';
    $maShort = (int)($_POST['ma_short'] ?? 5);
    $maLong = (int)($_POST['ma_long'] ?? 20);
    $quantity = (float)($_POST['quantity'] ?? 0.001);

    require __DIR__ . '/trader.php';
    try {
        $result = moving_average_cross($apiKey, $secret, $symbol, $maShort, $maLong, $quantity);
        $message = var_export($result, true);
    } catch (Throwable $e) {
        $message = 'Error: ' . $e->getMessage();
    }
} else {
    $message = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Binance Trading Bot</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        label { display: block; margin-top: 0.5em; }
    </style>
</head>
<body>
    <h1>Binance Trading Bot</h1>
    <form method="post">
        <label>API Key
            <input type="text" name="api_key" size="40" required>
        </label>
        <label>API Secret
            <input type="password" name="api_secret" size="40" required>
        </label>
        <label>Symbol
            <input type="text" name="symbol" value="BTCUSDT">
        </label>
        <label>Short MA
            <input type="number" name="ma_short" value="5">
        </label>
        <label>Long MA
            <input type="number" name="ma_long" value="20">
        </label>
        <label>Quantity
            <input type="text" name="quantity" value="0.001">
        </label>
        <button type="submit">Start Trading</button>
    </form>
    <pre><?= htmlspecialchars($message) ?></pre>
</body>
</html>
