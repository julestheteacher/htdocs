<script src="css-script.js"></script>
<h1>1.3 Strategies</h1>
<section>
<main class="wrap">
<div class='accordion'>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.1 Approaches to Solving Problems: Top-Down, Bottom-Up, Modularisation</button>
      <div class='accordion-content'>
        <p><div class="accordion-strong">Overview:</div> Different approaches structure how a solution is designed and built. Choose an approach based on complexity, team size, reuse needs, and available components.</p>
        <ul>
          <li><div class="accordion-strong">Top-down:</div> Start with the whole problem, break it into major parts, then into smaller tasks until each can be implemented. <em>Used when the overall goal is clear and you need a coherent end-to-end design.</em></li>
          <li><div class="accordion-strong">Bottom-up:</div> Start by creating or selecting small, reusable components, then combine them into larger subsystems and finally the whole solution. <em>Used when you have libraries/services to build upon or when rapid prototyping of parts is possible.</em></li>
          <li><div class="accordion-strong">Modularisation:</div> Organise the solution into independent, well-defined modules with clear interfaces. <em>Used to enable parallel work, easier maintenance, testing, and reuse.</em></li>
        </ul>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.2 Benefits and Drawbacks of These Approaches</button>
      <div class='accordion-content'>
        <ul>
          <li><div class="accordion-strong">Top-down – Benefits:</div> Clear roadmap, aligns teams to a shared goal, helps manage complexity. <div class="accordion-strong">Drawbacks:</div> Early design mistakes can cascade; may overlook low-level constraints until late.</li>
          <li><div class="accordion-strong">Bottom-up – Benefits:</div> Encourages reuse, quick wins from working components, realistic constraints discovered early. <div class="accordion-strong">Drawbacks:</div> Risk of misalignment with the big picture; integration can be hard.</li>
          <li><div class="accordion-strong">Modularisation – Benefits:</div> Easier testing, maintenance, and parallel development; clear ownership. <div class="accordion-strong">Drawbacks:</div> Requires good interface design and governance; too many modules can add overhead.</li>
        </ul>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.3 Purpose of Root Cause Analysis (RCA) and When It Is Used</button>
      <div class='accordion-content'>
        <p><div class="accordion-strong">Definition:</div> RCA is a structured method to identify the underlying cause(s) of an issue so you can prevent recurrence, not just fix symptoms.</p>
        <p><div class="accordion-strong">When used:</div> After incidents, recurring issues, safety/security events, quality problems, or failed changes where long-term prevention is required.</p>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.4 RCA Techniques: Five Whys, FMEA, ETA & Follow-up Actions</button>
      <div class='accordion-content'>
        <ul>
          <li><div class="accordion-strong">Five Whys:</div> Ask “why?” repeatedly (typically five times) to move from symptom to underlying cause.</li>
          <li><div class="accordion-strong">Failure Mode and Effects Analysis (FMEA):</div> Systematically list potential failure modes, their effects, causes, and controls; prioritise using severity, occurrence, and detection.</li>
          <li><div class="accordion-strong">Event Tree Analysis (ETA):</div> Start from an initiating event and map forward through branches of system responses to evaluate possible outcomes and their likelihood.</li>
        </ul>
        <p><div class="accordion-strong">Actions after RCA:</div> Implement corrective and preventive actions, log outcomes, document residual risks, communicate changes, and <em>escalate to the appropriate manager</em> where policy, budget, or cross-team changes are required. Close the record when actions are verified.</p>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.5 High-level Problem-solving Strategy</button>
      <div class='accordion-content'>
        <ol>
          <li><div class="accordion-strong">Define the problem:</div> Clarify scope, impact, constraints, and success criteria.</li>
          <li><div class="accordion-strong">Gather information:</div> Collect logs, data, user reports, environment details, and recent changes.</li>
          <li><div class="accordion-strong">Analyse the information:</div> Form hypotheses, compare expected vs actual behaviour, identify likely causes.</li>
          <li><div class="accordion-strong">Make a plan of action:</div> Prioritise safe, reversible steps aligned with risk and impact.</li>
          <li><div class="accordion-strong">Implement a solution:</div> Execute the plan, communicate status, and track results.</li>
          <li><div class="accordion-strong">Review the solution:</div> Verify outcomes, document lessons learned, and prevent recurrence.</li>
        </ol>
      </div>
    </div>
    <h2> Digital Support Services Only</h2>
    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.6 Definition of a Digital Incident (Incident Management)</button>
      <div class='accordion-content'>
        <p><div class="accordion-strong">Digital Incident</div> is a <em>single unplanned event</em> that <em>disrupts service operations</em> and <em>negatively impacts service quality</em> (e.g., outage, degraded performance, security alert).</p>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.7 Definition of a Digital Problem (Incident Management)</button>
      <div class='accordion-content'>
        <p><div class="accordion-strong">Digital Problem</div> is the <em>underlying cause</em> of one or more incidents (e.g., a software defect, misconfiguration, or capacity shortfall) that must be identified and resolved to prevent recurrence.</p>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.8 Process of Incident Management</button>
      <div class='accordion-content'>
        <ul>
          <li><div class="accordion-strong">Detection:</div> Report, record, categorise, and prioritise the incident; assess impact and urgency.</li>
          <li><div class="accordion-strong">Response:</div> Assign an owner, diagnose, resolve and restore service, apply workarounds as needed, and record the resolution.</li>
          <li><div class="accordion-strong">Intelligence:</div> Capture lessons learned, identify root cause or link to a problem record, and share insights with stakeholders.</li>
        </ul>
      </div>
    </div>

    <div class='accordion-item'>
      <button class='accordion-header' type='button'>1.3.9 Interrelationships & Choosing Suitable Strategies</button>
      <div class='accordion-content'>
        <p>Problems, incidents, and strategies are linked: recurring incidents point to deeper problems that require structured strategies (e.g., RCA, modular fixes) rather than ad-hoc patches. Selecting an approach depends on impact, risk, time, available components, and required long-term reliability.</p>
        <p><div class="accordion-strong">Judgement factors:</div> severity and frequency of incidents, clarity of the end goal, availability of reusable modules, constraints (security, compliance), and the need for quick restoration vs permanent fix.</p>
      </div>
    </div>

  </div>

</main>
</section>