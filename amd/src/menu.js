// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Custom dropdown menu for the tiny_styles toolbar button.
 *
 * Replaces TinyMCE's built-in addMenuButton dropdown with a fully owned HTML
 * panel to enable features that TinyMCE's menu API does not support.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {isInWord, isStyledInlineElement} from './styleutils';
import {previewElement} from './preview_element';

/** @type {HTMLElement|null} Currently mounted panel element. */
let activePanel = null;

/** @type {Function|null} Removes document-level event listeners on close. */
let cleanupListeners = null;

/**
 * Currently visually-focused row (fake focus).
 * @type {HTMLElement|null}
 */
let focusedRow = null;

/**
 * Sets the visually-focused row, removing the class from the previous one.
 * @param {HTMLElement|null} row
 */
function setFocusedRow(row) {
    if (focusedRow) {
        focusedRow.classList.remove('tsm-focused');
    }
    focusedRow = row;
    if (focusedRow) {
        focusedRow.classList.add('tsm-focused');
        focusedRow.scrollIntoView({block: 'nearest'});
    }
}

/**
 * Handles keyboard navigation for the active panel.
 *
 * @param {KeyboardEvent} e
 * @param {Object} editor TinyMCE editor instance.
 */
function handleKeydown(e, editor) {
    if (!activePanel) {
        return;
    }

    // Navigate within currently highlighted row if it is inside a submenu.
    const inSubmenu = focusedRow ? focusedRow.closest('.tsm-submenu') : null;

    if (inSubmenu) {
        const submenuRows = [...inSubmenu.querySelectorAll('.tsm-item:not(.tsm-item--disabled)')];
        const current = submenuRows.indexOf(focusedRow);

        switch (e.key) {
            case 'ArrowDown': {
                e.preventDefault();
                e.stopPropagation();
                setFocusedRow(submenuRows[current < submenuRows.length - 1 ? current + 1 : 0]);
                break;
            }
            case 'ArrowUp': {
                e.preventDefault();
                e.stopPropagation();
                setFocusedRow(submenuRows[current > 0 ? current - 1 : submenuRows.length - 1]);
                break;
            }
            case 'Enter':
            case ' ': {
                e.preventDefault();
                e.stopPropagation();
                if (focusedRow) {
                    focusedRow.click();
                }
                break;
            }
            case 'ArrowLeft':
            case 'Escape': {
                e.preventDefault();
                e.stopPropagation();
                // Close submenu and return highlight to the parent category row.
                const parentRow = inSubmenu.closest('.tsm-category');
                inSubmenu.classList.remove('tsm-submenu--open');
                if (parentRow) {
                    parentRow.setAttribute('aria-expanded', 'false');
                    setFocusedRow(parentRow);
                } else {
                    hide();
                    editor.focus();
                }
                break;
            }
        }
        return;
    }

    // Top-level navigation, excludes disabled rows and rows inside submenus.
    const rows = [...activePanel.querySelectorAll('.tsm-item:not(.tsm-item--disabled)')]
        .filter((row) => !row.closest('.tsm-submenu'));
    const current = rows.indexOf(focusedRow);

    switch (e.key) {
        case 'ArrowDown': {
            e.preventDefault();
            e.stopPropagation();
            // Close any submenu that was opened via keyboard before moving to the next row.
            closeAllSubmenus();
            setFocusedRow(rows[current < rows.length - 1 ? current + 1 : 0]);
            break;
        }
        case 'ArrowUp': {
            e.preventDefault();
            e.stopPropagation();
            closeAllSubmenus();
            setFocusedRow(rows[current > 0 ? current - 1 : rows.length - 1]);
            break;
        }
        case 'ArrowRight': {
            if (focusedRow && focusedRow.classList.contains('tsm-category')) {
                e.preventDefault();
                e.stopPropagation();
                openFocusedSubmenu();
            }
            break;
        }
        case 'Enter':
        case ' ': {
            e.preventDefault();
            e.stopPropagation();
            if (focusedRow) {
                if (focusedRow.classList.contains('tsm-category')) {
                    // Open the submenu and move highlight to its first item.
                    openFocusedSubmenu();
                } else {
                    focusedRow.click();
                }
            }
            break;
        }
        case 'Escape': {
            e.preventDefault();
            e.stopPropagation();
            // First Escape closes any open submenu and a second Escape closes the panel.
            if (activePanel && activePanel.querySelector('.tsm-submenu--open')) {
                closeAllSubmenus();
            } else {
                hide();
                editor.focus();
            }
            break;
        }
    }
}

