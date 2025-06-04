<?php
/**
 * Simple Binance trading example in PHP.
 */

function create_signature(string $query, string $secret): string {
    return hash_hmac('sha256', $query, $secret);
}

function send_request(string $method, string $endpoint, array $params, string $apiKey = '', string $secret = '') {
    $base = 'https://api.binance.com';
    $ch = curl_init();

    if ($apiKey) {
        $headers = [
            'X-MBX-APIKEY: ' . $apiKey,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $query = http_build_query($params);
    if ($secret) {
        $query .= '&signature=' . create_signature($query, $secret);
    }

    if ($method === 'GET') {
        $url = $base . $endpoint . '?' . $query;
    } else {
        $url = $base . $endpoint;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($response === false) {
        throw new RuntimeException('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($response, true);
}

function get_klines(string $symbol, string $interval, int $limit): array {
    return send_request('GET', '/api/v3/klines', [
        'symbol' => $symbol,
        'interval' => $interval,
        'limit' => $limit,
    ]);
}

function place_market_order(string $symbol, string $side, float $quantity, string $apiKey, string $secret) {
    $params = [
        'symbol' => $symbol,
        'side' => $side,
        'type' => 'MARKET',
        'quantity' => $quantity,
        'timestamp' => (int)(microtime(true) * 1000),
    ];
    return send_request('POST', '/api/v3/order', $params, $apiKey, $secret);
}

function moving_average_cross(string $apiKey, string $secret, string $symbol = 'BTCUSDT', int $maShort = 5, int $maLong = 20, float $quantity = 0.001) {
    $klines = get_klines($symbol, '1h', $maLong);
    $closes = array_map(fn($k) => (float)$k[4], $klines);

    $shortMA = array_sum(array_slice($closes, -$maShort)) / $maShort;
    $longMA = array_sum($closes) / $maLong;

    if ($shortMA > $longMA) {
        return place_market_order($symbol, 'BUY', $quantity, $apiKey, $secret);
    } elseif ($shortMA < $longMA) {
        return place_market_order($symbol, 'SELL', $quantity, $apiKey, $secret);
    }
    return null;
}

$apiKey = getenv('BINANCE_API_KEY');
$secret = getenv('BINANCE_API_SECRET');

if (!$apiKey || !$secret) {
    fwrite(STDERR, "Please set BINANCE_API_KEY and BINANCE_API_SECRET environment variables\n");
    exit(1);
}

try {
    $result = moving_average_cross($apiKey, $secret);
    print_r($result);
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
?>
