<?php
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
 * Plugin strings are defined here.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'TinyMCE Styles';
$string['privacy:metadata'] = 'The tiny_styles plugin does not store any personal data.';

// Main menu item
$string['menuitem_styles'] = 'Styles';
$string['tiny_styles_button'] = 'Styles';

// Toolbar button tooltips or text
$string['boxes'] = 'Boxes';
$string['labels'] = 'Labels';

$string['tiny_styles_admin'] = 'TinyMCE Styles';

// categories
$string['categories'] = 'Categories';
$string['createcategory'] = 'Create category';
$string['name'] = 'Name';
$string['description'] = 'Description   ';
$string['presentation'] = 'Presentation type';
$string['actions'] = 'Actions';
$string['view'] = 'View';
$string['elements'] = 'Edit elements';
$string['moveup'] = 'Move up';
$string['movedown'] = 'Move down';
$string['editcategory'] = 'Edit category';
$string['deletecategory'] = 'Delete category';
$string['nocategories'] = 'No categories';
$string['category_saved'] = 'Category saved';
$string['presentationhdr'] = 'Presentation of category';

$string['descriptiondisp'] = 'Description display';
$string['presentationtype'] = 'Presentation type';

// elements
$string['elementstitle'] = 'Category elements';
$string['elementsheading'] = 'Elements';
$string['type'] = 'Type';
$string['bootstrapclass'] = 'Bootstrap class / CSS style';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['noelements'] = 'No elements found.';
$string['no_selection'] = 'No selection';
$string['category'] = 'Category';

$string['submit'] = 'Submit';
$string['type'] = 'Type';
$string['create_element'] = 'Add Element';
$string['selectall'] = 'Select all';
$string['details'] = 'View details';
$string['manualconfig'] = 'Manual configuration';
$string['manualdefault'] = 'Please enter a valid inline CSS code. Example: <code>color: red; font-weight: bold;</code><br>Learn more about inline styles <a href="https://www.freecodecamp.org/news/inline-style-in-html/" target="_blank" rel="noopener">here</a>.';

// still to be defined
$string['elementsettings'] = 'Element settings';
$string['back_overview'] = 'Back to main'; 
$string['editelement'] = 'Edit element';
$string['invalidelelmentid'] = 'Invalid element ID';

$string['elements_updated'] = 'Elements updated';
$string['elementcreated'] = 'Element created';
$string['elementupdated'] = 'Element updated';
$string['error_nametooshort'] = 'Name is too short';
$string['elementcancel'] = 'Style form cancelled';

$string['confirmdeleteelement'] = 'Are you sure you want to delete this element?';
$string['confirmdeletecategory'] = 'Are you sure you want to delete this category?';
$string['elementdeleted'] = 'Element deleted';
$string['categorydeleted'] = 'Category deleted';
$string['preview'] = 'Preview';

$string['errorname'] = 'Name must be at least 3 characters.';

$string['exportdata'] = 'Export styles';
$string['importdata'] = 'Import styles';
$string['importsuccess'] = 'Import success';
$string['import'] = 'Import';
$string['close'] = 'Close';

$string['nofileuploaded'] = 'No file was uploaded.';
$string['invalidjson'] = 'Invalid JSON file.';

$string['selectjsonfile'] = 'Select JSON file to import:';
$string['jsonfilehelp'] = 'For ensuring the correct JSON format you can export current categories and use the JSON file structure as a template.';
$string['invalidfiletype'] = 'Invalid file type. Please upload a JSON file';
$string['invalidjsonstructure'] = 'Invalid JSON structure. The file must contain categories, elements, and the affiliation to the category (cat_elements).';

$string['withselection'] = 'With selected...';
$string['selectdefault'] = 'Choose...';
$string['showaction'] = 'Show';
$string['hideaction'] = 'Hide';
$string['duplicateaction'] = 'Duplicate';
$string['deleteaction'] = 'Delete';

$string['submenu'] = 'Submenu';
$string['inline'] = 'Inline';
$string['divider'] = 'Divider';

$string['selecticon'] = 'Icon';
$string['noiconselected'] = 'No elements selected';
$string['selectanicon'] = 'Select an icon';
$string['selectedicon'] = 'Selected icon';
$string['bulkactionmustselect'] = 'Please select at least one element.';

$string['categoryhelp'] = 'Category';
$string['typehelp'] = 'Type';
$string['bootstrapclass'] = 'Bootstrap class / CSS style';
$string['categoryhelp_help'] = 'Select the category to which this element belongs.';
$string['typehelp_help'] = 'Choose whether this style should be applied as inline or block.';
$string['bootstrapclass_help'] = 'Choose styling from predefined classes or manually define a style using CSS.';

