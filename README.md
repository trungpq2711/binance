# Binance Auto Trader

This project provides a minimal example of how to create an automated trading bot
for [Binance](https://www.binance.com/). It uses the
[`python-binance`](https://github.com/sammchardy/python-binance) library and
implements a simple moving average crossover strategy.

## Setup

1. Install Python 3.8+ and create a virtual environment (optional).
2. Install dependencies:

```bash
pip install -r requirements.txt
```

3. Create a Binance API key and secret:

   - Log in to your Binance account and open **API Management** from the profile menu.
   - Click **Create API**, give it a label and complete any security verification.
   - Copy the generated **API Key** and **Secret** (the secret is displayed only once).
   - Enable trading permissions if you intend to place real orders.

   Then export them as environment variables:

   ```bash
   export BINANCE_API_KEY="<your api key>"
   export BINANCE_API_SECRET="<your api secret>"
   ```

## Running the bot

The example strategy is defined in `autotrader/bot.py`. Run it with Python:

```bash
python -m autotrader.bot
```

The script fetches recent market data for BTC/USDT, calculates two moving
averages, and executes a market order when they cross.

## Disclaimer

This example is for educational purposes only. Trading cryptocurrencies involves
risk. Use at your own discretion and test thoroughly before trading with real
funds.
