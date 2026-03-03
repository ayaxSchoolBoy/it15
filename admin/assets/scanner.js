// Admin QR Scanner logic
// Uses html5-qrcode library (browser only)

const readerId = "reader";
const scanStatus = document.getElementById("scanStatus");
const resultBox = document.getElementById("result");
const startBtn = document.getElementById("startBtn");
const stopBtn = document.getElementById("stopBtn");
const historyBox = document.getElementById("history");
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
const cameraSelect = document.getElementById("cameraSelect");

let html5QrCode = null;
let isScanning = false;

function setStatus(message) {
  scanStatus.textContent = `Status: ${message}`;
}

function renderResult(data) {
  // Clear previous content
  resultBox.innerHTML = "";

  if (!data || data.status === "INVALID") {
    resultBox.innerHTML = `
      <p><strong>Status:</strong>
        <span class="badge invalid">INVALID</span>
      </p>
      <p>${data && data.message ? data.message : "No record found."}</p>
    `;
    return;
  }

  if (data.status === "UNPAID") {
    resultBox.innerHTML = `
      <p><strong>Status:</strong>
        <span class="badge unpaid">UNPAID</span>
      </p>
      <p>${data.message ? data.message : "Payment not approved."}</p>
    `;
    return;
  }

  const statusClass = data.status === "PAID" ? "paid" : data.status === "EXPIRED" ? "expired" : "invalid";

  resultBox.innerHTML = `
    <p><strong>Member Name:</strong> ${data.member_name}</p>
    <p><strong>Plan:</strong> ${data.plan}</p>
    <p><strong>Expiration:</strong> ${data.expiration_date}</p>
    <p><strong>Status:</strong>
      <span class="badge ${statusClass}">${data.status}</span>
    </p>
  `;
}

function renderHistory(items) {
  if (!items || items.length === 0) {
    historyBox.innerHTML = "<p class=\"placeholder\">No history yet.</p>";
    return;
  }

  historyBox.innerHTML = items
    .map((item) => {
      const statusClass = item.result_status === "ACTIVE" ? "active" : item.result_status === "EXPIRED" ? "expired" : "invalid";
      return `
        <div class="history-item">
          <div class="history-row">
            <strong>${item.membership_code}</strong>
            <span class="badge ${statusClass}">${item.result_status}</span>
          </div>
          <div class="history-row">
            <span>${item.scanned_at}</span>
            <span>${item.ip_address}</span>
          </div>
        </div>
      `;
    })
    .join("");
}

async function loadHistory() {
  try {
    const response = await fetch("../api/history.php", {
      headers: {
        "X-CSRF-Token": csrfToken,
      },
    });
    const data = await response.json();
    if (data.status === "OK") {
      renderHistory(data.history);
    }
  } catch (error) {
    console.error(error);
  }
}

async function validateCode(qrValue) {
  try {
    setStatus("Validating QR code...");

    const response = await fetch("../api/validate.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": csrfToken,
      },
      body: JSON.stringify({ qr_value: qrValue }),
    });

    const data = await response.json();
    renderResult(data);
    loadHistory();
    setStatus("Scan complete");
  } catch (error) {
    console.error(error);
    renderResult({ status: "INVALID", message: "Server error." });
    setStatus("Server error");
  }
}

function onScanSuccess(decodedText) {
  if (isScanning) {
    setStatus(`QR detected: ${decodedText}`);
    // Stop scanning temporarily to avoid multiple hits
    html5QrCode.stop().then(() => {
      isScanning = false;
      startBtn.disabled = false;
      stopBtn.disabled = true;
      validateCode(decodedText);
    });
  }
}

async function startScanner() {
  if (!html5QrCode) {
    html5QrCode = new Html5Qrcode(readerId);
  }

  try {
    setStatus("Requesting camera access...");

    const cameraId = cameraSelect?.value || null;
    const cameraConfig = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: "environment" };

    await html5QrCode.start(cameraConfig, { fps: 10, qrbox: 250 }, onScanSuccess, () => {});

    isScanning = true;
    startBtn.disabled = true;
    stopBtn.disabled = false;
    setStatus("Scanning...");
  } catch (err) {
    console.error(err);
    setStatus("Camera access denied or not available.");
  }
}

async function stopScanner() {
  if (html5QrCode && isScanning) {
    await html5QrCode.stop();
    isScanning = false;
    startBtn.disabled = false;
    stopBtn.disabled = true;
    setStatus("Scanner stopped");
  }
}

startBtn.addEventListener("click", startScanner);
stopBtn.addEventListener("click", stopScanner);

// Load history on page load
loadHistory();

async function loadCameras() {
  try {
    const cameras = await Html5Qrcode.getCameras();
    if (!cameraSelect) {
      return;
    }
    if (!cameras || cameras.length === 0) {
      cameraSelect.innerHTML = "<option value=\"\">No cameras found</option>";

      // Try requesting permission, then re-enumerate
      if (navigator.mediaDevices?.getUserMedia) {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: true });
          stream.getTracks().forEach((track) => track.stop());

          const camerasAfter = await Html5Qrcode.getCameras();
          if (camerasAfter && camerasAfter.length > 0) {
            cameraSelect.innerHTML = camerasAfter
              .map((cam) => `<option value="${cam.id}">${cam.label || cam.id}</option>`)
              .join("");
          }
        } catch (err) {
          console.error(err);
        }
      }

      return;
    }

    cameraSelect.innerHTML = cameras
      .map((cam) => `<option value="${cam.id}">${cam.label || cam.id}</option>`)
      .join("");
  } catch (err) {
    console.error(err);
    if (cameraSelect) {
      cameraSelect.innerHTML = "<option value=\"\">Camera list unavailable</option>";
    }
  }
}

loadCameras();

// Auto-start scanner on load (if browser allows)
window.addEventListener("load", () => {
  setStatus("Attempting to start scanner...");
  startScanner().catch(() => {
    setStatus("Click Start Scanner to allow camera access.");
  });
});
