import pandas as pd
import os

# Ganti dengan nama file Excel-mu
file_excel = 'FINAL NERACA KOPERASI 2025 20 JANUARI 2026(1).xlsx'

print(f"Membaca file {file_excel}...")
xls = pd.ExcelFile(file_excel)

# Looping ke setiap sheet dan jadikan CSV
for sheet_name in xls.sheet_names:
    print(f"Memproses sheet: {sheet_name}...")
    # Baca sheet
    df = pd.read_excel(xls, sheet_name=sheet_name)
    
    # Nama file output (misal: "1 PRINT NERACA.csv")
    nama_file_csv = f"{sheet_name}.csv"
    
    # Simpan ke CSV (index=False agar nomor baris bawaan pandas tidak ikut)
    df.to_csv(nama_file_csv, index=False)

print("✅ Selesai! Semua sheet berhasil diubah menjadi CSV.")