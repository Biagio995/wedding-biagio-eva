#!/usr/bin/env python3
"""
Genera CSS puro (box-shadow pixel art) dal PNG del monogramma.
Esegui dalla cartella laravel: python tools/generate-monogram-pixel-css.py
"""
from __future__ import annotations

import math
import os
import sys

from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PNG = os.path.join(ROOT, "public", "images", "wedding-monogram.png")
OUT = os.path.join(ROOT, "resources", "css", "site", "wedding-monogram-pixels.css")

TARGET_W, TARGET_H = 550, 393
MAX_INK_PIXELS = 9000
# Solo pixel chiaramente diversi dal fondo (tratti bronzo), non il rumore della carta
MIN_DIST_FROM_BG = 42


def dist(a: tuple[int, int, int], b: tuple[int, int, int]) -> float:
    return math.sqrt(sum((x - y) ** 2 for x, y in zip(a, b)))


def lum(rgb: tuple[int, int, int]) -> float:
    r, g, b = rgb
    return 0.299 * r + 0.587 * g + 0.114 * b


def is_ink(rgb: tuple[int, int, int], bg: tuple[int, int, int]) -> bool:
    """Tratti più scuri del fondo + distanza sufficiente (esclude alone anti-alias chiaro)."""
    if dist(rgb, bg) < MIN_DIST_FROM_BG:
        return False
    return lum(rgb) < lum(bg) - 6.0


def bg_color(img: Image.Image) -> tuple[int, int, int]:
    w, h = img.size
    samples = [
        img.getpixel((0, 0))[:3],
        img.getpixel((w - 1, 0))[:3],
        img.getpixel((0, h - 1))[:3],
        img.getpixel((w - 1, h - 1))[:3],
    ]
    return (
        sum(s[0] for s in samples) // 4,
        sum(s[1] for s in samples) // 4,
        sum(s[2] for s in samples) // 4,
    )


def hex_rgb(t: tuple[int, int, int]) -> str:
    return f"#{t[0]:02x}{t[1]:02x}{t[2]:02x}"


def build_css(im: Image.Image, bg: tuple[int, int, int]) -> str:
    w, h = im.size
    ink: list[tuple[int, int, tuple[int, int, int]]] = []
    for y in range(h):
        for x in range(w):
            rgb = im.getpixel((x, y))[:3]
            if is_ink(rgb, bg):
                ink.append((x, y, rgb))

    if not ink:
        raise RuntimeError("Nessun pixel inchiostro")

    ink.sort(key=lambda p: (p[1], p[0]))
    ax, ay, ac = ink[0]
    c0 = hex_rgb(ac)

    rest: list[str] = []
    for x, y, rgb in ink[1:]:
        rest.append(f"{x - ax}px {y - ay}px 0 0 {hex_rgb(rgb)}")

    shadows = ",\n    ".join(rest) if rest else "none"
    sx = TARGET_W / w
    sy = TARGET_H / h

    return f"""/* AUTO-GENERATO: python tools/generate-monogram-pixel-css.py */
.wedding-monogram__pixel-art {{
    position: relative;
    width: min({TARGET_W}px, 100%);
    aspect-ratio: {TARGET_W} / {TARGET_H};
    margin-inline: auto;
    overflow: visible;
}}

.wedding-monogram__pixel-art::before {{
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 1px;
    height: 1px;
    background-color: {c0};
    box-shadow:
    {shadows};
    transform: scale({sx}, {sy});
    transform-origin: 0 0;
    pointer-events: none;
}}
"""


def main() -> int:
    if not os.path.isfile(PNG):
        print(f"Manca: {PNG}", file=sys.stderr)
        return 1

    im0 = Image.open(PNG).convert("RGBA")
    scale = 1.0
    while scale > 0.03:
        w = max(1, int(im0.width * scale))
        h = max(1, int(im0.height * scale))
        im = im0.resize((w, h), Image.Resampling.LANCZOS)
        bg = bg_color(im)
        n = sum(
            1
            for y in range(h)
            for x in range(w)
            if is_ink(im.getpixel((x, y))[:3], bg)
        )
        if n <= MAX_INK_PIXELS:
            css = build_css(im, bg)
            os.makedirs(os.path.dirname(OUT), exist_ok=True)
            header = f"/* Monogramma pixel CSS: {w}x{h}, ~{n} px inchiostro, scala={scale:.4f} */\n"
            with open(OUT, "w", encoding="utf-8") as f:
                f.write(header + css)
            print(f"OK -> {OUT} ({w}x{h}, {n} px)")
            return 0
        scale *= 0.88

    print("Impossibile rientrare nel limite pixel.", file=sys.stderr)
    return 1


if __name__ == "__main__":
    raise SystemExit(main())
