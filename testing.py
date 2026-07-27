"""
Script FINAL: Ambil data SMK Swasta se-Jabodetabek + daftar jurusan per sekolah
Sumber: smk.kemendikdasmen.go.id (situs resmi Direktorat SMK - Kemendikdasmen)

Alur:
1. Crawl semua halaman list sekolah per provinsi (data-sekolah/list?provinsi=X&page=N)
2. Filter: status == "Swasta" DAN kab/kota termasuk daftar target (Jabodetabek)
3. Untuk tiap sekolah yang lolos filter, buka halaman detailnya dan ambil
   daftar jurusan (Kompetensi Keahlian) dari tabel "Rombongan Belajar per
   Kompetensi dan Tingkat"
4. Export ke Excel

Estimasi waktu: bisa 30-60+ menit tergantung jumlah sekolah & koneksi.
Cara pakai:
    pip install requests beautifulsoup4 pandas lxml openpyxl
    python scrape_smk_swasta_jurusan.py
"""

import requests
from bs4 import BeautifulSoup
import pandas as pd
import time
import re

BASE = "https://smk.kemendikdasmen.go.id"
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
                  "(KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept-Language": "id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7",
}

# Target wilayah Jabodetabek - sesuaikan format nama provinsi & kab/kota
# dengan yang dipakai situs smk.kemendikdasmen.go.id
PROVINSI_TARGET = ["Prov. D.K.I. Jakarta", "Prov. Jawa Barat", "Prov. Banten"]

KABKOTA_TARGET = {
    "Prov. D.K.I. Jakarta": [
        "Kota Jakarta Pusat", "Kota Jakarta Utara", "Kota Jakarta Barat",
        "Kota Jakarta Selatan", "Kota Jakarta Timur", "Kab. Kepulauan Seribu",
    ],
    "Prov. Jawa Barat": [
        "Kota Depok", "Kab. Bogor", "Kota Bogor", "Kota Bekasi", "Kab. Bekasi",
    ],
    "Prov. Banten": [
        "Kab. Tangerang", "Kota Tangerang", "Kota Tangerang Selatan",
    ],
}

REQUEST_DELAY = 0.3  # jeda antar request (detik) - biar sopan ke server


def fetch(url, params=None, retries=3):
    for attempt in range(retries):
        try:
            resp = requests.get(url, params=params, headers=HEADERS, timeout=30)
            if resp.status_code == 200:
                return resp.text
            print(f"    Status {resp.status_code}, retry {attempt+1}/{retries}...")
        except Exception as e:
            print(f"    Error: {e}, retry {attempt+1}/{retries}...")
        time.sleep(1)
    return None


def get_total_sekolah(html):
    m = re.search(r"dari\s*([\d.]+)\s*sekolah", html)
    if m:
        return int(m.group(1).replace(".", ""))
    return None


def parse_list_table(html):
    soup = BeautifulSoup(html, "html.parser")
    tables = soup.find_all("table")
    target = None
    for t in tables:
        first_row_text = t.find("tr").get_text() if t.find("tr") else ""
        if "NPSN" in first_row_text and "Nama SMK" in first_row_text:
            target = t
            break
    rows = []
    if not target:
        return rows

    for tr in target.find_all("tr")[1:]:
        cells = tr.find_all("td")
        if len(cells) < 7:
            continue
        npsn = cells[1].get_text(strip=True)
        nama_cell = cells[2]
        a_tag = nama_cell.find("a")
        nama = a_tag.get_text(strip=True) if a_tag else nama_cell.get_text(strip=True)
        detail_url = a_tag["href"] if a_tag and a_tag.has_attr("href") else None
        status = cells[3].get_text(strip=True)
        alamat = cells[4].get_text(strip=True)
        provinsi = cells[5].get_text(strip=True)
        kabkota = cells[6].get_text(strip=True)
        kec = cells[7].get_text(strip=True) if len(cells) > 7 else ""
        rows.append({
            "npsn": npsn, "nama": nama, "status": status,
            "alamat": alamat, "provinsi": provinsi, "kabkota": kabkota,
            "kecamatan": kec, "detail_url": detail_url,
        })
    return rows


def get_jurusan(detail_url):
    if not detail_url:
        return []
    if detail_url.startswith("/"):
        detail_url = BASE + detail_url
    html = fetch(detail_url)
    if not html:
        return []
    try:
        tables = pd.read_html(html, match="Kompetensi Keahlian")
        if not tables:
            return []
        df = tables[0]
        col_name = [c for c in df.columns if "Kompetensi Keahlian" in str(c)][0]
        jurusan_list = df[col_name].dropna().astype(str).tolist()
        jurusan_list = [j.strip() for j in jurusan_list if j.strip().upper() != "JUMLAH"]
        return jurusan_list
    except Exception as e:
        print(f"    Gagal parse jurusan dari {detail_url}: {e}")
        return []


def crawl_provinsi(provinsi, target_kabkota):
    print(f"\n=== {provinsi} ===")
    html = fetch(f"{BASE}/data-sekolah/list", params={"provinsi": provinsi, "page": 1})
    if not html:
        print("  Gagal fetch halaman 1, skip provinsi ini")
        return []

    total = get_total_sekolah(html)
    total_pages = -(-total // 10) if total else 1
    print(f"  Total sekolah: {total} ({total_pages} halaman)")

    matched = []
    page1_rows = parse_list_table(html)
    matched.extend([r for r in page1_rows if r["kabkota"] in target_kabkota and r["status"] == "Swasta"])

    for page in range(2, total_pages + 1):
        html = fetch(f"{BASE}/data-sekolah/list", params={"provinsi": provinsi, "page": page})
        if html:
            rows = parse_list_table(html)
            matched.extend([r for r in rows if r["kabkota"] in target_kabkota and r["status"] == "Swasta"])
        time.sleep(REQUEST_DELAY)
        if page % 25 == 0:
            print(f"    ...halaman {page}/{total_pages} (sudah ketemu {len(matched)} SMK Swasta target)")

    print(f"  Selesai crawl {provinsi}: {len(matched)} SMK Swasta ketemu di kota target")
    return matched


if __name__ == "__main__":
    all_matched = []
    for provinsi in PROVINSI_TARGET:
        matched = crawl_provinsi(provinsi, KABKOTA_TARGET[provinsi])
        all_matched.extend(matched)

    print(f"\n{'='*60}")
    print(f"Total SMK Swasta se-Jabodetabek (list awal): {len(all_matched)}")
    print("Sekarang ambil data jurusan tiap sekolah...")
    print(f"{'='*60}\n")

    for i, sekolah in enumerate(all_matched, 1):
        jurusan = get_jurusan(sekolah["detail_url"])
        sekolah["jurusan"] = ", ".join(jurusan) if jurusan else ""
        sekolah["jumlah_jurusan"] = len(jurusan)
        if i % 20 == 0 or i == len(all_matched):
            print(f"  Progress jurusan: {i}/{len(all_matched)}")
        time.sleep(REQUEST_DELAY)

    df = pd.DataFrame(all_matched)
    df = df.drop(columns=["detail_url"])  # gak perlu di output final

    output_file = "smk_swasta_jabodetabek_dengan_jurusan.xlsx"
    df.to_excel(output_file, index=False, sheet_name="SMK Swasta + Jurusan")

    print(f"\n{'='*60}")
    print(f"SELESAI. Total {len(df)} SMK Swasta dengan data jurusan.")
    print(f"Disimpan ke: {output_file}")
    print(f"Kolom: {list(df.columns)}")