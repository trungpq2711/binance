<?php
// Minimal web UI for entering Binance credentials and strategy options
session_start();

// Set defaults from the session or fall back to sensible values
$apiKey = $_SESSION['api_key'] ?? '';
$secret = $_SESSION['api_secret'] ?? '';
$symbol = 'BTCUSDT';
$maShort = 5;
$maLong = 20;
$quantity = 0.001;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear'])) {
        // User clicked "Clear Credentials"
        session_unset();
        session_destroy();
        $apiKey = $secret = '';
        $message = 'Credentials cleared.';
    } else {
        $apiKey = $_POST['api_key'] ?? $apiKey;
        $secret = $_POST['api_secret'] ?? $secret;
        $symbol = $_POST['symbol'] ?? $symbol;
        $maShort = (int)($_POST['ma_short'] ?? $maShort);
        $maLong = (int)($_POST['ma_long'] ?? $maLong);
        $quantity = (float)($_POST['quantity'] ?? $quantity);

        // Persist credentials so the form doesn't require re-entry on refresh
        $_SESSION['api_key'] = $apiKey;
        $_SESSION['api_secret'] = $secret;

        require __DIR__ . '/trader.php';
        try {
            $result = moving_average_cross($apiKey, $secret, $symbol, $maShort, $maLong, $quantity);
            $message = var_export($result, true);
        } catch (Throwable $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    }
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
            <input type="text" name="api_key" size="40" required value="<?= htmlspecialchars($apiKey) ?>">
        </label>
        <label>API Secret
            <input type="password" name="api_secret" size="40" required value="<?= htmlspecialchars($secret) ?>">
        </label>
        <label>Symbol
            <input type="text" name="symbol" value="<?= htmlspecialchars($symbol) ?>">
        </label>
        <label>Short MA
            <input type="number" name="ma_short" value="<?= htmlspecialchars($maShort) ?>">
        </label>
        <label>Long MA
            <input type="number" name="ma_long" value="<?= htmlspecialchars($maLong) ?>">
        </label>
        <label>Quantity
            <input type="text" name="quantity" value="<?= htmlspecialchars($quantity) ?>">
        </label>
        <button type="submit">Start Trading</button>
    </form>
    <form method="post" style="margin-top:1em;">
        <input type="hidden" name="clear" value="1">
        <button type="submit">Clear Credentials</button>
    </form>
    <pre><?= htmlspecialchars($message) ?></pre>
</body>
</html>
