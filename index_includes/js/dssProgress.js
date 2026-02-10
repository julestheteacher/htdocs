  // Load the Problem Solving criteria on page load
  DSSProgress.init('/criteria/problem-solving.json').then(() => {
    // OPTIONAL: render your UI here using DSSProgress.getSections()
    // renderUI(DSSProgress.getSections());
  });

/*!
 * DSSProgress v1.0
 * - Loads criteria JSON
 * - Merges with local progress
 * - Exposes functions for quizzes/sub-processes to update criteria
 * - Auto-saves to localStorage (no student ID)
 */
(function (global) {
  const STORAGE_KEY = 'dss-progress';
  let sections = [];           // [{ name, href, criteria: [{id,label,to_revise,completed}] }]
  const listeners = [];        // subscribed onChange callbacks

  // ---- Utilities ----
  function notify() { listeners.forEach(cb => { try { cb(getSections()); } catch (e) {} }); }
  function flatten() {
    const map = {};
    sections.forEach(sec => sec.criteria.forEach(c => {
      map[c.id] = { completed: !!c.completed, to_revise: !!c.to_revise };
    }));
    return map;
  }
  function save() { localStorage.setItem(STORAGE_KEY, JSON.stringify(flatten())); notify(); }
  function loadSavedMap() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); }
    catch { return {}; }
  }

  // ---- Public API ----
  async function init(jsonUrl) {
    const res = await fetch(jsonUrl, { cache: 'no-store' });
    const base = await res.json(); // either a single section object or an array of sections
    const saved = loadSavedMap();

    // Normalize to an array of sections
    sections = Array.isArray(base) ? base : [base];

    // Merge saved status
    sections.forEach(section => {
      section.criteria = section.criteria || [];
      section.criteria.forEach(c => {
        const s = saved[c.id];
        if (s) {
          c.completed = !!s.completed;
          c.to_revise = !!s.to_revise;
        } else {
          c.completed = !!c.completed;
          c.to_revise = !!c.to_revise;
        }
      });
    });

    // Cross-tab sync
    window.addEventListener('storage', (e) => {
      if (e.key === STORAGE_KEY) {
        const incoming = loadSavedMap();
        sections.forEach(sec => sec.criteria.forEach(c => {
          if (incoming[c.id]) {
            c.completed = !!incoming[c.id].completed;
            c.to_revise = !!incoming[c.id].to_revise;
          }
        }));
        notify();
      }
    });

    notify();
    return getSections();
  }

  function getSections() { 
    // return deep-ish clone to avoid accidental mutation
    return sections.map(s => ({
      name: s.name,
      href: s.href,
      criteria: s.criteria.map(c => ({...c}))
    }));
  }

  function getCriterion(id) {
    for (const sec of sections) {
      for (const c of sec.criteria) {
        if (c.id === id) return c;
      }
    }
    return null;
  }

  function setCriterion(id, { completed, to_revise }) {
    const c = getCriterion(id);
    if (!c) return false;
    if (typeof completed !== 'undefined') c.completed = !!completed;
    if (typeof to_revise !== 'undefined') c.to_revise = !!to_revise;

    // Optional rule: if completed -> to_revise false
    if (c.completed) c.to_revise = false;

    save();
    return true;
  }

  function complete(id) { return setCriterion(id, { completed: true, to_revise: false }); }
  function markToRevise(id) { return setCriterion(id, { to_revise: true, completed: false }); }

  function completeSection(sectionName) {
    sections.forEach(sec => {
      if (sec.name === sectionName) {
        sec.criteria.forEach(c => { c.completed = true; c.to_revise = false; });
      }
    });
    save();
  }

  function onChange(callback) {
    if (typeof callback === 'function') listeners.push(callback);
    // immediately invoke with current data for convenience
    try { callback(getSections()); } catch {}
    return () => {
      const i = listeners.indexOf(callback);
      if (i >= 0) listeners.splice(i, 1);
    };
  }

  // expose globally
  global.DSSProgress = { init, getSections, getCriterion, setCriterion, complete, markToRevise, completeSection, onChange };
})(window);