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
 * Commands for the Moodle tiny_styles plugin
 * 
 * Currently hardcoded, and not connected to the admin/db side
 *
 * @module      tiny_styles/commands
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { get_string as getString } from 'core/str';
import { getButtonImage } from 'editor_tiny/utils';

import {
    component,
    styleMenuItemName,
    boxesButtonName,
    labelsButtonName,
    menuIconName,
    boxIconName,
    labelIconName,
} from './common';


/**
 * TODO: fix stripping - wrapping logic
 * Applies a style to the selected text
 *
 * @param {TinyMCE.editor} editor
 * @param {Object} styleDef  hardcoded: { className: 'alert alert-info', block: true }
 *   
 */
function applyBootstrapClass(editor, styleDef) {
    const { className, block } = styleDef;
    const selectedHtml = editor.selection.getContent({ format: 'html' });
    if (!selectedHtml.trim()) {
        return;
    }

    const wrapperDiv = document.createElement('div');
    wrapperDiv.innerHTML = selectedHtml;

    /**
     * Recursively method to iterate styling elements and removing the previous css style
     */
    function processNode(node) {
        if (node.nodeType === Node.ELEMENT_NODE) {
            const tag = node.tagName.toUpperCase();

            //  known styles and redundant nested divs/spans
            if ((tag === 'DIV' || tag === 'SPAN') && node.className.match(/(alert|badge)/)) {
                const parent = node.parentNode;
                const children = Array.from(node.childNodes);

                // move to the parent node to unwrap
                children.forEach((child) => parent.insertBefore(child, node));
                parent.removeChild(node);
                return;
            }
        }

        Array.from(node.childNodes).forEach(processNode);
    }

    // from the innermost nodes outward
    let iterationNeeded = true;
    while (iterationNeeded) {
        iterationNeeded = false;
        Array.from(wrapperDiv.childNodes).forEach((node) => {
            const originalNodeCount = node.childNodes.length;
            processNode(node);
            if (node.childNodes.length < originalNodeCount) {
                iterationNeeded = true;
            }
        });
    }

    // cleaned content in to new container and appending nodes
    const containerTag = block ? 'div' : 'span';
    const newContainer = document.createElement(containerTag);
    newContainer.setAttribute('class', className);

    while (wrapperDiv.firstChild) {
        newContainer.appendChild(wrapperDiv.firstChild);
    }

    editor.selection.setContent(newContainer.outerHTML);
}


export const getSetup = async () => {
    const [
        menuItemStyles,
        boxesTitle,
        labelsTitle,
        menuIconData,
        boxIconData,
        labelIconData,
    ] = await Promise.all([
        getString('menuitem_styles', component),
        getString('boxes', component),
        getString('labels', component),
        getButtonImage('menu_icon', component),
        getButtonImage('box_icon', component),
        getButtonImage('label_icon', component),
    ]);

    // Hard-coded examples
    const labelStyles = [
        { textKey: 'label primary', className: 'badge badge-primary', block: false },
        { textKey: 'label success', className: 'badge badge-success', block: false },
    ];

    const boxStyles = [
        { textKey: 'box info', className: 'alert alert-info', block: true },
        { textKey: 'box danger', className: 'alert alert-danger', block: true },
        { textKey: 'box warning', className: 'alert alert-warning', block: true },
    ];

    const getSubmenuItems = (editor, styleArray) => styleArray.map((styleDef) => ({
        type: 'menuitem',
        text: styleDef.textKey,
        onAction: () => applyBootstrapClass(editor, styleDef),
    }));

    return (editor) => {
        // TODO: fix registering icons 
        editor.ui.registry.addIcon(menuIconName, menuIconData.html);
        editor.ui.registry.addIcon(boxIconName, boxIconData.html);
        editor.ui.registry.addIcon(labelIconName, labelIconData.html);

        // boxes dropdown
        editor.ui.registry.addMenuButton(boxesButtonName, {
            icon: boxIconName,
            tooltip: boxesTitle,
            fetch: (callback) => {
                callback(getSubmenuItems(editor, boxStyles));
            },
        });

        // labels dropdown
        editor.ui.registry.addMenuButton(labelsButtonName, {
            icon: labelIconName,
            tooltip: labelsTitle,
            fetch: (callback) => {
                callback(getSubmenuItems(editor, labelStyles));
            },
        });

        // nested menu under the format tab
        editor.ui.registry.addNestedMenuItem(styleMenuItemName, {
            icon: menuIconName,
            text: menuItemStyles,
            getSubmenuItems: () => [
                {
                    type: 'nestedmenuitem',
                    text: boxesTitle,
                    getSubmenuItems: () => getSubmenuItems(editor, boxStyles),
                },
                {
                    type: 'nestedmenuitem',
                    text: labelsTitle,
                    getSubmenuItems: () => getSubmenuItems(editor, labelStyles),
                },
            ],
        });
    };
};
