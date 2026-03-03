const form = document.getElementById("registerForm");
const resultBox = document.getElementById("result");
const planSelect = document.getElementById("planSelect");

function showResult(html) {
  resultBox.innerHTML = html;
}

function presetPlan() {
  const params = new URLSearchParams(window.location.search);
  const plan = params.get("plan");
  if (!plan) return;

  const allowed = ["Monthly", "Quarterly", "Annual"];
  if (allowed.includes(plan)) {
    planSelect.value = plan;
  }
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();

  const formData = new FormData(form);

  showResult("<p>Processing subscription...</p>");

  try {
    const response = await fetch("api/subscribe.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.status !== "OK") {
      showResult(`<p>${data.message}</p>`);
      return;
    }

    const errorHtml = data.email_error
      ? `<p><strong>Email Error:</strong> ${data.email_error}</p>`
      : "";

    showResult(`
      <p><strong>Subscription ID:</strong> ${data.membership_code}</p>
      <p><strong>Plan:</strong> ${planSelect.value}</p>
      <p><strong>Expiration Date:</strong> ${data.expiration_date}</p>
      <p><strong>Payment Status:</strong> ${data.payment_status}</p>
      <p><strong>Email Status:</strong> ${data.email_status}</p>
      ${errorHtml}
      <img src="${data.qr_url}" alt="Subscription QR Code" />
    `);
  } catch (error) {
    console.error(error);
    showResult("<p>Server error. Please try again.</p>");
  }
});

presetPlan();
