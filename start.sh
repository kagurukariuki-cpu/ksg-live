#!/bin/bash
# KSG Live — Launcher

clear
C='\033[36m'; Y='\033[33m'; G='\033[32m'; R='\033[31m'; D='\033[90m'; N='\033[0m'

echo ""
echo -e "${C}  ╔══════════════════════════════════════════════════════╗${N}"
echo -e "${C}  ║                                                      ║${N}"
echo -e "${C}  ║              ${N}K S G   L I V E${C}                          ║${N}"
echo -e "${C}  ║           ${D}Screen Sharing for the LAN${C}                    ║${N}"
echo -e "${C}  ║                                                      ║${N}"
echo -e "${C}  ╚══════════════════════════════════════════════════════╝${N}"
echo ""

IP=$(hostname -I 2>/dev/null | awk '{print $1}')
if [ -z "$IP" ]; then IP="localhost"; fi

echo -e "  ${G}▶${N} Server starting on port ${Y}8000${N}"
echo ""
echo -e "  ${G}▶${N} URLs to share:"
echo -e "      ${D}Presenter:${N}  ${Y}http://${IP}:8000/?role=presenter&room=class1${N}"
echo -e "      ${D}Viewer:${N}     ${Y}http://${IP}:8000/?role=viewer&room=class1${N}"
echo ""
echo -e "  ${D}Press Ctrl+C to stop the server${N}"
echo ""
echo -e "  ${C}─────────────────────────────────────────────────────${N}"
echo ""

# Run PHP, filter noise, colourise
php -S 0.0.0.0:8000 2>&1 | while IFS= read -r line; do
  case "$line" in
    *"Accepted"*|*"Closing"*) ;;
    *"[200]:"*) echo -e "  ${G}✓${N} ${D}$(date +%H:%M:%S)${N}  ${line#*] }" ;;
    *"[404]:"*) echo -e "  ${R}✗${N} ${D}$(date +%H:%M:%S)${N}  ${line#*] }" ;;
    *"[401]:"*) echo -e "  ${R}✗${N} ${D}$(date +%H:%M:%S)${N}  ${line#*] }" ;;
    *"Development Server"*) ;;
    *) ;;
  esac
done
