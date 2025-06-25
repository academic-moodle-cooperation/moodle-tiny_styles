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
 * JS support to select an icon for a category
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize icon selector functionality
 *
 * @return {void}
 */
export const init = () => {
    // Get DOM elements
    const iconSearchInput = document.getElementById('icon-search-input');
    const searchContainer = document.getElementById('search-container');
    const closeIconPopupBtn = document.getElementById('close-icon-popup');
    const iconPopup = document.getElementById('icon-popup');
    const selectedIconInput = document.querySelector('input[name="selectedicon"]');
    const iconGrid = document.getElementById('icon-grid');
    const noIconsFound = document.getElementById('no-icons-found');
    const clearSearchBtn = document.getElementById('clear-search-btn');

    
    // Places popup relative to search container
    const positionPopup = () => {
        if (iconPopup && searchContainer) {
            if (iconPopup.parentElement !== searchContainer) {
                searchContainer.appendChild(iconPopup);
            }
        }
    };
    
    // Get available icons from the global variable set by PHP
    const availableIcons = window.availableIcons || [];
    
    // Position popup on initialization
    positionPopup();
    
    const isValidIcon = (iconName) => {
        // Check both with and without .svg extension.
        const nameWithoutExt = iconName.replace(/\.svg$/i, '');
        const nameWithExt = nameWithoutExt + '.svg';
        
        return availableIcons.some(icon => 
            icon.toLowerCase() === nameWithExt.toLowerCase() ||
            icon.toLowerCase() === nameWithoutExt.toLowerCase()
        );
    };
    
    // Get correct icon filename
    const getCorrectIconName = (iconName) => {
        const nameWithoutExt = iconName.replace(/\.svg$/i, '');
        const nameWithExt = nameWithoutExt + '.svg';
        
        const foundIcon = availableIcons.find(icon => 
            icon.toLowerCase() === nameWithExt.toLowerCase() ||
            icon.toLowerCase() === nameWithoutExt.toLowerCase()
        );
        
        return foundIcon || 'default.svg';
    };
    
    const updateSelectedIcon = (iconName) => {
        const correctIconName = getCorrectIconName(iconName);
        if (selectedIconInput) {
            selectedIconInput.value = correctIconName;
        }        
        if (iconSearchInput) {
            iconSearchInput.value = correctIconName.replace(/\.svg$/i, '');
        }
    };
    
    // Filter icons in popup when typing
    const filterIcons = (searchTerm) => {
        const iconItems = document.querySelectorAll('.icon-grid-item');
        let visibleCount = 0;
        
        iconItems.forEach(item => {
            const iconName = item.dataset.iconName.toLowerCase();
            const fullIconName = item.dataset.icon.toLowerCase();
            const searchLower = searchTerm.toLowerCase();
            
            if (iconName.includes(searchLower) || fullIconName.includes(searchLower) || searchLower === 'default') {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show "no icons found" message
        if (noIconsFound) {
            noIconsFound.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    };
    
    // Init with existing value or default icon
    if (selectedIconInput && iconSearchInput) {
        const initialIcon = selectedIconInput.value;
        if (initialIcon && initialIcon.length > 0) {
            iconSearchInput.value = initialIcon.replace(/\.svg$/i, '');
        } else {
            updateSelectedIcon('default');
        }
    }
    
    // Handle search input in main form
    if (iconSearchInput) {
        // Handle typing for real-time filtering
        iconSearchInput.addEventListener('input', (e) => {
            const inputValue = e.target.value;
            // Filter icons in popup if it's open
            if (iconPopup && iconPopup.style.display === 'block') {
                filterIcons(inputValue);
            }
        });
        
        iconSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const inputValue = iconSearchInput.value.trim();
                
                if (inputValue === '') {
                    updateSelectedIcon('default');
                } else if (isValidIcon(inputValue)) {
                    updateSelectedIcon(inputValue);
                } else {
                    // Invalid icon name, reset to default
                    updateSelectedIcon('default');
                    iconSearchInput.style.borderColor = '#dc3545';
                    setTimeout(() => {
                        iconSearchInput.style.borderColor = '';
                    }, 2000);
                }
                
                // Close popup after selection
                if (iconPopup) {
                    iconPopup.style.display = 'none';
                }
            }
            
            // Escape key to close popup
            if (e.key === 'Escape') {
                if (iconPopup) {
                    iconPopup.style.display = 'none';
                }
            }
        });
        
        // Handle focus and click to open popup
        iconSearchInput.addEventListener('focus', () => {
            positionPopup();
            if (iconPopup) {
                iconPopup.style.display = 'block';
                // Filter with current input value
                filterIcons(iconSearchInput.value);
            }
        });
        
        iconSearchInput.addEventListener('click', () => {
            positionPopup();
            if (iconPopup) {
                iconPopup.style.display = 'block';
                // Filter with current input value
                filterIcons(iconSearchInput.value);
            }
        });
    }
    
    // Close popup button
    if (closeIconPopupBtn) {
        closeIconPopupBtn.addEventListener('click', () => {
            if (iconPopup) {
                iconPopup.style.display = 'none';
            }
        });
    }
    
    // Clear search
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            // Clear the search input
            if (iconSearchInput) {
                iconSearchInput.value = '';
            }
            filterIcons('');
            // Focus back on the search
            if (iconSearchInput) {
                iconSearchInput.focus();
            }
        });
    }

    // Handle icon selection from grid
    const iconGridItems = document.querySelectorAll('.icon-grid-item');
    iconGridItems.forEach(item => {
        // Hover effects
        item.addEventListener('mouseenter', () => {
            item.style.borderColor = '#007bff';
            item.style.backgroundColor = '#f8f9fa';
        });        
        item.addEventListener('mouseleave', () => {
            item.style.borderColor = 'transparent';
            item.style.backgroundColor = 'transparent';
        });
        
        // Handle click
        item.addEventListener('click', () => {
            const iconFile = item.dataset.icon;
            updateSelectedIcon(iconFile);
            
            if (iconPopup) {
                iconPopup.style.display = 'none';
            }
        });
    });
    
    // Close popup when clicking outside
    document.addEventListener('click', (e) => {
        if (iconPopup && 
            iconPopup.style.display === 'block' && 
            !iconPopup.contains(e.target) && 
            !iconSearchInput.contains(e.target)) {
            iconPopup.style.display = 'none';
        }
    });
    
    // Prevent popup from closing when clicking inside it
    if (iconPopup) {
        iconPopup.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
};