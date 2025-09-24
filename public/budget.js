// ===== SUOMEKSI: Render-palvelun PHP-endpoint =====
const API_BASE = "https://websitecv-nrv1.onrender.com/save-budget.php";

const incomeEl = document.getElementById('income');
const sumIncomeEl = document.getElementById('sumIncome');
const sumCostsEl = document.getElementById('sumCosts');
const leftoverEl = document.getElementById('leftover');
const savePctEl = document.getElementById('savePct');
const saveBtn = document.getElementById('saveBtn');
const statusEl = document.getElementById('status');

function addRow(containerId, name="", value=0) {
  const container = document.getElementById(containerId);
  const row = document.createElement('div');
  row.className = "row";
  row.innerHTML = `
    <input type="text" value="${name}" placeholder="Name"/>
    <input type="number" value="${value}" />
    <button onclick="this.parentElement.remove(); recalc()">–</button>
  `;
  container.appendChild(row);
}

function readRows(containerId) {
  const container = document.getElementById(containerId);
  const rows = [...container.querySelectorAll('.row')];
  return rows.map(r => {
    const [nameI, valI] = r.querySelectorAll('input');
    return { name: nameI.value.trim() || "(unnamed)", value: Number(valI.value||0) };
  });
}

function recalc() {
  const income = Number(incomeEl.value||0);
  const fixed = readRows('fixed');
  const variable = readRows('variable');
  const costs = [...fixed, ...variable].reduce((a,b)=>a+b.value, 0);
  const left = income - costs;
  const pct = income>0 ? Math.round((left/income)*100) : 0;

  sumIncomeEl.textContent = income.toFixed(0);
  sumCostsEl.textContent = costs.toFixed(0);
  leftoverEl.textContent = left.toFixed(0);
  savePctEl.textContent = pct;

  saveBtn.disabled = !(income > 0 || costs > 0);
}

incomeEl.addEventListener('input', recalc);

saveBtn.onclick = async () => {
  statusEl.textContent = "Saving…";
  const payload = {
    meta: {
      type: "budget",
      createdAt: new Date().toISOString(),
      userAgent: navigator.userAgent
    },
    income: Number(incomeEl.value||0),
    fixed: readRows('fixed'),
    variable: readRows('variable'),
    sums: {
      costs: Number(sumCostsEl.textContent),
      leftover: Number(leftoverEl.textContent),
      savePct: Number(savePctEl.textContent)
    }
  };
  try {
    const res = await fetch(API_BASE, {
      method: "POST",
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if(!res.ok) throw new Error(data.error || "Save failed");
    statusEl.textContent = `✅ Saved: ${data.path}`;
  } catch(e) {
    statusEl.textContent = `❌ Error: ${e.message}`;
  }
};

// Alkuarvot
addRow('fixed','Rent',0);
addRow('fixed','Internet',0);
addRow('variable','Food',0);
addRow('variable','Transport',0);
recalc();
