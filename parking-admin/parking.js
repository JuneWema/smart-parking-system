/* parking.js — shared by admin AND student pages.
   Data lives in localStorage under one key, so any page on the same origin
   (admin, student dashboard, booking page) reads the same spaces. */
(function () {
  const KEY = 'parkingSpaces';
  const DEFAULTS = [
    { id: 'p1', name: 'P1', status: 'available' },
    { id: 'p2', name: 'P2', status: 'booked' },
    { id: 'p3', name: 'P3', status: 'available' },
    { id: 'p4', name: 'P4', status: 'occupied' },
    { id: 'p5', name: 'P5', status: 'available' }
  ];
  const LABELS = { available: 'Available', booked: 'Booked', occupied: 'Occupied' };
  const role = document.body.dataset.role || 'student';

  function load() {
    try {
      const raw = localStorage.getItem(KEY);
      if (raw) return JSON.parse(raw);
    } catch (e) { /* fall through to defaults */ }
    save(DEFAULTS);
    return DEFAULTS.map(s => ({ ...s }));
  }
  function save(spaces) {
    localStorage.setItem(KEY, JSON.stringify(spaces));
  }
  function esc(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  /* ---------- "Today's data" cards ---------- */
  function renderStats(spaces) {
    const stats = {
      total: spaces.length,
      available: spaces.filter(s => s.status === 'available').length,
      booked: spaces.filter(s => s.status === 'booked').length,
      occupied: spaces.filter(s => s.status === 'occupied').length
    };
    document.querySelectorAll('[data-stat]').forEach(el => {
      el.textContent = stats[el.dataset.stat];
    });
  }

  /* ---------- Parking grid ---------- */
  function renderGrid(spaces) {
    const grid = document.getElementById('parkingGrid');
    if (!grid) return;
    grid.innerHTML = spaces.map(s => `
      <div class="parking--card ${s.status}" data-id="${esc(s.id)}">
        <h3>${esc(s.name)}</h3>
        <div class="car--icon"><i class="fa-solid fa-car"></i></div>
        <span class="status">${LABELS[s.status]}</span>
        ${role === 'admin' ? '<span class="edit--hint"><i class="fa-solid fa-pen"></i> Edit</span>' : ''}
      </div>`).join('');
  }

  function refresh() {
    const spaces = load();
    renderStats(spaces);
    renderGrid(spaces);
  }

  /* ---------- Admin: add / edit / delete ---------- */
  function nextName(spaces) {
    let n = spaces.length + 1;
    while (spaces.some(s => s.name.toUpperCase() === 'P' + n)) n++;
    return 'P' + n;
  }

  function initAdmin() {
    const modal = document.getElementById('spaceModal');
    const form = document.getElementById('spaceForm');
    const title = document.getElementById('modalTitle');
    const nameInput = document.getElementById('spaceName');
    const statusInput = document.getElementById('spaceStatus');
    const deleteBtn = document.getElementById('deleteSpace');
    const errorBox = document.getElementById('spaceError');
    let editingId = null;

    function openModal(space) {
      const spaces = load();
      editingId = space ? space.id : null;
      title.textContent = space ? 'Edit Parking Space' : 'Add Parking Space';
      nameInput.value = space ? space.name : nextName(spaces);
      statusInput.value = space ? space.status : 'available';
      deleteBtn.style.display = space ? 'inline-block' : 'none';
      errorBox.textContent = '';
      modal.classList.add('open');
      nameInput.focus();
    }
    function closeModal() { modal.classList.remove('open'); }

    document.getElementById('addSpaceBtn').addEventListener('click', () => openModal(null));
    document.getElementById('cancelSpace').addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.getElementById('parkingGrid').addEventListener('click', e => {
      const card = e.target.closest('.parking--card');
      if (!card) return;
      const space = load().find(s => s.id === card.dataset.id);
      if (space) openModal(space);
    });

    form.addEventListener('submit', e => {
      e.preventDefault();
      const name = nameInput.value.trim();
      const status = statusInput.value;
      const spaces = load();

      if (!name) { errorBox.textContent = 'Please enter a space name.'; return; }
      if (spaces.some(s => s.id !== editingId && s.name.toLowerCase() === name.toLowerCase())) {
        errorBox.textContent = 'A space with that name already exists.';
        return;
      }

      if (editingId) {
        const s = spaces.find(s => s.id === editingId);
        s.name = name;
        s.status = status;
      } else {
        spaces.push({ id: 'p' + Date.now(), name, status });
      }
      save(spaces);
      refresh();
      closeModal();
    });

    deleteBtn.addEventListener('click', () => {
      if (!editingId || !confirm('Delete this parking space?')) return;
      save(load().filter(s => s.id !== editingId));
      refresh();
      closeModal();
    });
  }

  /* ---------- Student: book an available space ---------- */
  function initStudent() {
    const grid = document.getElementById('parkingGrid');
    if (!grid) return;
    grid.addEventListener('click', e => {
      const card = e.target.closest('.parking--card.available');
      if (!card) return;
      const spaces = load();
      const s = spaces.find(s => s.id === card.dataset.id);
      if (s && confirm('Book space ' + s.name + '?')) {
        s.status = 'booked';
        save(spaces);
        refresh();
      }
    });
  }

  /* ---------- Boot ---------- */
  refresh();
  if (role === 'admin') initAdmin(); else initStudent();

  // "Live Updates": when another tab/page changes the data, redraw here.
  window.addEventListener('storage', e => { if (e.key === KEY) refresh(); });
})();