/**
 * Opens the submenu of the currently focused category row and moves fake focus
 * to its first non-disabled element.
 */
function openFocusedSubmenu() {
    if (!focusedRow || !focusedRow.classList.contains('tsm-category')) {
        return;
    }
    const submenu = focusedRow.querySelector('.tsm-submenu');
    if (!submenu) {
        return;
    }
    closeAllSubmenus();
    submenu.classList.add('tsm-submenu--open');
    focusedRow.setAttribute('aria-expanded', 'true');
    positionSubmenu(submenu);
    const firstItem = submenu.querySelector('.tsm-item:not(.tsm-item--disabled)');
    if (firstItem) {
        setFocusedRow(firstItem);
    }
}

/**
 * Returns true if the menu panel is currently mounted and visible.
 *
 * @returns {boolean}
 */
export function isVisible() {
    return activePanel !== null;
}

/**
 * Builds and shows the custom styles dropdown panel below the toolbar button.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {Array} categories Fetched categories array.
 * @param {Function} applyStyleFn Callback  applyStyleFn(styleDef).
 * @param {Function} clearStylingFn Callback  clearStylingFn().
 * @param {string} clearLabel Localised string for the clear-style row.
 */
export function show(editor, categories, applyStyleFn, clearStylingFn, clearLabel) {
    hide();

    const panel = buildPanel(categories, applyStyleFn, clearStylingFn, clearLabel, editor);
    document.body.appendChild(panel);
    activePanel = panel;

    positionPanel(panel, editor);

    // Wait so the button click that opened the panel does not immediately close it.
    setTimeout(() => {
        const outsideClick = (e) => {
            if (!activePanel || activePanel.contains(e.target)) {
                return;
            }
            // Clicks inside preview/description modal keep the menu open.
            if (e.target.closest('.tsm-styles-modal')) {
                return;
            }
            // Toolbar button's own onAction toggle handler closes the panel.
            const anchor = findAnchorEl(editor);
            if (anchor && anchor.contains(e.target)) {
                return;
            }
            hide();
        };
        const keydown = (e) => handleKeydown(e, editor);
        // Intercepts TinyMCE toolbar/menu button clicks before TinyMCE stops propagation.
        const iframeDoc = editor.getDoc();
        document.addEventListener('mousedown', outsideClick, true);
        document.addEventListener('keydown', keydown);
        window.addEventListener('resize', hide);
        if (iframeDoc) {
            iframeDoc.addEventListener('click', outsideClick);
            // Fires before TinyMCE's keydown handlers, to block editor behaviour while the panel is open.
            iframeDoc.addEventListener('keydown', keydown, true);
        }
        cleanupListeners = () => {
            document.removeEventListener('mousedown', outsideClick, true);
            document.removeEventListener('keydown', keydown);
            window.removeEventListener('resize', hide);
            if (iframeDoc) {
                iframeDoc.removeEventListener('click', outsideClick);
                iframeDoc.removeEventListener('keydown', keydown, true);
            }
        };
    }, 0);
}

/**
 * Hides the active panel and removes document level event listeners.
 */
export function hide() {
    if (activePanel) {
        activePanel.remove();
        activePanel = null;
    }
    if (cleanupListeners) {
        cleanupListeners();
        cleanupListeners = null;
    }
    setFocusedRow(null);
}

/**
 * Builds the full panel DOM element.
 *
 * @param {Array} categories
 * @param {Function} applyStyleFn
 * @param {Function} clearStylingFn
 * @param {string} clearLabel
 * @param {Object} editor TinyMCE editor instance.
 * @returns {HTMLElement}
 */
