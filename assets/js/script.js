// Shared helpers for the Gym Management System pages.

// Usage: <form onsubmit="return confirmDelete('plan')">
function confirmDelete(itemName = 'item') {
    return confirm(`Are you sure you want to delete this ${itemName}? This cannot be undone.`);
}

function confirmUpdate(itemName = 'item') {
    return confirm(`Save changes to this ${itemName}?`);
}

// Unlocks every field inside the element with this id, and swaps the
// Edit button (data-role="edit") for the Save button (data-role="save").
function enableEdit(containerId) {
    const box = document.getElementById(containerId);
    if (!box) return;
    box.querySelectorAll('input:not([type="hidden"]), textarea, select').forEach(field => {
        field.removeAttribute('readonly');
        field.removeAttribute('disabled');
        field.classList.remove('readonly');
    });
    box.querySelectorAll('[data-role="edit"]').forEach(btn => { btn.style.display = 'none'; });
    box.querySelectorAll('[data-role="save"]').forEach(btn => { btn.style.display = 'inline-block'; });
}

// Shows/hides an element; used for collapsible profile sections.
function toggleVisibility(elementId, button) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const hidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';
    el.style.display = hidden ? 'block' : 'none';
    if (button) button.textContent = hidden ? 'Hide Details' : 'Show Details';
}
