#!/usr/bin/env python3
"""
JARVIS - Sistema de control por aplausos
Doble aplauso -> Abre Chrome, Spotify y VSCode
Compatible con Windows, Linux y Mac
"""

import json
import os
import platform
import shutil
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

IS_WINDOWS = platform.system() == "Windows"
IS_MAC     = platform.system() == "Darwin"

# ── Habilitar colores ANSI en Windows 10+ ─────────────────────────────────────
if IS_WINDOWS:
    try:
        import ctypes
        ctypes.windll.kernel32.SetConsoleMode(
            ctypes.windll.kernel32.GetStdHandle(-11), 7
        )
    except Exception:
        pass

R = "\033[91m"
G = "\033[92m"
Y = "\033[93m"
B = "\033[94m"
M = "\033[95m"
C = "\033[96m"
RESET = "\033[0m"
BOLD  = "\033[1m"

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
    with open(config_path, encoding="utf-8") as f:
        return json.load(f)


# ── Rutas típicas de Windows ───────────────────────────────────────────────────
WINDOWS_CHROME_PATHS = [
    r"C:\Program Files\Google\Chrome\Application\chrome.exe",
    r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
    os.path.expandvars(r"%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe"),
]

WINDOWS_SPOTIFY_PATHS = [
    os.path.expandvars(r"%APPDATA%\Spotify\Spotify.exe"),
    os.path.expandvars(r"%LOCALAPPDATA%\Microsoft\WindowsApps\Spotify.exe"),
]

WINDOWS_VSCODE_PATHS = [
    os.path.expandvars(r"%LOCALAPPDATA%\Programs\Microsoft VS Code\Code.exe"),
    r"C:\Program Files\Microsoft VS Code\Code.exe",
    r"C:\Program Files (x86)\Microsoft VS Code\Code.exe",
]


def find_windows_app(paths: list[str]) -> str | None:
    for p in paths:
        if os.path.exists(p):
            return p
    return None


def launch(cmd: list[str]):
    """Lanza un proceso sin ventana de consola extra en Windows."""
    kwargs = {"stdout": subprocess.DEVNULL, "stderr": subprocess.DEVNULL}
    if IS_WINDOWS:
        kwargs["creationflags"] = subprocess.CREATE_NO_WINDOW
    subprocess.Popen(cmd, **kwargs)


# ── Abrir aplicaciones ─────────────────────────────────────────────────────────

def open_browser(cfg: dict) -> bool:
    url = cfg["apps"]["browser"]["url"]

    if IS_WINDOWS:
        # Buscar Chrome instalado
        exe = find_windows_app(WINDOWS_CHROME_PATHS)
        if not exe:
            exe = shutil.which("chrome") or shutil.which("msedge")
        if exe:
            launch([exe, url])
        else:
            # Fallback: abrir con el navegador predeterminado
            os.startfile(url)
        print(f"  {G}[✓] Navegador abierto{RESET}")
        return True

    if IS_MAC:
        launch(["open", "-a", "Google Chrome", url])
        print(f"  {G}[✓] Chrome abierto{RESET}")
        return True

    # Linux
    for candidate in cfg["apps"]["browser"]["candidates"]:
        binary = candidate.split()[0]
        if shutil.which(binary):
            launch(candidate.split() + [url])
            print(f"  {G}[✓] Navegador abierto{RESET}")
            return True

    print(f"  {R}[✗] Navegador no encontrado{RESET}")
    return False


def open_spotify(cfg: dict) -> bool:
    uri     = cfg["apps"]["spotify"]["playlist_url"]
    web_url = cfg["apps"]["spotify"]["playlist_web_url"]

    if IS_WINDOWS:
        exe = find_windows_app(WINDOWS_SPOTIFY_PATHS)
        if exe:
            launch([exe, "--uri", uri])
            print(f"  {G}[✓] Spotify abierto con playlist{RESET}")
            return True
        # Fallback: el URI abre Spotify si está instalado vía Store
        try:
            os.startfile(uri)
            print(f"  {G}[✓] Spotify abierto con playlist{RESET}")
            return True
        except Exception:
            os.startfile(web_url)
            print(f"  {Y}[~] Spotify abierto en navegador{RESET}")
            return True

    if IS_MAC:
        launch(["open", uri])
        print(f"  {G}[✓] Spotify abierto con playlist{RESET}")
        return True

    # Linux
    if shutil.which("spotify"):
        launch(["spotify", "--uri", uri])
        print(f"  {G}[✓] Spotify abierto con playlist{RESET}")
        return True
    # Flatpak
    if shutil.which("flatpak"):
        launch(["flatpak", "run", "com.spotify.Client", "--uri", uri])
        print(f"  {G}[✓] Spotify (flatpak) abierto{RESET}")
        return True

    print(f"  {Y}[~] Spotify no encontrado, abriendo en navegador{RESET}")
    open_browser_url(web_url, cfg)
    return True