function buildPanel(categories, applyStyleFn, clearStylingFn, clearLabel, editor) {
    const panel = document.createElement('div');
    panel.className = 'tsm-panel';
    panel.setAttribute('role', 'menu');

    // Build the category rows first and redundant dividers are normalized.
    const rows = [];
    categories.forEach((cat) => {
        if (cat.menumode === 'divider') {
            rows.push(buildDivider());
            return;
        }

        // Inline mode: elements appear directly in the top-level panel.
        if (cat.menumode === 'inline' && Array.isArray(cat.elements)) {
            cat.elements.forEach((elem) => {
                rows.push(buildElementRow(elem, applyStyleFn, editor));
            });
            return;
        }

        // Submenu mode: category row with flyout.
        if (Array.isArray(cat.elements) && cat.elements.length > 0) {
            rows.push(buildCategoryRow(cat, applyStyleFn, editor));
        }
    });

    normalizeDividers(rows).forEach((row) => panel.appendChild(row));

    // Separator before the clear row.
    if (panel.childElementCount > 0) {
        panel.appendChild(buildDivider());
    }
    panel.appendChild(buildClearRow(clearStylingFn, clearLabel));

    // Highlight the first row when the panel opens.
    requestAnimationFrame(() => {
        if (!activePanel) {
            return;
        }
        const firstRows = [...activePanel.querySelectorAll('.tsm-item:not(.tsm-item--disabled)')]
            .filter((row) => !row.closest('.tsm-submenu'));
        if (firstRows.length > 0) {
            setFocusedRow(firstRows[0]);
        }
    });

    return panel;
}

/**
 * Builds a category row.
 *
 * @param {Object} cat
 * @param {Function} applyStyleFn
 * @param {Object} editor TinyMCE editor instance.
 * @returns {HTMLElement}
 */
function buildCategoryRow(cat, applyStyleFn, editor) {
    const row = document.createElement('div');
    row.className = 'tsm-item tsm-category';
    row.setAttribute('role', 'menuitem');
    row.setAttribute('aria-haspopup', 'true');
    row.setAttribute('aria-expanded', 'false');
    row.tabIndex = -1;
    row.dataset.catId = cat.id;

    const faName = cat.symbol || 'droplet';

    const iconEl = document.createElement('span');
    iconEl.className = 'tsm-icon';
    const i = document.createElement('i');
    i.className = 'fa-solid fa-' + faName;
    iconEl.appendChild(i);
    row.appendChild(iconEl);

    const label = document.createElement('span');
    label.className = 'tsm-label';
    label.textContent = cat.name;
    row.appendChild(label);

    const spacer = document.createElement('span');
    spacer.className = 'tsm-spacer';
    row.appendChild(spacer);

    if (cat.showdesc === 'helptext' && cat.description) {
        const infoBtn = document.createElement('button');
        infoBtn.type = 'button';
        infoBtn.className = 'tsm-info';
        infoBtn.title = cat.description;
        infoBtn.setAttribute('aria-label', cat.description);
        const infoIcon = document.createElement('i');
        infoIcon.className = 'fa-solid fa-circle-question';
        infoIcon.setAttribute('aria-hidden', 'true');
        infoBtn.appendChild(infoIcon);
        infoBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            previewElement.showDescription(cat.name, cat.description);
        });
        row.appendChild(infoBtn);
    } else if (cat.showdesc === 'tooltip' && cat.description) {
        row.title = cat.description;
    }

    const caret = document.createElement('span');
    caret.className = 'tsm-caret';
    caret.setAttribute('aria-hidden', 'true');
    const caretIcon = document.createElement('i');
    caretIcon.className = 'fa-solid fa-chevron-right';
    caret.appendChild(caretIcon);
    row.appendChild(caret);

    const submenu = buildSubmenu(cat.elements, applyStyleFn, editor);
    row.appendChild(submenu);

    // Click/keyboard activation: open submenu immediately.
    row.addEventListener('click', (e) => {
        if (e.target.closest('.tsm-info')) {
            return;
        }
        closeAllSubmenus();
        submenu.classList.add('tsm-submenu--open');
        row.setAttribute('aria-expanded', 'true');
        positionSubmenu(submenu);
    });

    // Hover behaviour.
    let openTimer = null;
    let closeTimer = null;

    const openSubmenu = () => {
        clearTimeout(closeTimer);
        openTimer = setTimeout(() => {
            closeAllSubmenus();
            submenu.classList.add('tsm-submenu--open');
            row.setAttribute('aria-expanded', 'true');
            positionSubmenu(submenu);
        }, 100);
    };

    const closeSubmenu = () => {
        // Keep the submenu open while a preview/description modal is showing.
        if (document.body.classList.contains('modal-open')) {
            return;
        }
        clearTimeout(openTimer);
        closeTimer = setTimeout(() => {
            if (document.body.classList.contains('modal-open')) {
                return;
            }
            submenu.classList.remove('tsm-submenu--open');
            row.setAttribute('aria-expanded', 'false');
        }, 150);
    };

    row.addEventListener('mouseenter', () => {
        setFocusedRow(row);
        openSubmenu();
    });
    row.addEventListener('mouseleave', (e) => {
        // Don't close if the pointer is moving into the submenu.
        if (submenu.contains(e.relatedTarget)) {
            return;
        }
        closeSubmenu();
    });

    submenu.addEventListener('mouseenter', () => {
        clearTimeout(closeTimer);
    });
    submenu.addEventListener('mouseleave', (e) => {
        // Don't close if the pointer moves back to the category row.
        if (row.contains(e.relatedTarget)) {
            return;
        }
        closeSubmenu();
    });

    return row;
}

