const tableBody = document.querySelector("#membersTable tbody");
const searchInput = document.getElementById("searchInput");
const searchBtn = document.getElementById("searchBtn");
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

function renderRows(rows) {
  if (!rows || rows.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="6" class="placeholder">No results.</td></tr>`;
    return;
  }

  tableBody.innerHTML = rows
    .map((row) => {
      const statusClass = row.status === "ACTIVE" ? "active" : "expired";
      const nextStatus = row.status === "ACTIVE" ? "INACTIVE" : "ACTIVE";
      const actionLabel = row.status === "ACTIVE" ? "Deactivate" : "Activate";

      return `
        <tr>
          <td>${row.membership_code}</td>
          <td>${row.member_name}</td>
          <td>${row.plan}</td>
          <td>${row.expiration_date}</td>
          <td><span class="badge ${statusClass}">${row.status}</span></td>
          <td>
            <button class="btn-secondary" data-code="${row.membership_code}" data-status="${nextStatus}">${actionLabel}</button>
          </td>
        </tr>
      `;
    })
    .join("");
}

async function loadMembers(query = "") {
  tableBody.innerHTML = `<tr><td colspan="6" class="placeholder">Loading...</td></tr>`;

  const response = await fetch(`../api/members.php?search=${encodeURIComponent(query)}`, {
    headers: {
      "X-CSRF-Token": csrfToken,
    },
  });
  const data = await response.json();
  if (data.status === "OK") {
    renderRows(data.members);
  }
}

async function updateStatus(code, status) {
  const response = await fetch("../api/members.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": csrfToken,
    },
    body: JSON.stringify({ membership_code: code, status }),
  });

  const data = await response.json();
  if (data.status === "OK") {
    loadMembers(searchInput.value.trim());
  } else {
    alert(data.message || "Update failed.");
  }
}

searchBtn.addEventListener("click", () => {
  loadMembers(searchInput.value.trim());
});

searchInput.addEventListener("keyup", (e) => {
  if (e.key === "Enter") {
    loadMembers(searchInput.value.trim());
  }
});

document.addEventListener("click", (e) => {
  const target = e.target;
  if (target.matches("button[data-code]")) {
    updateStatus(target.dataset.code, target.dataset.status);
  }
});

loadMembers();
