#!/usr/bin/env python3
"""
JARVIS - Sistema de control por aplausos
Doble aplauso -> Abre Chrome, Spotify y VSCode
"""

import json
import os
import subprocess
import sys
import time
from pathlib import Path

try:
    import numpy as np
    import sounddevice as sd
except ImportError:
    print("[ERROR] Faltan dependencias. Ejecuta: pip install sounddevice numpy")
    sys.exit(1)

# ── Colores ANSI ──────────────────────────────────────────────────────────────
R = "\033[91m"
G = "\033[92m"
Y = "\033[93m"
B = "\033[94m"
M = "\033[95m"
C = "\033[96m"
W = "\033[97m"
RESET = "\033[0m"
BOLD = "\033[1m"

BANNER = f"""
{M}{BOLD}
  ╔══════════════════════════════════════╗
  ║         J.A.R.V.I.S  v1.0           ║
  ║   Just A Rather Very Intelligent     ║
  ║          System  (Aplauso)           ║
  ╚══════════════════════════════════════╝
{RESET}"""


def load_config() -> dict:
    config_path = Path(__file__).parent / "config.json"
    with open(config_path) as f:
        return json.load(f)


def find_executable(candidates: list[str]) -> str | None:
    for candidate in candidates:
        parts = candidate.split()
        binary = parts[0]
        result = subprocess.run(["which", binary], capture_output=True, text=True)
        if result.returncode == 0:
            return candidate
    return None


def open_browser(cfg: dict) -> bool:
    app = find_executable(cfg["apps"]["browser"]["candidates"])
    if not app:
        print(f"  {R}[✗] Navegador no encontrado{RESET}")
        return False
    url = cfg["apps"]["browser"]["url"]
    subprocess.Popen(app.split() + [url], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    print(f"  {G}[✓] Chrome/navegador abierto → {url}{RESET}")
    return True


def open_spotify(cfg: dict) -> bool:
    app = find_executable(cfg["apps"]["spotify"]["candidates"])
    playlist_uri = cfg["apps"]["spotify"]["playlist_url"]
    playlist_web = cfg["apps"]["spotify"]["playlist_web_url"]

    if app:
        cmd = app.split() + ["--uri", playlist_uri]
        subprocess.Popen(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        print(f"  {G}[✓] Spotify abierto con playlist{RESET}")
        return True

    # Fallback: abrir en el navegador
    browser = find_executable(cfg["apps"]["browser"]["candidates"])
    if browser:
        subprocess.Popen(browser.split() + [playlist_web],
                         stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        print(f"  {Y}[~] Spotify abierto en navegador (app no encontrada){RESET}")
        return True

    print(f"  {R}[✗] Spotify no encontrado{RESET}")
    return False


def open_editor(cfg: dict) -> bool:
    app = find_executable(cfg["apps"]["editor"]["candidates"])
    if not app:
        print(f"  {R}[✗] Editor no encontrado{RESET}")
        return False
    subprocess.Popen(app.split(), stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    print(f"  {G}[✓] Visual Studio Code abierto{RESET}")
    return True


def trigger_jarvis(cfg: dict):
    print(f"\n{M}{BOLD}  ⚡ JARVIS ACTIVADO ⚡{RESET}")
    print(f"{C}  Iniciando aplicaciones...{RESET}")
    open_browser(cfg)
    time.sleep(0.3)
    open_spotify(cfg)
    time.sleep(0.3)
    open_editor(cfg)
    print(f"{G}{BOLD}  ✔ Todo listo, que empiece el show.{RESET}\n")


class ClapDetector:
    def __init__(self, cfg: dict):
        det = cfg["clap_detection"]
        self.threshold = det["threshold"]
        self.min_gap = det["min_clap_gap_ms"] / 1000
        self.max_gap = det["max_clap_gap_ms"] / 1000
        self.cooldown = det["cooldown_seconds"]
        self.sample_rate = det["sample_rate"]
        self.buffer_size = det["buffer_size"]
        self.clap_count = cfg["trigger"]["clap_count"]

        self.clap_times: list[float] = []
        self.last_trigger = 0.0
        self.in_clap = False
        self.cfg = cfg

    def _rms(self, data: np.ndarray) -> float:
        return float(np.sqrt(np.mean(data ** 2)))

    def process(self, indata: np.ndarray, frames: int, time_info, status):
        if status:
            return

        rms = self._rms(indata)
        now = time.time()

        # Refractory: ignore sound right after a clap to avoid echo
        if self.clap_times and (now - self.clap_times[-1]) < 0.08:
            return

        # Detect onset: RMS crosses threshold
        if rms >= self.threshold and not self.in_clap:
            self.in_clap = True

            # Prune old claps outside the window
            self.clap_times = [t for t in self.clap_times if now - t <= self.max_gap]
            self.clap_times.append(now)

            self._show_clap(rms)

            if self._check_double_clap(now):
                self._fire()

        elif rms < self.threshold * 0.5:
            self.in_clap = False

    def _show_clap(self, rms: float):
        bar = int(rms * 40)
        bar_str = "█" * bar + "░" * max(0, 20 - bar)
        print(f"\r  {Y}[APLAUSO] {bar_str} {rms:.3f}{RESET}", end="", flush=True)

    def _check_double_clap(self, now: float) -> bool:
        if len(self.clap_times) < self.clap_count:
            return False
        recent = self.clap_times[-self.clap_count:]
        gap = recent[-1] - recent[0]
        if self.min_gap <= gap <= self.max_gap:
            # Cooldown check
            if now - self.last_trigger >= self.cooldown:
                return True
        return False

    def _fire(self):
        self.last_trigger = time.time()
        self.clap_times.clear()
        trigger_jarvis(self.cfg)
        print(f"{B}  Escuchando... (doble aplauso para activar){RESET}\n")


def main():
    print(BANNER)
    cfg = load_config()

    det = cfg["clap_detection"]
    sr = det["sample_rate"]
    bs = det["buffer_size"]

    detector = ClapDetector(cfg)

    print(f"{C}  Umbral de aplauso : {detector.threshold}")
    print(f"  Ventana doble     : {int(detector.min_gap*1000)}-{int(detector.max_gap*1000)} ms")
    print(f"  Cooldown          : {detector.cooldown}s")
    print(f"{RESET}")
    print(f"{B}  Escuchando micrófono... (Ctrl+C para salir){RESET}")
    print(f"{B}  Doble aplauso para abrir Chrome, Spotify y VSCode{RESET}\n")

    try:
        with sd.InputStream(
            samplerate=sr,
            blocksize=bs,
            channels=1,
            dtype="float32",
            callback=detector.process,
        ):
            while True:
                time.sleep(0.1)
    except KeyboardInterrupt:
        print(f"\n\n{M}  JARVIS desconectado. Hasta la próxima.{RESET}\n")
    except sd.PortAudioError as e:
        print(f"\n{R}[ERROR] Problema con el micrófono: {e}{RESET}")
        print(f"{Y}  Asegúrate de que el micrófono está conectado y tiene permisos.{RESET}")
        sys.exit(1)


if __name__ == "__main__":
    main()