/**
 * Builds the submenu container holding element rows.
 *
 * @param {Array} elements
 * @param {Function} applyStyleFn
 * @param {Object} editor TinyMCE editor instance.
 * @returns {HTMLElement}
 */
function buildSubmenu(elements, applyStyleFn, editor) {
    const submenu = document.createElement('div');
    submenu.className = 'tsm-submenu';
    elements.forEach((elem) => {
        submenu.appendChild(buildElementRow(elem, applyStyleFn, editor));
    });
    return submenu;
}

/**
 * Builds a single clickable element row.
 * Inline-type elements are disabled when the cursor is not in a word
 * and not inside an already-styled span.
 *
 * @param {Object} elem
 * @param {Function} applyStyleFn
 * @param {Object} editor TinyMCE editor instance.
 * @returns {HTMLElement}
 */
function buildElementRow(elem, applyStyleFn, editor) {
    const row = document.createElement('div');
    row.className = 'tsm-item tsm-element';
    row.setAttribute('role', 'menuitem');
    row.dataset.elemId = elem.id;

    const isInline = elem.type !== 'block';
    let disabled = false;
    if (isInline && editor) {
        const inStyledSpan = !!editor.dom.getParent(
            editor.selection.getNode(), isStyledInlineElement
        );
        disabled = !isInWord(editor) && !inStyledSpan;
    }

    if (disabled) {
        row.classList.add('tsm-item--disabled');
        row.setAttribute('aria-disabled', 'true');
    }
    row.tabIndex = -1;

    const label = document.createElement('span');
    label.className = 'tsm-label';
    label.textContent = elem.name;
    row.appendChild(label);

    const previewBtn = document.createElement('button');
    previewBtn.type = 'button';
    previewBtn.className = 'tsm-preview';
    previewBtn.setAttribute('aria-label', 'Preview: ' + elem.name);
    const previewIcon = document.createElement('i');
    previewIcon.className = 'fa-solid fa-magnifying-glass-plus';
    previewIcon.setAttribute('aria-hidden', 'true');
    previewBtn.appendChild(previewIcon);
    previewBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        previewElement.showPreview(
            elem.name,
            elem.cssclasses,
            elem.type,
            () => {
                applyStyleFn({
                    className: elem.cssclasses,
                    block: elem.type === 'block',
                    custom: elem.custom === 1,
                    id: elem.id,
                });
                hide();
            },
             // Apply button is greyed out for disabled rows
            disabled
        );
    });
    row.appendChild(previewBtn);

    if (!disabled) {
        row.addEventListener('mouseenter', () => setFocusedRow(row));
        row.addEventListener('click', (e) => {
            e.stopPropagation();
            applyStyleFn({
                className: elem.cssclasses,
                block: elem.type === 'block',
                custom: elem.custom === 1,
                id: elem.id,
            });
            hide();
        });
    }

    return row;
}

/**
 * Builds a horizontal divider row.
 *
 * @returns {HTMLElement}
 */
function buildDivider() {
    const div = document.createElement('div');
    div.className = 'tsm-divider';
    div.setAttribute('role', 'separator');
    return div;
}

/**
 * Removes redundant dividers from a list of panel rows.
 * Drops leading and trailing dividers and collapses consecutive dividers down to a single line.
 *
 * @param {HTMLElement[]} rows Ordered panel rows (category rows, element rows, dividers).
 * @returns {HTMLElement[]} The filtered rows.
 */
