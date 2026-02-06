<script src="css-script.js"></script>
<section>
<h1>8.4.1 How Confidentiality, Integrity, and Availability Interrelate</h1>
<p class="note">Click each part of the CIA triad to learn how they connect and depend on each other.</p>

<div class="accordion" id="cia-841">

  <!-- CONFIDENTIALITY -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Confidentiality</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Ensuring data is kept private</strong> — only authorised users, systems, or processes can access the information.</li>
        <li><strong>Access control</strong> (permissions, passwords, MFA) is essential to maintaining privacy.</li>
        <li><strong>Relationship to Integrity</strong> — strong confidentiality measures help prevent unauthorised modifications, supporting data integrity.</li>
      </ul>
    </div>
  </div>

  <!-- INTEGRITY -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Integrity</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Ensures data has not been tampered with</strong> — information must remain accurate, reliable, and unchanged unless modified by authorised users.</li>
        <li>Checksums, hashing, digital signatures, audit trails, and access controls help guarantee integrity.</li>
        <li><strong>Relationship to Availability</strong> — data that is corrupted or altered becomes unusable, reducing availability.</li>
        <li><strong>Relationship to Confidentiality</strong> — preventing unauthorised access reduces the chance of unauthorised edits.</li>
      </ul>
    </div>
  </div>

  <!-- AVAILABILITY -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Availability</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Ensures data and systems are available when needed</strong> — users must be able to access information easily and reliably.</li>
        <li>Requires working hardware, maintained servers, backups, redundancy, and protection from outages or attacks.</li>
        <li><strong>Relationship to Integrity</strong> — if data is corrupted or tampered with, it is effectively unavailable.</li>
      </ul>
    </div>
  </div>

</div>

<p class="note"><em>Summary:</em> Confidentiality protects access, integrity protects correctness, and availability ensures usefulness — and each one supports the others.</p>

</main>


</section>