$string['presentationtype'] = 'Presentation';
$string['presentationtype_help'] = 'Select whether to display style elements directly in the Styles menu or organize them under a submenu within the editor. Alternatively you can separate some categories using a dividing line.';

$string['elementshown'] = 'Element(s) set visible successfully';
$string['elementhidden'] = 'Element(s) hidden successfully';
$string['elementduplicated'] = 'Element(s) duplicated successfully';
$string['elementdeleted'] = 'Element(s) deleted successfully';

$string['importdatainfo'] = 'Upload a JSON file containing style configurations for the TinyMCE editor.';
$string['importjsonfile'] = 'File';
$string['importinstructions'] = 'Upload one JSON file containing the style configurations. The file should contain a valid JSON format compatible with the TinyMCE styles plugin.';
$string['importsuccess'] = 'Styling file imported successfully.';
$string['importfailed'] = 'Failed to import configuration from file.';
$string['invalidjson'] = 'The file contains invalid JSON data.';
$string['importjsonfile_help'] = 'Upload a JSON file containing the style configurations.';

$string['searchplaceholder'] = 'Search';
$string['noiconsfound'] = 'No icons found';

$string['examplefile'] = 'Example file';
$string['instructions_toggle'] = 'Instructions on how to prepare an import configuration file.';
$string['instructions_heading'] = 'Instructions for the import form fillout (example.json)';

$string['instr_structure_heading'] = 'Structure';
$string['instr_structure_text'] = 'The example.json is structured into a category array, where each category contains an element array. This is the format which any JSON file imported should follow.';
$string['instr_visualized_label'] = 'Visualized:';
$string['instr_visualized_code'] = "Categories: [ category_1, category_2 ... category_n ]\n\ncategory_1: [ element_a, element_b ... element_n ]\ncategory_2: [ element_x, element_y ...\n→ with the elements carrying the styling information";

$string['instr_usage_heading'] = 'How to use the JSON';
$string['instr_usage_text'] = 'The example JSON can be easily used for editing directly and expanded by copying it. <br><strong>Note:</strong> The user should follow correct JSON syntax and formatting for the file to work properly.';

$string['instr_fill_heading'] = 'How to fill out the example.json:';
$string['instr_fill_note'] = '(See below for further explanations of enabled, type, etc.)';
$string['instr_fill_code'] = "\"categories\": [\n    {\n        \"name\": \"Enter a minimum 3 characters long name here.\",\n        \"description\": \"Write a short category description here.\",\n        \"showdesc\": \"pick one of the following: helptext/tooltip/never\",\n        \"presentation\": \"pick one of the following: submenu/inline/divider\",\n        \"enabled\": 1,\n        \"elements\": [\n            {\n                \"name\": \"enter a descriptive name here\",\n                \"type\": \"pick either inline or block\",\n                \"cssclasses\": \"a valid css styling alert alert-danger\",\n                \"enabled\": 1,\n                \"custom\": 0\n            },\n            ... next elements ...\n        ]\n    },\n    ... possible to add more categories ...\n]";

$string['instr_expl_heading'] = 'Explanations';
$string['instr_expl_intro'] = 'The naming and description fields are self-explanatory.';
$string['instr_fields_title'] = 'The other fields are:';

$string['instr_cat_heading'] = 'Category:';
$string['instr_cat_list'] = '<ul>
<li><strong>showdesc:</strong> how the description is shown to users, or if at all</li>
<li><strong>presentation:</strong> how elements are displayed in the editor (submenu / inline / divider)</li>
<li><strong>enabled:</strong> either <code>1</code> (enabled) or <code>0</code> (disabled), default: <code>0</code></li>
</ul>';

$string['instr_elem_heading'] = 'Element:';
$string['instr_elem_list'] = '<ul>
<li><strong>type:</strong> <code>inline</code> or <code>block</code><br><em>inline is for styling short text or words<br>block is for paragraphs or larger text blocks</em></li>
<li><strong>cssclasses:</strong> CSS styling for the text<br><em>This can be Bootstrap classes or inline CSS, eg. <code>color: red; font-weight: bold;</code></em></li>
<li><strong>enabled:</strong> same logic as category</li>
<li><strong>custom:</strong> <code>1</code> if using custom CSS inline code</li>
</ul>';

$string['instr_good_heading'] = 'Good to Know';
$string['instr_good_list'] = '<ul>
<li>Duplicate categories can be imported multiple times. This avoids accidental deletions or edits.<br><strong>Suggestion:</strong> Use the example JSON as the basis for imports to prevent duplicates.</li>
<li>All fields can be edited later via Moodle admin pages.</li>
<li>Currently, icon selection must be done manually through the Moodle admin category editor.</li>
<li>See the full exported JSON for more examples and detailed usage.</li>
</ul>';
