<script src="css-script.js"></script>

<h1><i class="fas fa-user-secret"></i> 8.3.2 Process and Procedures</h1>
    <p>Understand the processes and procedures that assure internet security, and the reasons why they are used.</p>

    <section>
      <h2>2 Understand the processes and procedures that assure internet security, and the reasons why they are used</h2>
      <p>Click each category below to expand and view details:</p>

      <div class="accordion">

        <div class="accordion-item">
          <button class="accordion-header" aria-expanded="false">Firewall configuration</button>
          <div class="accordion-content" hidden>
            <ul>
              <li><i class="strong">Rules for traffic (inbound and outbound)</i> — Define which connections are allowed in and out to reduce attack surface.</li>
              <li><i class="strong">Traffic type rules</i> — Permit or deny traffic by protocol/port (e.g. HTTP, HTTPS, SSH) to limit unnecessary services.</li>
              <li><i class="strong">Application rules</i> — Control specific applications or services regardless of port to prevent misuse.</li>
              <li><i class="strong">IP address rules</i> — Allow or block traffic from specific IPs or networks to enforce trusted sources.</li>
            </ul>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header" aria-expanded="false">Network segregation</button>
          <div class="accordion-content" hidden>
            <ul>
              <li><i class="strong">Virtual</i> — Use VLANs and virtual networks to isolate traffic logically between segments.</li>
              <li><i class="strong">Physical</i> — Separate critical systems using dedicated hardware and physical boundaries to reduce lateral movement.</li>
              <li><i class="strong">Offline network</i> — Keep highly sensitive systems off the internet (air-gapped) to eliminate online attack paths.</li>
            </ul>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header" aria-expanded="false">Network monitoring</button>
          <div class="accordion-content" hidden>
            <ul>
              <li><i class="strong">Traffic analysis</i> — Continuous monitoring of network flows to detect anomalies and suspicious behaviour.</li>
              <li><i class="strong">Logging and alerting</i> — Centralise logs and generate alerts for indicators of compromise to enable rapid response.</li>
            </ul>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header" aria-expanded="false">Port scanning</button>
          <div class="accordion-content" hidden>
            <ul>
              <li><i class="strong">Internal scanning</i> — Regular internal scans identify open ports and services that need hardening or removal.</li>
              <li><i class="strong">External scanning</i> — External scans simulate attacker reconnaissance to find exposed services and misconfigurations.</li>
            </ul>
          </div>
        </div>

      </div>
    </section>
