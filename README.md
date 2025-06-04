# Binance Auto Trader

This project provides a minimal example of how to create an automated trading bot for [Binance](https://www.binance.com/) using PHP. It implements a simple moving average crossover strategy and exposes both a command line script and a tiny web interface.

## Setup

1. Install PHP 8 with the cURL extension enabled.
2. Create a Binance API key and secret:
   - Log in to your Binance account and open **API Management**.
   - Click **Create API**, give it a label and complete the verification steps.
   - Copy the generated **API Key** and **Secret** (the secret is shown only once).
   - Enable trading permissions if you intend to place real orders.

Set the credentials as environment variables when running the command line script:

```bash
export BINANCE_API_KEY="<your api key>"
export BINANCE_API_SECRET="<your api secret>"
```

## Running the CLI bot

Execute the strategy from the terminal:

```bash
php trader.php
```

The script fetches recent BTC/USDT prices and places a market buy or sell order when the short moving average crosses the long one.

## Web interface

A minimal form is provided in `ui.php` so you can enter your API credentials and strategy parameters via a browser. Start a local server with:

```bash
php -S localhost:8000 ui.php
```

Open <http://localhost:8000> and fill in the form to run the strategy once.

## Disclaimer

This example is for educational purposes only. Trading cryptocurrencies involves risk. Test thoroughly and use at your own discretion before trading with real funds.