def open_browser_url(url: str, cfg: dict):
    if IS_WINDOWS:
        os.startfile(url)
    elif IS_MAC:
        launch(["open", url])
    else:
        for c in cfg["apps"]["browser"]["candidates"]:
            if shutil.which(c.split()[0]):
                launch(c.split() + [url])
                return


def open_editor(cfg: dict) -> bool:
    if IS_WINDOWS:
        # Primero intentar 'code' en el PATH (más común)
        if shutil.which("code"):
            launch(["code"])
            print(f"  {G}[✓] Visual Studio Code abierto{RESET}")
            return True
        exe = find_windows_app(WINDOWS_VSCODE_PATHS)
        if exe:
            launch([exe])
            print(f"  {G}[✓] Visual Studio Code abierto{RESET}")
            return True
        print(f"  {R}[✗] VSCode no encontrado{RESET}")
        return False

    if IS_MAC:
        launch(["open", "-a", "Visual Studio Code"])
        print(f"  {G}[✓] Visual Studio Code abierto{RESET}")
        return True

    # Linux
    for candidate in cfg["apps"]["editor"]["candidates"]:
        if shutil.which(candidate.split()[0]):
            launch(candidate.split())
            print(f"  {G}[✓] Visual Studio Code abierto{RESET}")
            return True

    print(f"  {R}[✗] Editor no encontrado{RESET}")
    return False


def trigger_jarvis(cfg: dict):
    print(f"\n{M}{BOLD}  ⚡ JARVIS ACTIVADO ⚡{RESET}")
    print(f"{C}  Iniciando aplicaciones...{RESET}")
    open_browser(cfg)
    time.sleep(0.4)
    open_spotify(cfg)
    time.sleep(0.4)
    open_editor(cfg)
    print(f"{G}{BOLD}  ✔ Todo listo, que empiece el show.{RESET}\n")


# ── Detector de aplausos ───────────────────────────────────────────────────────

class ClapDetector:
    def __init__(self, cfg: dict):
        det = cfg["clap_detection"]
        self.threshold   = det["threshold"]
        self.min_gap     = det["min_clap_gap_ms"] / 1000
        self.max_gap     = det["max_clap_gap_ms"] / 1000
        self.cooldown    = det["cooldown_seconds"]
        self.clap_count  = cfg["trigger"]["clap_count"]

        self.clap_times: list[float] = []
        self.last_trigger = 0.0
        self.in_clap      = False
        self.cfg          = cfg

    def _rms(self, data: np.ndarray) -> float:
        return float(np.sqrt(np.mean(data ** 2)))

    def process(self, indata: np.ndarray, frames: int, time_info, status):
        if status:
            return
        rms = self._rms(indata)
        now = time.time()

        # Periodo refractario: ignora el eco justo después del aplauso
        if self.clap_times and (now - self.clap_times[-1]) < 0.08:
            return

        if rms >= self.threshold and not self.in_clap:
            self.in_clap = True
            self.clap_times = [t for t in self.clap_times if now - t <= self.max_gap]
            self.clap_times.append(now)
            self._show_clap(rms)
            if self._check_double_clap(now):
                self._fire()

        elif rms < self.threshold * 0.5:
            self.in_clap = False

    def _show_clap(self, rms: float):
        bar = "█" * int(rms * 40) + "░" * max(0, 20 - int(rms * 40))
        print(f"\r  {Y}[APLAUSO] {bar} {rms:.3f}{RESET}", end="", flush=True)

    def _check_double_clap(self, now: float) -> bool:
        if len(self.clap_times) < self.clap_count:
            return False
        recent = self.clap_times[-self.clap_count:]
        gap = recent[-1] - recent[0]
        return (self.min_gap <= gap <= self.max_gap) and (now - self.last_trigger >= self.cooldown)

    def _fire(self):
        self.last_trigger = time.time()
        self.clap_times.clear()
        trigger_jarvis(self.cfg)
        print(f"{B}  Escuchando... (doble aplauso para activar){RESET}\n")


# ── Main ───────────────────────────────────────────────────────────────────────

def main():
    print(BANNER)
    cfg = load_config()
    det = cfg["clap_detection"]

    detector = ClapDetector(cfg)

    so = platform.system()
    print(f"{C}  Sistema          : {so}")
    print(f"  Umbral aplauso   : {detector.threshold}")
    print(f"  Ventana doble    : {int(detector.min_gap*1000)}-{int(detector.max_gap*1000)} ms")
    print(f"  Cooldown         : {detector.cooldown}s{RESET}\n")
    print(f"{B}  Escuchando micrófono... (Ctrl+C para salir)")
    print(f"  Da un DOBLE APLAUSO para abrir Chrome, Spotify y VSCode{RESET}\n")

    try:
        with sd.InputStream(
            samplerate=det["sample_rate"],
            blocksize=det["buffer_size"],
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
        print(f"{Y}  Verifica que el micrófono esté conectado y tenga permisos.{RESET}")
        sys.exit(1)


if __name__ == "__main__":
    main()
