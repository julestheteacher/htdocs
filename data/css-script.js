      document.addEventListener('click', (e) => {
        const header = e.target.closest('.accordion-header');
        if (!header) return;
        const item = header.closest('.accordion-item');
        item.classList.toggle('open');
      });