"""Core trading logic used by the example UI."""

import os
from binance.client import Client
from binance.exceptions import BinanceAPIException, BinanceOrderException

def create_client(api_key: str, api_secret: str) -> Client:
    """Instantiate and return a Binance client."""
    return Client(api_key, api_secret)


def moving_average_cross(client: Client, symbol: str = "BTCUSDT", ma_short: int = 5, ma_long: int = 20, quantity: float = 0.001):
    """Simple moving average crossover strategy."""
    klines = client.get_klines(symbol=symbol, interval=Client.KLINE_INTERVAL_1HOUR, limit=ma_long)
    closes = [float(k[4]) for k in klines]
    short_ma = sum(closes[-ma_short:]) / ma_short
    long_ma = sum(closes) / ma_long

    try:
        if short_ma > long_ma:
            order = client.order_market_buy(symbol=symbol, quantity=quantity)
            return order
        elif short_ma < long_ma:
            order = client.order_market_sell(symbol=symbol, quantity=quantity)
            return order
    except (BinanceAPIException, BinanceOrderException) as e:
        print(f"Error executing order: {e}")
    return None


if __name__ == "__main__":
    api_key = os.environ.get("BINANCE_API_KEY")
    api_secret = os.environ.get("BINANCE_API_SECRET")

    if not api_key or not api_secret:
        raise RuntimeError(
            "Please set BINANCE_API_KEY and BINANCE_API_SECRET environment variables"
        )

    client = create_client(api_key, api_secret)
    result = moving_average_cross(client)
    print(result)
