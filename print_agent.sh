#!/bin/bash
# =============================================================
# print_agent.sh — ESC/POS Print Queue Agent
# Versi: 2.0
#
# Jalankan di VPS lokal yang terhubung ke printer thermal.
# Cara install sebagai service: lihat bagian SETUP di bawah.
#
# SETUP (Ubuntu/Debian):
#   1. Simpan file ini ke /opt/print-agent/print_agent.sh
#   2. chmod +x /opt/print-agent/print_agent.sh
#   3. Edit KONFIGURASI di bawah
#   4. Install jq: sudo apt install jq -y
#   5. Daftarkan sebagai systemd service (lihat print_agent.service)
# =============================================================

# ─────────────────────────────────────────
# KONFIGURASI — SESUAIKAN INI
# ─────────────────────────────────────────
SERVER_URL="https://imei.kwadev.my.id/api/print"
AGENT_KEY="ganti_dengan_secret_key_yang_kuat_dan_panjang_minimal_32_karakter"
AGENT_ID="agent-$(hostname)-$(echo $$)"   # Unik per proses
PRINTER_DEV="/dev/usb/lp0"
LOG_FILE="/var/log/print_agent.log"
POLL_INTERVAL=2         # detik antar polling
MAX_LOG_SIZE=5242880    # 5MB, rotate otomatis
LOCK_FILE="/tmp/print_agent.lock"
CURL_TIMEOUT=10         # timeout curl dalam detik
# ─────────────────────────────────────────

# ─────── Cegah multiple instance ─────────
if [ -f "$LOCK_FILE" ]; then
    OLD_PID=$(cat "$LOCK_FILE")
    if kill -0 "$OLD_PID" 2>/dev/null; then
        echo "Agent sudah berjalan (PID: $OLD_PID). Keluar."
        exit 1
    fi
fi
echo $$ > "$LOCK_FILE"
trap "rm -f $LOCK_FILE; log 'Agent berhenti.'; exit" INT TERM EXIT

# ─────── Fungsi Logging ───────────────────
log() {
    local MSG="[$(date '+%Y-%m-%d %H:%M:%S')] [$AGENT_ID] $1"
    echo "$MSG" | tee -a "$LOG_FILE"
    # Rotate log jika terlalu besar
    if [ -f "$LOG_FILE" ] && [ $(stat -c%s "$LOG_FILE") -gt $MAX_LOG_SIZE ]; then
        mv "$LOG_FILE" "${LOG_FILE}.bak"
        echo "$MSG" > "$LOG_FILE"
    fi
}

# ─────── Lapor status ke server ──────────
report_done() {
    local JOB_ID="$1"
    local STATUS="$2"    # 'done' atau 'failed'
    local ERROR_MSG="$3"

    local BODY="{\"status\":\"${STATUS}\",\"error_message\":\"${ERROR_MSG}\"}"

    curl -s -X POST "${SERVER_URL}/done/${JOB_ID}" \
        -H "Authorization: Bearer ${AGENT_KEY}" \
        -H "X-Agent-Id: ${AGENT_ID}" \
        -H "Content-Type: application/json" \
        --connect-timeout $CURL_TIMEOUT \
        -d "$BODY" > /dev/null 2>&1
}