function normalizeDividers(rows) {
    const isDivider = (row) => row.classList.contains('tsm-divider');
    const result = [];
    rows.forEach((row) => {
        if (isDivider(row)) {
            // Skips a leading divider or that immediately follows another divider.
            if (result.length === 0 || isDivider(result[result.length - 1])) {
                return;
            }
        }
        result.push(row);
    });
    // Drops a trailing divider left with no content after it.
    while (result.length > 0 && isDivider(result[result.length - 1])) {
        result.pop();
    }
    return result;
}

/**
 * Builds the clear-style row at the bottom of the panel.
 *
 * @param {Function} clearStylingFn
 * @param {string} clearLabel
 * @returns {HTMLElement}
 */
function buildClearRow(clearStylingFn, clearLabel) {
    const row = document.createElement('div');
    row.className = 'tsm-item tsm-clear';
    row.setAttribute('role', 'menuitem');
    row.tabIndex = -1;

    const iconEl = document.createElement('span');
    iconEl.className = 'tsm-icon';
    const i = document.createElement('i');
    i.className = 'fa-solid fa-xmark';
    iconEl.appendChild(i);
    row.appendChild(iconEl);

    const label = document.createElement('span');
    label.className = 'tsm-label';
    label.textContent = clearLabel;
    row.appendChild(label);

    row.addEventListener('mouseenter', () => setFocusedRow(row));
    row.addEventListener('click', (e) => {
        e.stopPropagation();
        clearStylingFn();
        hide();
    });

    return row;
}

/**
 * Closes all open submenus in the active panel.
 */
function closeAllSubmenus() {
    if (!activePanel) {
        return;
    }
    activePanel.querySelectorAll('.tsm-submenu--open').forEach((s) => {
        s.classList.remove('tsm-submenu--open');
        const parentRow = s.closest('.tsm-category');
        if (parentRow) {
            parentRow.setAttribute('aria-expanded', 'false');
        }
    });
}

/**
 * Positions a submenu relative to its category row, flipping left if it
 * would overflow the right edge of the viewport.
 *
 * @param {HTMLElement} submenu
 */
function positionSubmenu(submenu) {
    // Reset to default right-side position first.
    submenu.style.left = '100%';
    submenu.style.right = 'auto';

    requestAnimationFrame(() => {
        if (!submenu.classList.contains('tsm-submenu--open')) {
            return;
        }
        const rect = submenu.getBoundingClientRect();
        if (rect.right > window.innerWidth) {
            submenu.style.left = 'auto';
            submenu.style.right = '100%';
        }
    });
}

/**
 * Positions the panel directly below the toolbar button.
 * Falls back to the editor container top if the button cannot be found.
 *
 * @param {HTMLElement} panel
 * @param {Object} editor
 */
function positionPanel(panel, editor) {
    const anchor = findAnchorEl(editor);
    const scrollX = window.scrollX || window.pageXOffset;
    const scrollY = window.scrollY || window.pageYOffset;

    if (anchor) {
        const rect = anchor.getBoundingClientRect();
        panel.style.top  = Math.round(rect.bottom + scrollY) + 'px';
        panel.style.left = Math.round(rect.left + scrollX) + 'px';
    } else {
        const container = editor.getContainer();
        const rect = container.getBoundingClientRect();
        panel.style.top  = Math.round(rect.top + scrollY + 40) + 'px';
        panel.style.left = Math.round(rect.left + scrollX) + 'px';
    }

    // After first paint clamps to viewport if needed.
    requestAnimationFrame(() => {
        if (!activePanel) {
            return;
        }
        const panelRect = panel.getBoundingClientRect();
        if (panelRect.right > window.innerWidth) {
            panel.style.left = Math.round(Math.max(0, window.innerWidth - panelRect.width + scrollX)) + 'px';
        }
        if (panelRect.bottom > window.innerHeight && anchor) {
            const rect = anchor.getBoundingClientRect();
            panel.style.top = Math.round(rect.top + scrollY - panelRect.height) + 'px';
        }
    });
}

/**
 * Finds the toolbar button DOM element to use as positioning anchor.
 *
 * @param {Object} editor
 * @returns {HTMLElement|null}
 */
function findAnchorEl(editor) {
    const container = editor.getContainer();
    if (!container) {
        return null;
    }
    return container.querySelector('[data-mce-name="tiny_styles_button"]');
}
