<?php
declare(strict_types=1);
session_start();

// EXAMPLE ONLY: set a student_id if you don't already set it in login
if (!isset($_SESSION['student_id'])) {
    $_SESSION['student_id'] = $_GET['student_id'] ?? 'demo_student_001';
}
$studentId = $_SESSION['student_id'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>DSS – Progress Tracker</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; color: #222; }
    header { background:#0b5cff; color:#fff; padding:12px 16px; display:flex; align-items:center; gap:12px; }
    header a.btn { color:#0b5cff; background:#fff; border-radius:6px; padding:6px 10px; text-decoration:none; font-weight:600; }
    main { padding: 16px; max-width: 960px; margin: 0 auto; }
    .section { border:1px solid #e6e8eb; border-radius:10px; margin-bottom:14px; overflow:hidden; }
    .section h3 { margin:0; padding:10px 12px; background:#f6f8fa; display:flex; justify-content:space-between; align-items:center; }
    .criteria { padding:6px 12px 12px; }
    .row { display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px dashed #eee; }
    .row:last-child { border-bottom:0; }
    .tag { font-size:12px; padding:2px 6px; border-radius:4px; }
    .tag.rev { background:#fff1cc; color:#a16900; border:1px solid #ffd266;}
    .tag.done { background:#d1f7c4; color:#216e09; border:1px solid #9fe490;}
    .muted { color:#6b7280; font-size:12px; }
    .spacer { flex:1; }
    .controls { display:flex; gap:10px; align-items:center; }
    .counter { font-weight:600; color:#374151; }
    .pill { border:1px solid #e6e8eb; border-radius:999px; padding:4px 10px; font-size:12px; color:#374151; background:#fff; }
  </style>
</head>
<body>
  <header>
    <strong>HOME</strong>
    <span class="spacer"></span>
    <a href="#" id="btn-load" class="btn">Load Progress</a>
    <a href="#" id="btn-save" class="btn">Save Progress</a>
    <a href="logout.php" class="btn">Logout</a>
  </header>

  <main>
    <div class="muted">Student ID: <?php echo htmlspecialchars($studentId, ENT_QUOTES); ?></div>
    <div id="summary" style="margin:8px 0 16px;"></div>
    <div id="progress-menu" aria-live="polite"></div>
  </main>

  <script>
    const studentId = <?php echo json_encode($studentId); ?>;
    let model = { sections: [] };

    const elMenu = document.getElementById('progress-menu');
    const elSummary = document.getElementById('summary');
    const btnLoad = document.getElementById('btn-load');
    const btnSave = document.getElementById('btn-save');

    btnLoad.addEventListener('click', (e) => { e.preventDefault(); loadProgress(); });
    btnSave.addEventListener('click', (e) => { e.preventDefault(); saveProgress(); });

    async function loadProgress() {
      const res = await fetch(`api/load_progress.php?student_id=${encodeURIComponent(studentId)}`, { credentials: 'same-origin' });
      if (!res.ok) { alert('Failed to load progress'); return; }
      model = await res.json();
      render();
    }

    async function saveProgress() {
      // Optional rule: completed → to_revise false
      model.sections.forEach(sec => sec.criteria.forEach(c => { if (c.completed) c.to_revise = false; }));
      const res = await fetch(`api/save_progress.php?student_id=${encodeURIComponent(studentId)}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({ sections: model.sections })
      });
      if (!res.ok) { alert('Failed to save progress'); return; }
      const data = await res.json();
      if (data.last_updated) {
        const time = new Date(data.last_updated).toLocaleString();
        elSummary.innerHTML = `<span class="pill">Last saved: ${time}</span>`;
      }
    }

    function render() {
      // Summary
      const totals = model.sections.flatMap(s => s.criteria);
      const done = totals.filter(c => c.completed).length;
      const revise = totals.filter(c => c.to_revise).length;
      elSummary.innerHTML = `
        <span class="pill counter">Completed: ${done}</span>
        <span class="pill counter">To revise: ${revise}</span>
        ${model.last_updated ? `<span class="pill">Last saved: ${new Date(model.last_updated).toLocaleString()}</span>` : ''}
      `;

      // Sections + criteria
      elMenu.innerHTML = '';
      model.sections.forEach(section => {
        const sec = document.createElement('section');
        sec.className = 'section';
        sec.innerHTML = `
          <h3>
            <span>${section.title}</span>
            <span class="muted">${section.criteria.filter(c => c.completed).length}/${section.criteria.length} done</span>
          </h3>
          <div class="criteria"></div>
        `;
        const list = sec.querySelector('.criteria');

        section.criteria.forEach(crit => {
          const row = document.createElement('div');
          row.className = 'row';
          row.innerHTML = `
            <div style="min-width:24px; text-align:center;">
              ${crit.completed ? '✅' : (crit.to_revise ? '📝' : '⬜')}
            </div>
            <div style="flex:1 1 auto;">${crit.label}</div>
            <label class="controls"><input type="checkbox" ${crit.to_revise ? 'checked' : ''} /> <span>To revise</span></label>
            <label class="controls"><input type="checkbox" ${crit.completed ? 'checked' : ''} /> <span>Completed</span></label>
          `;

          const [revInput, doneInput] = row.querySelectorAll('input[type="checkbox"]');

          revInput.addEventListener('change', () => {
            crit.to_revise = revInput.checked;
            if (revInput.checked) { crit.completed = false; doneInput.checked = false; }
            render(); // refresh icons/counters
          });
          doneInput.addEventListener('change', () => {
            crit.completed = doneInput.checked;
            if (doneInput.checked) { crit.to_revise = false; revInput.checked = false; }
            render();
          });

          list.appendChild(row);
        });

        elMenu.appendChild(sec);
      });
    }

    // Auto-load on first visit
    loadProgress().catch(console.error);
  </script>
</body>
</html>