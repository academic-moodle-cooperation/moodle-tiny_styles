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
 * JS support to select a Font Awesome icon for a category.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {faIcons} from './fa_icons';

const BATCH_SIZE = 50;

/**
 * Initialize icon selector functionality.
 *
 * @return {void}
 */
export const init = () => {
    const iconSearchInput = document.getElementById('icon-search-input');
    const searchContainer = document.getElementById('search-container');
    const closeIconPopupBtn = document.getElementById('close-icon-popup');
    const iconPopup = document.getElementById('icon-popup');
    const iconGrid = document.getElementById('icon-grid');
    const iconGridContainer = document.getElementById('icon-grid-container');
    const selectedIconInput = document.querySelector('input[name="selectedicon"]');
    const noIconsFound = document.getElementById('no-icons-found');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const iconPreview = document.getElementById('icon-preview');

    // Places popup relative to search container.
    const positionPopup = () => {
        if (iconPopup && searchContainer && iconPopup.parentElement !== searchContainer) {
            searchContainer.appendChild(iconPopup);
        }
    };

    positionPopup();

    // Batch rendering state.
    let activeList = faIcons;
    let offset = 0;

    const clearGrid = () => {
        if (iconGrid) {
            iconGrid.innerHTML = '';
        }
        offset = 0;
    };

    const renderBatch = () => {
        if (!iconGrid) {
            return;
        }
        const slice = activeList.slice(offset, offset + BATCH_SIZE);
        slice.forEach(name => {
            const item = document.createElement('div');
            item.className = 'icon-grid-item';
            item.dataset.iconName = name;
            item.style.cssText = 'cursor:pointer; display:flex; flex-direction:column; align-items:center;'
                + ' justify-content:flex-start; padding:8px; border-radius:12px; box-sizing:border-box;';
            item.title = name;

            const i = document.createElement('i');
            i.className = 'fa-solid fa-' + name;
            i.style.cssText = 'font-size:20px; pointer-events:none;';

            const label = document.createElement('div');
            label.style.cssText = 'font-size:0.65em; margin-top:4px; color:#666; text-align:center;'
                + ' width:100%; line-height:1.3; overflow-wrap:break-word; word-break:normal;'
                + ' display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;';
            label.textContent = name === 'droplet' ? name + ' (default)' : name;

            item.appendChild(i);
            item.appendChild(label);

            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = '#e7f1ff';
            });
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = '';
            });
            item.addEventListener('click', () => {
                updateSelectedIcon(name);
                if (iconPopup) {
                    iconPopup.style.display = 'none';
                }
            });

            iconGrid.appendChild(item);
        });

        offset += slice.length;

        if (noIconsFound) {
            noIconsFound.style.display = offset === 0 ? 'block' : 'none';
        }
    };

    const updateSelectedIcon = (name) => {
        if (selectedIconInput) {
            selectedIconInput.value = name;
        }
        if (iconSearchInput) {
            iconSearchInput.value = name;
        }
        if (iconPreview) {
            iconPreview.className = 'fa-solid fa-' + name;
        }
    };

    const openPopup = () => {
        positionPopup();
        if (!iconPopup) {
            return;
        }
        // Reset to full list filtered by current search term.
        const term = iconSearchInput ? iconSearchInput.value.trim().toLowerCase() : '';
        activeList = term ? faIcons.filter(n => n.includes(term)) : faIcons;
        clearGrid();
        renderBatch();
        iconPopup.style.display = 'block';
    };

    // Scroll-driven loading.
    if (iconGridContainer) {
        iconGridContainer.addEventListener('scroll', () => {
            const {scrollTop, clientHeight, scrollHeight} = iconGridContainer;
            if (scrollTop + clientHeight >= scrollHeight - 60 && offset < activeList.length) {
                renderBatch();
            }
        });
    }

    // Init: pre-fill from existing selectedicon value.
    // Set to default when no icon has been chosen.
    if (selectedIconInput && iconSearchInput) {
        const initialIcon = selectedIconInput.value;
        if (initialIcon && initialIcon.length > 0) {
            iconSearchInput.value = initialIcon;
            if (iconPreview) {
                iconPreview.className = 'fa-solid fa-' + initialIcon;
            }
        } else {
            selectedIconInput.value = 'droplet';
            if (iconPreview) {
                iconPreview.className = 'fa-solid fa-droplet';
            }
        }
    }

    // Filter search on typing and open popup if closed.
    if (iconSearchInput) {
        iconSearchInput.addEventListener('input', (e) => {
            const term = e.target.value.trim().toLowerCase();
            activeList = term ? faIcons.filter(n => n.includes(term)) : faIcons;
            clearGrid();
            renderBatch();
            if (iconPopup && iconPopup.style.display !== 'block') {
                positionPopup();
                iconPopup.style.display = 'block';
            }
        });

        iconSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = iconSearchInput.value.trim();
                if (val === '') {
                    // Revert to default.
                    if (selectedIconInput) {
                        selectedIconInput.value = 'droplet';
                    }
                    if (iconPreview) {
                        iconPreview.className = 'fa-solid fa-droplet';
                    }
                } else if (faIcons.includes(val)) {
                    updateSelectedIcon(val);
                } else {
                    // Not a valid icon falls back to default.
                    if (selectedIconInput) {
                        selectedIconInput.value = val;
                    }
                }
                if (iconPopup) {
                    iconPopup.style.display = 'none';
                }
            }
            if (e.key === 'Escape' && iconPopup) {
                iconPopup.style.display = 'none';
            }
        });

        iconSearchInput.addEventListener('focus', openPopup);
        iconSearchInput.addEventListener('click', openPopup);
    }

    if (closeIconPopupBtn) {
        closeIconPopupBtn.addEventListener('click', () => {
            if (iconPopup) {
                iconPopup.style.display = 'none';
            }
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            if (iconSearchInput) {
                iconSearchInput.value = '';
            }
            activeList = faIcons;
            clearGrid();
            renderBatch();
            if (iconSearchInput) {
                iconSearchInput.focus();
            }
        });
    }

    // Close popup when clicking outside.
    document.addEventListener('click', (e) => {
        if (iconPopup &&
            iconPopup.style.display === 'block' &&
            !iconPopup.contains(e.target) &&
            iconSearchInput && !iconSearchInput.contains(e.target)) {
            iconPopup.style.display = 'none';
        }
    });

    if (iconPopup) {
        iconPopup.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
};
