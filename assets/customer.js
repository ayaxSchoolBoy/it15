const membershipBox = document.getElementById("membershipDetails");
const isAuthed = document.body?.dataset?.auth === "1";

function renderMembership(data) {
  if (!data || data.status !== "OK") {
    membershipBox.innerHTML = `<p class="placeholder">${data?.message || "No membership found."}</p>`;
    return;
  }

  membershipBox.innerHTML = `
    <p><strong>Membership Code:</strong> ${data.membership_code}</p>
    <p><strong>Plan:</strong> ${data.plan}</p>
    <p><strong>Expiration Date:</strong> ${data.expiration_date}</p>
    <p><strong>Status:</strong> ${data.status}</p>
    <img src="${data.qr_url}" alt="Membership QR Code" />
  `;
}

async function loadMembership() {
  try {
    const response = await fetch("api/customer_membership.php");
    const data = await response.json();
    renderMembership(data);
  } catch (error) {
    console.error(error);
    membershipBox.innerHTML = "<p class=\"placeholder\">Failed to load membership.</p>";
  }
}

if (isAuthed) {
  loadMembership();
}
