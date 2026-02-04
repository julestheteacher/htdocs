    <h1>HOME</h1>
    <ul id="topics"></ul>
    <div id="remaining"></div>
    <button id="saveBtn">Save Progress</button>
    <button id="loadBtn">Load Progress</button>
    <button id="logoutBtn" onclick="location.href='/index_includes/logout.php'">Logout</button>
    <input type="file" id="fileInput" accept=".json">

    <script>
      // Array of topics consisting of the names, and their redirect locations relative to webroot.
      const topics = [
        { name: "Problem Solving", href: "/problem/problem-index.php" },
        { name: "Business Environment", href: "/business/business-index.php" },
        { name: "Data and Data Analysis", href: "/data/data-index.php" },
        { name: "Digital Environment", href: "/digital/digital-index.php" },
        { name: "Emerging Issues", href: "/emerging/emerging-index.php" },
        { name: "Legislation", href: "/legislation/legislation-index.php" },
        { name: "Security", href: "/security/security-index.php" }
      ];

      // Make a clone of topics but add "done: false" to all of them by default
      let progress = topics.map(t => ({ name: t.name, href: t.href, done: false }));

      const topicsList = document.getElementById("topics");
      const remainingDiv = document.getElementById("remaining");
      const saveBtn = document.getElementById("saveBtn");
      const loadBtn = document.getElementById("loadBtn");
      const fileInput = document.getElementById("fileInput");

      function render() {
        topicsList.innerHTML = "";
        let completed = 0;

        progress.forEach((topic, index) => {
          const li = document.createElement("li");

          const a = document.createElement("a");
          a.textContent = topic.name;
          a.href = topic.href;
          a.target = "_blank";
          li.appendChild(a);

          const status = document.createElement("span");
          status.className = "status";
          // Ternary operator same as writing "if(topic.done == true){status.textContent = "✔"} else{status.textContent = "✖"}"
          status.textContent = topic.done ? "✔" : "✖";
          status.classList.remove("done", "incomplete");
          status.classList.add(topic.done ? "done" : "incomplete");
          li.appendChild(status);

          status.addEventListener("click", () => toggleTopic(index));
          topicsList.appendChild(li);

          if (topic.done) completed++;
        });

        remainingDiv.textContent = `Progress: ${completed} of ${progress.length} completed`;
      }

      function toggleTopic(index) {
        progress[index].done = !progress[index].done;
        render();
      }

      function saveProgress() {
        const filename = `revision-progress-${new Date().toISOString().replace(/[:.]/g, "-")}.json`;
        const blob = new Blob([JSON.stringify(progress, null, 2)], { type: "application/json" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
      }

      function handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
          try {
            progress = JSON.parse(e.target.result);
            render();
            alert("Progress loaded successfully!");
          } catch {
            alert("Invalid file format. Please upload a valid JSON file.");
          }
        };
        reader.readAsText(file);
      }

      saveBtn.addEventListener("click", saveProgress);
      loadBtn.addEventListener("click", () => fileInput.click());
      fileInput.addEventListener("change", handleFileUpload);

      window.onload = render;
    </script>