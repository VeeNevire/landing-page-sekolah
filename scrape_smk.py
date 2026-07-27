import requests
import pandas as pd
import time
import re

API_BASE = "https://api.ibnuhabib.web.id/api/schools"
CITIES = [
    ("D.K.I. JAKARTA", "KOTA JAKARTA PUSAT"),
    ("D.K.I. JAKARTA", "KOTA JAKARTA UTARA"),
    ("D.K.I. JAKARTA", "KOTA JAKARTA BARAT"),
    ("D.K.I. JAKARTA", "KOTA JAKARTA SELATAN"),
    ("D.K.I. JAKARTA", "KOTA JAKARTA TIMUR"),
    ("D.K.I. JAKARTA", "KAB. KEPULAUAN SERIBU"),
    ("JAWA BARAT", "KOTA DEPOK"),
    ("JAWA BARAT", "KAB. BOGOR"),
    ("JAWA BARAT", "KOTA BOGOR"),
    ("JAWA BARAT", "KOTA BEKASI"),
    ("JAWA BARAT", "KAB. BEKASI"),
    ("BANTEN", "KAB. TANGERANG"),
    ("BANTEN", "KOTA TANGERANG"),
    ("BANTEN", "KOTA TANGERANG SELATAN"),
]

def is_negeri(name):
    return bool(re.search(r'\bNEGERI\b', name.upper()))

def fetch_all(province, city):
    all_data = []
    page = 1
    while True:
        params = {
            "province": province,
            "city": city,
            "type": "SMK",
            "limit": 500,
            "page": page,
        }
        try:
            resp = requests.get(API_BASE, params=params, timeout=30)
            if resp.status_code != 200:
                print(f"  Error {resp.status_code} for {city} page {page}")
                break
            data = resp.json()
            records = data.get("data", [])
            if not records:
                break
            all_data.extend(records)
            total = data.get("total", 0)
            if len(all_data) >= total:
                break
            page += 1
            time.sleep(0.3)
        except Exception as e:
            print(f"  Exception: {e}")
            break
    return all_data

all_smk = []
for prov, city in CITIES:
    print(f"Fetching {city}...")
    records = fetch_all(prov, city)
    print(f"  Got {len(records)} SMK")
    for r in records:
        all_smk.append({
            "npsn": r.get("npsn", ""),
            "nama": r.get("name", ""),
            "provinsi": r.get("province", ""),
            "kota": r.get("city", ""),
            "kecamatan": r.get("district", ""),
            "akreditasi": r.get("accreditation", ""),
            "status": "SWASTA",
        })
    time.sleep(0.5)

print(f"\nTotal SMK di Jabodetabek: {len(all_smk)}")

df = pd.DataFrame(all_smk)

smk_swasta = df[df["status"] == "SWASTA"].copy()
print(f"SMK Swasta: {len(smk_swasta)}")

status_counts = df["status"].value_counts()
print(f"\nPer status:\n{status_counts}")

kota_counts = smk_swasta.groupby("kota").size().sort_values(ascending=False)
print(f"\nSMK Swasta per kota:\n{kota_counts}")

output_file = "smk_swasta_jabodetabek.xlsx"
smk_swasta.to_excel(output_file, index=False, sheet_name="SMK Swasta Jabodetabek")
print(f"\nExported to {output_file}")
print(f"Columns: {list(smk_swasta.columns)}")