# ─────── Format & Cetak ESC/POS ──────────
print_job() {
    local PAYLOAD="$1"
    local JOB_ID="$2"

    # Cek printer tersedia
    if [ ! -c "$PRINTER_DEV" ] && [ ! -e "$PRINTER_DEV" ]; then
        log "ERROR: Printer ${PRINTER_DEV} tidak tersedia atau tidak terhubung!"
        report_done "$JOB_ID" "failed" "Printer device tidak ditemukan: ${PRINTER_DEV}"
        return 1
    fi

    if [ ! -w "$PRINTER_DEV" ]; then
        log "ERROR: Tidak ada permission write ke ${PRINTER_DEV}. Coba: sudo chmod 666 ${PRINTER_DEV}"
        report_done "$JOB_ID" "failed" "Permission denied ke printer device"
        return 1
    fi

    # ── Ekstrak field dari JSON payload ──
    local IMEI=$(echo "$PAYLOAD"       | jq -r '.imei        // "-"')
    local NAMA=$(echo "$PAYLOAD"       | jq -r '.nama        // "-"')
    local NPK=$(echo "$PAYLOAD"        | jq -r '.npk         // "-"')
    local APLIKASI=$(echo "$PAYLOAD"   | jq -r '.aplikasi    // "-"')
    local PT=$(echo "$PAYLOAD"         | jq -r '.pt          // "-"')
    local AFD=$(echo "$PAYLOAD"        | jq -r '.afd         // "-"')
    local POS_TITLE=$(echo "$PAYLOAD"  | jq -r '.pos_title   // "-"')
    local GROUP=$(echo "$PAYLOAD"      | jq -r '.group_asset // "-"')
    local TIPE=$(echo "$PAYLOAD"       | jq -r '.tipe_asset  // "-"')
    local PART=$(echo "$PAYLOAD"       | jq -r '.part_asset  // "-"')
    local JUMLAH=$(echo "$PAYLOAD"     | jq -r '.jumlah      // "-"')
    local ASAL=$(echo "$PAYLOAD"       | jq -r '.asal        // "-"')
    local STATUS=$(echo "$PAYLOAD"     | jq -r '.status_desc // "-"')
    local KERUSAKAN=$(echo "$PAYLOAD"  | jq -r '.kerusakan   // "-"')
    local OPERATOR=$(echo "$PAYLOAD"   | jq -r '.dicetak_oleh// "Sistem"')
    local TIMESTAMP=$(echo "$PAYLOAD"  | jq -r '.timestamp   // "-"')

    # ── ESC/POS Commands ──
    local ESC=$'\x1b'
    local GS=$'\x1d'

    # Initialize printer
    local CMD_INIT="${ESC}@"

    # Alignment: Center
    local CMD_CENTER="${ESC}a\x01"
    # Alignment: Left
    local CMD_LEFT="${ESC}a\x00"

    # Font: Bold ON
    local CMD_BOLD_ON="${ESC}E\x01"
    # Font: Bold OFF
    local CMD_BOLD_OFF="${ESC}E\x00"

    # Font: Double size ON (untuk IMEI besar)
    local CMD_DOUBLE_ON="${ESC}!\x30"
    # Font: Normal
    local CMD_NORMAL="${ESC}!\x00"

    # Line feed
    local LF=$'\n'

    # Cut paper (full cut)
    local CMD_CUT="${GS}V\x41\x03"

    # ── Susun struk 72mm ──
    # Lebar efektif: 32 karakter untuk font normal
    local SEP="--------------------------------"

    {
        printf "%s" "$CMD_INIT"
        printf "%s" "$CMD_CENTER"
        printf "%s" "$CMD_BOLD_ON"
        printf "ASTRA AGRO\n"
        printf "%s" "$CMD_BOLD_OFF"
        printf "Sistem Distribusi Gadget\n"
        printf "%s" "$SEP\n"
        printf "%s" "$CMD_BOLD_ON"
        printf "LABEL GADGET\n"
        printf "%s" "$CMD_BOLD_OFF"
        printf "%s" "$SEP\n"

        # IMEI besar dan bold (center)
        printf "%s" "$CMD_DOUBLE_ON"
        printf "%s\n" "$IMEI"
        printf "%s" "$CMD_NORMAL"
        printf "%s" "$SEP\n"

        # Data dalam format left-aligned, label + nilai
        printf "%s" "$CMD_LEFT"

        # Fungsi baris data (label: nilai)
        print_row() { printf "%-14s: %s\n" "$1" "$2"; }

        print_row "Aplikasi"    "$APLIKASI"
        print_row "PT/AFD"      "${PT}/${AFD}"
        print_row "NPK"         "$NPK"
        print_row "Nama"        "$NAMA"
        print_row "Jabatan"     "$POS_TITLE"

        printf "%s" "$SEP\n"

        print_row "Group Asset"  "$GROUP"
        print_row "Tipe Asset"   "$TIPE"
        print_row "Part Asset"   "$PART"
        print_row "Jumlah"       "$JUMLAH"
        print_row "Asal"         "$ASAL"

        printf "%s" "$SEP\n"

        print_row "Status"       "$STATUS"

        printf "%s" "$SEP\n"

        # Keterangan/kerusakan bisa multi-baris
        printf "Keterangan:\n"
        printf "%s\n" "$KERUSAKAN"

        printf "%s" "$SEP\n"

        # Footer center
        printf "%s" "$CMD_CENTER"
        printf "Dicetak: %s\n" "$TIMESTAMP"
        printf "Oleh: %s\n" "$OPERATOR"
        printf "--- Sistem Gadget BIM-PPS ---\n"

        # Jarak sebelum cut
        printf "\n\n\n"

        # Potong kertas
        printf "%s" "$CMD_CUT"

    } > "$PRINTER_DEV"

    local PRINT_EXIT=$?

    if [ $PRINT_EXIT -eq 0 ]; then
        log "✓ Job #${JOB_ID} berhasil dicetak (IMEI: ${IMEI})"
        report_done "$JOB_ID" "done" ""
        return 0
    else
        log "✗ Job #${JOB_ID} gagal dicetak (exit code: ${PRINT_EXIT})"
        report_done "$JOB_ID" "failed" "Print command gagal dengan exit code ${PRINT_EXIT}"
        return 1
    fi
}

