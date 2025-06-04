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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h1 class="mb-4">Binance Trading Bot</h1>
    <form method="post" class="mb-3">
        <div class="mb-3">
            <label class="form-label">API Key</label>
            <input type="text" class="form-control" name="api_key" required value="<?= htmlspecialchars($apiKey) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">API Secret</label>
            <input type="password" class="form-control" name="api_secret" required value="<?= htmlspecialchars($secret) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Symbol</label>
            <input type="text" class="form-control" name="symbol" value="<?= htmlspecialchars($symbol) ?>">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Short MA</label>
                <input type="number" class="form-control" name="ma_short" value="<?= htmlspecialchars($maShort) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Long MA</label>
                <input type="number" class="form-control" name="ma_long" value="<?= htmlspecialchars($maLong) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="text" class="form-control" name="quantity" value="<?= htmlspecialchars($quantity) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Start Trading</button>
    </form>
    <form method="post" class="mb-3">
        <input type="hidden" name="clear" value="1">
        <button type="submit" class="btn btn-secondary">Clear Credentials</button>
    </form>
    <?php if ($message): ?>
        <pre class="bg-light p-3 border rounded"><?= htmlspecialchars($message) ?></pre>
    <?php endif; ?>
</body>
</html>
