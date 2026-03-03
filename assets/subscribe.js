const form = document.getElementById("subscribeForm");
const resultBox = document.getElementById("result");
const isAuthed = document.body?.dataset?.auth === "1";
const planInput = document.getElementById("planInput");
const planCards = document.querySelectorAll(".plan-card");

function setActivePlan(card) {
  planCards.forEach((item) => item.classList.remove("selected"));
  card.classList.add("selected");
  if (planInput) {
    planInput.value = card.dataset.plan || "Monthly";
  }
}

if (planCards.length) {
  setActivePlan(planCards[0]);
  planCards.forEach((card) => {
    card.addEventListener("click", () => {
      if (!isAuthed) return;
      setActivePlan(card);
    });
  });
}

function showResult(html) {
  resultBox.innerHTML = html;
}

if (form && isAuthed) {
form.addEventListener("submit", async (event) => {
  event.preventDefault();

  const formData = new FormData(form);

  const plan = formData.get("plan");
  const confirmed = window.confirm(`Confirm subscription for ${plan} plan?`);
  if (!confirmed) {
    return;
  }

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
      <p><strong>Membership Code:</strong> ${data.membership_code}</p>
      <p><strong>Expiration Date:</strong> ${data.expiration_date}</p>
      <p><strong>Email Status:</strong> ${data.email_status}</p>
      ${errorHtml}
      <img src="${data.qr_url}" alt="Membership QR Code" />
    `);
  } catch (error) {
    console.error(error);
    showResult("<p>Server error. Please try again.</p>");
  }
});
}
