import tkinter as tk
from tkinter import ttk
from .bot import create_client, moving_average_cross


def run_ui():
    root = tk.Tk()
    root.title("Binance Trading Bot")

    tk.Label(root, text="API Key").grid(row=0, column=0, sticky="e")
    api_key_entry = tk.Entry(root, width=40)
    api_key_entry.grid(row=0, column=1)

    tk.Label(root, text="API Secret").grid(row=1, column=0, sticky="e")
    api_secret_entry = tk.Entry(root, width=40, show="*")
    api_secret_entry.grid(row=1, column=1)

    tk.Label(root, text="Symbol").grid(row=2, column=0, sticky="e")
    symbol_entry = tk.Entry(root)
    symbol_entry.insert(0, "BTCUSDT")
    symbol_entry.grid(row=2, column=1)

    tk.Label(root, text="Short MA").grid(row=3, column=0, sticky="e")
    ma_short_entry = tk.Entry(root)
    ma_short_entry.insert(0, "5")
    ma_short_entry.grid(row=3, column=1)

    tk.Label(root, text="Long MA").grid(row=4, column=0, sticky="e")
    ma_long_entry = tk.Entry(root)
    ma_long_entry.insert(0, "20")
    ma_long_entry.grid(row=4, column=1)

    tk.Label(root, text="Quantity").grid(row=5, column=0, sticky="e")
    quantity_entry = tk.Entry(root)
    quantity_entry.insert(0, "0.001")
    quantity_entry.grid(row=5, column=1)

    result_var = tk.StringVar()

    def start():
        api_key = api_key_entry.get().strip()
        api_secret = api_secret_entry.get().strip()
        symbol = symbol_entry.get().strip()
        ma_short = int(ma_short_entry.get())
        ma_long = int(ma_long_entry.get())
        quantity = float(quantity_entry.get())

        client = create_client(api_key, api_secret)
        result = moving_average_cross(
            client,
            symbol=symbol,
            ma_short=ma_short,
            ma_long=ma_long,
            quantity=quantity,
        )
        result_var.set(str(result))

    ttk.Button(root, text="Start Trading", command=start).grid(row=6, column=0, columnspan=2, pady=10)
    ttk.Label(root, textvariable=result_var).grid(row=7, column=0, columnspan=2)

    root.mainloop()


if __name__ == "__main__":
    run_ui()
