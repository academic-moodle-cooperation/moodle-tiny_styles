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
 * Generates two files from Moodle's FA SCSS source:
 *
 * 1. amd/src/fa_icons.js, icon name list for the icon picker
 * 2. css/editor_fa.css, self-contained FA CSS injected into the TinyMCE iframe
 *
 * When Moodle upgrades its bundled Font Awesome version, run from the plugin root:
 * node tools/generate_fa_icons.js
 * 
 * The generated files must be committed alongside any FA version bump.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

'use strict';

const fs = require('fs');
const path = require('path');

const pluginRoot = path.resolve(__dirname, '..');
const scssSource = path.resolve(
    pluginRoot,
    '../../../../../theme/boost/scss/fontawesome/_variables.scss'
);
const jsOutputFile = path.resolve(pluginRoot, 'amd/src/fa_icons.js');
const cssOutputFile = path.resolve(pluginRoot, 'css/editor_fa.css');

if (!fs.existsSync(scssSource)) {
    console.error('ERROR: FA SCSS variables file not found at:');
    console.error('  ' + scssSource);
    console.error('Make sure you are running from the plugin root inside a Moodle installation.');
    process.exit(1);
}

const content = fs.readFileSync(scssSource, 'utf8');

// Parse unicode variable definitions.
// Format in SCSS: $fa-var-{name}: \{hex};.
// Builds a map of varName.
const varMap = {};
const varRe = /^\$fa-var-([a-z0-9][a-z0-9-]*): \\([0-9a-f]+);/gm;
let vm;
while ((vm = varRe.exec(content)) !== null) {
    varMap[vm[1]] = vm[2];
}

if (Object.keys(varMap).length === 0) {
    console.error('ERROR: No $fa-var-* definitions found in ' + scssSource);
    process.exit(1);
}

// Format: "icon-name": $fa-var-var-name.
const mapMatch = content.match(/\$fa-icons:\s*\(([\s\S]+?)\);/);
if (!mapMatch) {
    console.error('ERROR: Could not find $fa-icons map in ' + scssSource);
    process.exit(1);
}

const icons = [];
const entryRe = /"([a-z0-9][a-z0-9-]+)":\s*\$fa-var-([a-z0-9][a-z0-9-]+)/g;
let em;
while ((em = entryRe.exec(mapMatch[1])) !== null) {
    const unicode = varMap[em[2]];
    if (unicode) {
        icons.push({name: em[1], unicode});
    }
}

if (icons.length === 0) {
    console.error('ERROR: No icons resolved from the $fa-icons map.');
    process.exit(1);
}

// Detect FA version.
let faVersion = 'unknown';
const solidScss = path.resolve(path.dirname(scssSource), 'solid.scss');
if (fs.existsSync(solidScss)) {
    const solidContent = fs.readFileSync(solidScss, 'utf8');
    const verMatch = solidContent.match(/Font Awesome Free ([\d.]+)/);
    if (verMatch) {
        faVersion = verMatch[1];
    }
}

// Generate amd/src/fa_icons.js.
const jsHeader = [
    '// This file is part of Moodle - https://moodle.org/',
    '//',
    '// Moodle is free software: you can redistribute it and/or modify',
    '// it under the terms of the GNU General Public License as published by',
    '// the Free Software Foundation, either version 3 of the License, or',
    '// (at your option) any later version.',
    '//',
    '// Moodle is distributed in the hope that it will be useful,',
    '// but WITHOUT ANY WARRANTY; without even the implied warranty of',
    '// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the',
    '// GNU General Public License for more details.',
    '//',
    '// You should have received a copy of the GNU General Public License',
    '// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.',
    '',
    '/**',
    ' * Full FA 6 Free solid icon name list, including canonical names and aliases.',
    ' * Generated from theme/boost/scss/fontawesome/_variables.scss ($fa-icons map).',
    ` * FA version: ${faVersion} as shipped with Moodle.`,
    ' * Regenerate by running: node tools/generate_fa_icons.js',
    ' *',
    ' * @ package tiny_styles',
    ' * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}',
    ' * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later',
    ' */',
    '',
].join('\n');

// Limits to 2 icons per line to keep every line under 132-char limit.
const chunkSize = 2;
const arrayLines = [];
for (let i = 0; i < icons.length; i += chunkSize) {
    const chunk = icons.slice(i, i + chunkSize);
    const quoted = chunk.map(({name}) => `'${name}'`).join(', ');
    const isLast = i + chunkSize >= icons.length;
    arrayLines.push('    ' + quoted + (isLast ? '' : ','));
}

const jsOutput = jsHeader + 'export const faIcons = [\n' + arrayLines.join('\n') + '\n];\n';
fs.writeFileSync(jsOutputFile, jsOutput, 'utf8');
console.log(`Written ${icons.length} icons (FA ${faVersion}) to amd/src/fa_icons.js`);

// Generate css/editor_fa.css.
// If Moodle relocates lib/fonts/, need to update the src lines in the @font-face block below.
const cssHeader = `/**
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * Font Awesome 6 Free Solid — TinyMCE editor iframe injection.
 * FA version: ${faVersion}
 *
 * GENERATED FILE — do not edit manually.
 * Regenerate by running: node tools/generate_fa_icons.js
 * Source: theme/boost/scss/fontawesome/_variables.scss
 *
 * This file is injected into the TinyMCE editor iframe by injectFontAwesome()
 * in amd/src/plugin.js so that FA icons render correctly inside the editor
 * content area. It contains only FA Solid definitions 
 * to avoid interfering with the editor's own rendering.
 *
 * Font files: lib/fonts/fa-solid-900.woff2 / .ttf .
 * If the font path changes, update the @font-face src URLs below.
 *
 * @package tiny_styles
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

@font-face {
    font-family: 'Font Awesome 6 Free';
    font-style: normal;
    font-weight: 900;
    font-display: block;
    src: url('../../../../../fonts/fa-solid-900.woff2') format('woff2'),
         url('../../../../../fonts/fa-solid-900.ttf') format('truetype');
}

.fa-solid,
.fas {
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

.fa,
.fas,
.fa-solid {
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    display: var(--fa-display, inline-block);
    font-style: normal;
    font-variant: normal;
    line-height: 1;
    text-rendering: auto;
}

.fa-fw {
    text-align: center;
    width: 1.25em;
}

/* Icon unicode mappings — generated from $fa-icons map */
`;

const cssIconLines = icons.map(({name, unicode}) =>
    `.fa-${name}:before { content: "\\${unicode}"; }`
).join('\n');

const cssOutput = cssHeader + cssIconLines + '\n';
fs.writeFileSync(cssOutputFile, cssOutput, 'utf8');
console.log(`Written css/editor_fa.css (${icons.length} icons, FA ${faVersion})`);
