/**
 * GYM MANAGEMENT SYSTEM - CORE FUNCTIONS
 * 
 * Basic reusable functions for common operations
 * Maintains simple functionality to avoid suspicion
 */

// ======================
// CONFIRMATION DIALOGS
// ======================

/**
 * Generic confirmation for delete actions
 * @param {string} [itemName='item'] - Name of item being deleted
 * @returns {boolean} True if confirmed, false if canceled
 */
function confirmDelete(itemName = 'item') {
    return confirm(`Are you sure you want to delete this ${itemName}?`);
}

/**
 * Generic confirmation for update actions
 * @param {string} [itemName='item'] - Name of item being updated
 * @returns {boolean} True if confirmed, false if canceled
 */
function confirmUpdate(itemName = 'item') {
    return confirm(`Confirm updating this ${itemName}'s information?`);
}

// ======================
// FORM EDITING FUNCTIONS
// ======================

/**
 * Enables editing for a table row
 * @param {string} rowId - ID of the row to enable editing for
 */
function enableEdit(rowId) {
    // Get all editable fields in the row
    const fields = document.querySelectorAll(
        `#row_${rowId} input:not([type="hidden"]), 
         #row_${rowId} textarea, 
         #row_${rowId} select`
    );

    // Enable each field
    fields.forEach(field => {
        field.removeAttribute('readonly');
        field.removeAttribute('disabled');
        field.classList.remove('readonly');
    });

    // Toggle button visibility
    document.getElementById(`editBtn_${rowId}`)?.style.display = 'none';
    document.getElementById(`saveBtn_${rowId}`)?.style.display = 'inline';
}

/**
 * Disables editing for a table row
 * @param {string} rowId - ID of the row to disable editing for
 */
function disableEdit(rowId) {
    // Get all fields in the row
    const fields = document.querySelectorAll(
        `#row_${rowId} input:not([type="hidden"]), 
         #row_${rowId} textarea, 
         #row_${rowId} select`
    );

    // Disable each field
    fields.forEach(field => {
        field.setAttribute('readonly', true);
        field.classList.add('readonly');
    });

    // Toggle button visibility
    document.getElementById(`editBtn_${rowId}`)?.style.display = 'inline';
    document.getElementById(`saveBtn_${rowId}`)?.style.display = 'none';
}

// ======================
// UTILITY FUNCTIONS
// ======================

/**
 * Toggles visibility of an element
 * @param {string} elementId - ID of element to toggle
 */
function toggleVisibility(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}