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
 * Commands for the Moodle tiny_styles plugin.
 *
 * Now dynamically connected to DB categories/elements via
 * editor.options.get('tiny_styles_categories'), which is set in lib.php.
 *
 * @module      tiny_styles/commands
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { get_string as getString } from 'core/str';

/**
 * TODO:refine the "unwrapping" logic
 *
 * Applies a style to the selected text,
 *
 * @param {TinyMCE.editor} editor - current TinyMCE editor instance
 * @param {Object} styleDef - e.g. { className: 'alert alert-info', block: true }
 */
function applyBootstrapClass(editor, styleDef) {
    const { className, block } = styleDef;
    const selectedHtml = editor.selection.getContent({ format: 'html' });
    if (!selectedHtml.trim()) {
        return;
    }

    const wrapperDiv = document.createElement('div');
    wrapperDiv.innerHTML = selectedHtml;

    // Iterate styling elements removing the old classes.
    function processNode(node) {
        if (node.nodeType === Node.ELEMENT_NODE) {
            const tag = node.tagName.toUpperCase();

            if ((tag === 'DIV' || tag === 'SPAN') && node.className.match(/(alert|badge)/)) {
                const parent = node.parentNode;
                const children = Array.from(node.childNodes);

                children.forEach((child) => parent.insertBefore(child, node));
                parent.removeChild(node);
                return;
            }
        }
        // Recurse into children.
        Array.from(node.childNodes).forEach(processNode);
    }

    let iterationNeeded = true;
    while (iterationNeeded) {
        iterationNeeded = false;
        Array.from(wrapperDiv.childNodes).forEach((node) => {
            const originalCount = node.childNodes.length;
            processNode(node);
            if (node.childNodes.length < originalCount) {
                iterationNeeded = true;
            }
        });
    }

    // cleaned content in <div> or <span> with new classes.
    const containerTag = block ? 'div' : 'span';
    const newContainer = document.createElement(containerTag);
    newContainer.setAttribute('class', className);

    while (wrapperDiv.firstChild) {
        newContainer.appendChild(wrapperDiv.firstChild);
    }

    editor.selection.setContent(newContainer.outerHTML);
}

export const getSetup = async () => {
    // A localized string for the top-level "Styles" menu item.
    const [menuItemStyles] = await Promise.all([
        getString('menuitem_styles', 'tiny_styles'),
    ]);

    return (editor) => {
        // Categories from the editor config populated by lib.php.
        const cats = editor.options.get('tiny_styles_categories') || [];

        // A nested menu item in the editor UI.
        editor.ui.registry.addNestedMenuItem('tiny_styles_menuitem', {
            text: menuItemStyles,
            getSubmenuItems: () => {
                const items = [];

                cats.forEach(cat => {
                    // Menu separator line.
                    if (cat.presentation === 'divider') {
                        items.push({ type: 'separator' });
                    } else {
                        // FA icon if cat.symbol is set.
                        const catIcon = cat.symbol
                            ? `<i class="${cat.symbol}" style="margin-right:4px;"></i>`
                            : '';

                        // Sub-items from bridging elements.
                        let subItems = [];
                        if (cat.elements && cat.elements.length > 0) {
                            subItems = cat.elements.map(elem => ({
                                type: 'menuitem',
                                text: elem.name,
                                onAction: () => applyBootstrapClass(editor, {
                                    className: elem.cssclasses,
                                    block: (elem.type === 'block')
                                })
                            }));
                        }

                        // A nested menu item for the cat.
                        if (subItems.length > 0) {
                            items.push({
                                type: 'nestedmenuitem',
                                text: `${catIcon}${cat.name}`,
                                getSubmenuItems: () => subItems
                            });
                        } else {
                            items.push({
                                type: 'menuitem',
                                text: `${catIcon}${cat.name}`,
                                onAction: () => {}
                            });
                        }
                    }
                });
                return items;
            }
        });
    };
};
