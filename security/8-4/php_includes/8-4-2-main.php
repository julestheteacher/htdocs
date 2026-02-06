<script src="css-script.js"></script>
<section>

<h1>8.4.2 Elements of the IAAA Model</h1>
<p class="note">Click each section to explore how Identification, Authentication, Authorisation and Accountability work in digital systems.</p>

<div class="accordion" id="iaaa-842">

  <!-- Identification -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Identification</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Recognising the individual</strong> — the system identifies who the user claims to be.</li>
        <li><strong>Knowledge‑based identification</strong> — such as entering a username.</li>
        <li><strong>Possession‑based methods</strong> — ID cards, access tokens, registered devices.</li>
        <li><strong>Biometric‑based methods</strong> — fingerprints, facial recognition, iris scans.</li>
        <li><strong>Benefit:</strong> multiple identification methods increase flexibility.</li>
        <li><strong>Drawback:</strong> some identification factors can be shared, stolen or guessed.</li>
      </ul>
    </div>
  </div>

  <!-- Authentication -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Authentication</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Verifying the identity provided during identification</strong> — confirming the user truly is who they claim to be.</li>
        <li><strong>Multi‑factor authentication (MFA)</strong> — combines knowledge, possession, and biometrics.</li>
        <li><strong>Passwords and passphrases</strong> — traditional but vulnerable if weak or reused.</li>
        <li><strong>Biometric authentication</strong> — stronger but raises privacy concerns.</li>
        <li><strong>Benefit:</strong> reduces the likelihood of impersonation.</li>
        <li><strong>Drawback:</strong> stronger authentication may reduce user convenience.</li>
      </ul>
    </div>
  </div>

  <!-- Authorisation -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Authorisation</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Ensures that authenticated users only access what they are allowed to</strong>.</li>
        <li><strong>Role‑based access control (RBAC)</strong> — permissions determined by user role.</li>
        <li><strong>Access control lists (ACLs)</strong> — assign specific permissions to users or groups.</li>
        <li><strong>Benefit:</strong> reduces risk by limiting access to sensitive resources.</li>
        <li><strong>Drawback:</strong> poorly maintained permissions can create vulnerabilities.</li>
      </ul>
    </div>
  </div>

  <!-- Accountability -->
  <div class="accordion-item">
    <button class="accordion-header" type="button">Accountability</button>
    <div class="accordion-content">
      <ul>
        <li><strong>Ensures actions can be traced back to individual users</strong>.</li>
        <li><strong>Audit logs</strong> — record system events and user activity.</li>
        <li><strong>User activity tracking</strong> — provides evidence of actions within a system.</li>
        <li><strong>Benefit:</strong> supports incident investigation and compliance.</li>
        <li><strong>Drawback:</strong> logs must be protected; if altered, accountability is lost.</li>
      </ul>
    </div>
  </div>

</div>

<p class="note"><em>Summary:</em> The IAAA model ensures that users are correctly identified, verified, permitted, and monitored within a digital system.</p>


</section>