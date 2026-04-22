#!/bin/bash
# Setup script para JARVIS

echo ""
echo "  ╔══════════════════════════════════════╗"
echo "  ║         JARVIS - Setup               ║"
echo "  ╚══════════════════════════════════════╝"
echo ""

# Instalar dependencias Python
echo "[1/3] Instalando dependencias Python..."
pip3 install -r requirements.txt -q
echo "      ✓ sounddevice y numpy instalados"

# Verificar PortAudio (necesario para sounddevice en Linux)
echo ""
echo "[2/3] Verificando PortAudio..."
if ! dpkg -l libportaudio2 &>/dev/null 2>&1; then
    echo "      Instalando libportaudio2..."
    sudo apt-get install -y libportaudio2 2>/dev/null || \
    sudo dnf install -y portaudio 2>/dev/null || \
    echo "      (instala manualmente: sudo apt install libportaudio2)"
else
    echo "      ✓ PortAudio disponible"
fi

# Verificar apps
echo ""
echo "[3/3] Verificando aplicaciones..."
for app in google-chrome chromium-browser chromium firefox; do
    if command -v $app &>/dev/null; then
        echo "      ✓ Navegador: $app"
        break
    fi
done

for app in spotify; do
    if command -v $app &>/dev/null; then
        echo "      ✓ Spotify: $app"
    else
        echo "      ~ Spotify no encontrado (se abrirá en navegador)"
    fi
done

for app in code codium code-insiders; do
    if command -v $app &>/dev/null; then
        echo "      ✓ Editor: $app"
        break
    fi
done

echo ""
echo "  Setup completo. Ejecuta:"
echo "  python3 jarvis.py"
echo ""