# ─────── MAIN LOOP ───────────────────────
log "=== Print Agent v2.0 dimulai ==="
log "Server : ${SERVER_URL}"
log "Printer: ${PRINTER_DEV}"
log "Polling setiap ${POLL_INTERVAL} detik"

# Cek dependensi
if ! command -v jq &> /dev/null; then
    log "FATAL: 'jq' tidak ditemukan. Install dengan: sudo apt install jq -y"
    exit 1
fi
if ! command -v curl &> /dev/null; then
    log "FATAL: 'curl' tidak ditemukan. Install dengan: sudo apt install curl -y"
    exit 1
fi

CONSECUTIVE_ERRORS=0
MAX_CONSECUTIVE_ERRORS=10

while true; do
    # Polling: ambil job berikutnya dari server
    # Gunakan -w "%{http_code}" untuk mendapatkan status code dan body sekaligus
    RESPONSE_FILE=$(mktemp)
    HTTP_CODE=$(curl -s -o "$RESPONSE_FILE" -w "%{http_code}" \
        -H "Authorization: Bearer ${AGENT_KEY}" \
        -H "X-Agent-Id: ${AGENT_ID}" \
        --connect-timeout $CURL_TIMEOUT \
        "${SERVER_URL}/queue")

    CURL_EXIT=$?
    RESPONSE=$(cat "$RESPONSE_FILE")
    rm -f "$RESPONSE_FILE"

    if [ $CURL_EXIT -ne 0 ]; then
        CONSECUTIVE_ERRORS=$((CONSECUTIVE_ERRORS + 1))
        log "WARNING: Gagal terhubung ke server (curl exit: ${CURL_EXIT}). Percobaan ke-${CONSECUTIVE_ERRORS}."
        sleep $POLL_INTERVAL
        continue
    fi

    # 1. Validasi: Apakah respons adalah JSON yang valid?
    if ! echo "$RESPONSE" | jq -e . > /dev/null 2>&1; then
        log "ERROR: Respons bukan JSON valid (HTTP ${HTTP_CODE}). Isi respons: ${RESPONSE:0:100}..."
        sleep $POLL_INTERVAL
        continue
    fi

    # Reset error counter jika berhasil konek & JSON valid
    CONSECUTIVE_ERRORS=0

    # 2. Ambil panjang array respons
    COUNT=$(echo "$RESPONSE" | jq '. | length' 2>/dev/null)

    if [ "$COUNT" -gt 0 ]; then
        # Ambil elemen pertama (job pertama)
        JOB_ID=$(echo "$RESPONSE"   | jq -r '.[0].id // empty')
        PAYLOAD=$(echo "$RESPONSE"  | jq -r '.[0].text // empty')
        ATTEMPTS=$(echo "$RESPONSE" | jq -r '.[0].attempts // "1"')

        if [ -n "$JOB_ID" ] && [ -n "$PAYLOAD" ]; then
            log "⟶ Mengambil job #${JOB_ID} (percobaan ke-${ATTEMPTS})"
            print_job "$PAYLOAD" "$JOB_ID"
        else
            log "WARNING: Job ID atau Payload kosong pada elemen array pertama."
        fi
    elif [ "$COUNT" -eq 0 ]; then
        # Antrian kosong, normal.
        :
    else
        log "WARNING: Respons tidak dikenal (HTTP ${HTTP_CODE}). Body: ${RESPONSE:0:50}"
    fi

    sleep $POLL_INTERVAL
done
