#!/usr/bin/env python3
"""Unduh PDF ebook BacaGratis per kategori.

Tanpa dependency eksternal (hanya stdlib).

Contoh:
    python scrape_bacagratis.py                     # kategori education
    python scrape_bacagratis.py --category novel
    python scrape_bacagratis.py --out unduhan_pdf
"""

import argparse
import json
import os
import re
import sys
import urllib.parse
import urllib.request

SUPA_URL = "https://bejicowolnkzvrwspwki.supabase.co"
ANON_KEY = (
    "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"
    ".eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJlamljb3dvbG5renZyd3Nwd2tpIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzMyNjQxNDgsImV4cCI6MjA4ODg0MDE0OH0"
    ".cggw3uHo_jW9WloiiEPgLZhUb0gWT-7ZGLm128HHJWM"
)
REST_HEADERS = {
    "apikey": ANON_KEY,
    "Authorization": f"Bearer {ANON_KEY}",
    "User-Agent": "Mozilla/5.0 (scraper)",
}


def http_get_json(url: str) -> list:
    req = urllib.request.Request(url, headers=REST_HEADERS)
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read())


def download_pdf(url: str, dest: str) -> None:
    req = urllib.request.Request(url, headers={"User-Agent": REST_HEADERS["User-Agent"]})
    with urllib.request.urlopen(req, timeout=120) as resp, open(dest, "wb") as fh:
        while True:
            chunk = resp.read(1024 * 256)
            if not chunk:
                break
            fh.write(chunk)


def sanitize_filename(name: str) -> str:
    name = re.sub(r'[\\/:*?"<>|]', " ", name)
    name = re.sub(r"\s+", " ", name).strip(" .")
    return name[:150] if name else "tanpa-judul"


def list_ebooks(category_id: str, limit: int = 1000) -> list:
    books = []
    offset = 0
    while True:
        qs = urllib.parse.urlencode({
            "select": "id,title,author",
            "category_id": f"eq.{category_id}",
            "is_visible": "eq.true",
            "order": "title.asc",
            "limit": str(limit),
            "offset": str(offset),
        })
        rows = http_get_json(f"{SUPA_URL}/rest/v1/ebooks?{qs}")
        books.extend(rows)
        if len(rows) < limit:
            break
        offset += limit
    return books


def main() -> int:
    ap = argparse.ArgumentParser(description="Unduh PDF ebook BacaGratis.")
    ap.add_argument("--category", default="education", help="slug kategori (default: education)")
    ap.add_argument("--out", default="bacagratis_pdf", help="folder tujuan (default: bacagratis_pdf)")
    args = ap.parse_args()

    print(f"[1/3] Mencari kategori '{args.category}'...")
    rows = http_get_json(f"{SUPA_URL}/rest/v1/categories?select=id,slug&slug=eq.{urllib.parse.quote(args.category)}")
    if not rows:
        print(f"Kategori '{args.category}' tidak ditemukan.")
        return 1
    category_id = rows[0]["id"]

    print(f"[2/3] Mengambil daftar ebook kategori '{args.category}'...")
    ebooks = list_ebooks(category_id)
    if not ebooks:
        print("Tidak ada ebook di kategori ini.")
        return 0

    os.makedirs(args.out, exist_ok=True)
    print(f"[3/3] Mengunduh {len(ebooks)} PDF ke '{args.out}/'...")

    ok = skipped = failed = 0
    for i, book in enumerate(ebooks, 1):
        title = sanitize_filename(book.get("title") or book["id"])
        dest = os.path.join(args.out, f"{title}.pdf")
        if os.path.exists(dest):
            skipped += 1
            print(f"  [{i}/{len(ebooks)}] Lewati (sudah ada): {title}")
            continue
        url = f"{SUPA_URL}/functions/v1/proxy-pdf?id={book['id']}&download=1"
        try:
            download_pdf(url, dest)
            ok += 1
            print(f"  [{i}/{len(ebooks)}] OK: {title}")
        except Exception as exc:  # noqa: BLE001
            failed += 1
            if os.path.exists(dest):
                os.remove(dest)
            print(f"  [{i}/{len(ebooks)}] GAGAL: {title} ({exc})")

    print(f"\nSelesai. {ok} terunduh, {skipped} dilewati, {failed} gagal.")
    return 0 if failed == 0 else 2


if __name__ == "__main__":
    sys.exit(main())
