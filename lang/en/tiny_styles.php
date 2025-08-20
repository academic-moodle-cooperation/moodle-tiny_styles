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

$string['actions'] = 'Actions';
$string['back_overview'] = 'Back to main';
$string['bootstrapclass'] = 'CSS style / Bootstrap class';
$string['bootstrapclass_help'] = 'Choose which styling should be applied: a manually defined inline CSS style or a predefined Bootstrap class.';
$string['boxes'] = 'Boxes';
$string['bulkactionmustselect'] = 'Please select at least one element.';
$string['categories'] = 'Categories';
$string['category'] = 'Category';
$string['category_saved'] = 'Category saved';
$string['categorydeleted'] = 'Category deleted';
$string['categoryhelp'] = 'Category';
$string['categoryhelp_help'] = 'Select the category to which this element belongs.';
$string['clearsearch'] = "Clear";
$string['close'] = 'Close';
$string['confirmdeletecategory'] = 'Are you sure you want to delete this category?';
$string['confirmdeleteelement'] = 'Are you sure you want to delete this element?';
$string['create_element'] = 'Add Element';
$string['createcategory'] = 'Create category';
$string['delete'] = 'Delete';
$string['deleteaction'] = 'Delete';
$string['deletecategory'] = 'Delete category';
$string['description'] = 'Description   ';
$string['descriptiondisp'] = 'Description display';
$string['details'] = 'View details';
$string['divider'] = 'Divider';
$string['duplicateaction'] = 'Duplicate';
$string['edit'] = 'Edit';
$string['editcategory'] = 'Edit category';
$string['editelement'] = 'Edit element';
$string['eidtor:clearstyle'] = 'Clear styles';
$string['elementcancel'] = 'Entry/ Editing of style cancelled';
$string['elementcreated'] = 'Element created';
$string['elementdeleted'] = 'Element(s) deleted successfully';
$string['elementduplicated'] = 'Element(s) duplicated successfully';
$string['elementhidden'] = 'Element(s) hidden successfully';
$string['elements'] = 'Edit elements';
$string['elements_updated'] = 'Elements updated';
$string['elementsettings'] = 'Element settings';
$string['elementsheading'] = 'Elements';
$string['elementshown'] = 'Element(s) set visible successfully';
$string['elementstitle'] = 'Category elements';
$string['elementupdated'] = 'Element updated';
$string['error_nametooshort'] = 'Name is too short';
$string['errorname'] = 'Name must be at least 3 characters.';
$string['examplefile'] = 'Example file';
$string['exportdata'] = 'Export styles';
$string['hideaction'] = 'Hide';
$string['import'] = 'Import';
$string['importdata'] = 'Import styles';
$string['importdatainfo'] = 'Upload a JSON file containing style configurations for the TinyMCE editor.';
$string['importfailed'] = 'Failed to import configuration from file.';
$string['importinstructions'] = 'Upload one JSON file containing the style configurations. The file should contain a valid JSON format compatible with the TinyMCE styles plugin.';
$string['importjsonfile'] = 'File';
$string['importjsonfile_help'] = 'Upload a JSON file containing the style configurations.';
$string['importsuccess'] = 'Styling file imported successfully.';
$string['inline'] = 'Inline';
$string['instr_cat_heading'] = 'Category:';
$string['instr_cat_list'] = '<ul><li><strong>menumode:</strong> how elements are displayed in the editor (submenu / inline / divider)</li> <li><strong>enabled:</strong> either <code>1</code> (enabled) or <code>0</code> (disabled), default: <code>0</code></li> </ul>';
$string['instr_elem_heading'] = 'Element:';
$string['instr_elem_list'] = '<ul> <li><strong>type:</strong> <code>inline</code> or <code>block</code><br><em>inline is for styling short text or words<br>block is for paragraphs or larger text blocks</em></li> <li><strong>cssclasses:</strong> CSS styling for the text<br><em>This can be <a href="https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline_styles" target="_blank">inline CSS styles</a> (e.g. <code>color: red; font-weight: bold;</code>) or Bootstrap classes</em></li> <li><strong>enabled:</strong> either <code>1</code> (enabled) or <code>0</code> (disabled), default: <code>0</code></li> <li><strong>custom:</strong> <code>1</code> if using custom CSS inline code</li> </ul>';
$string['instr_expl_heading'] = 'Explanations';
$string['instr_expl_intro'] = 'The naming and description fields are self-explanatory.';
$string['instr_fields_title'] = 'The further options are:';
$string['instr_fill_code'] = "\"categories\": [\n    {\n        \"name\": \"Enter a minimum 3 characters long name here.\",\n        \"description\": \"Write a short category description here.\",\n        \"showdesc\": \"pick one of the following: helptext/tooltip/never\",\n        \"menumode\": \"pick one of the following: submenu/inline/divider\",\n        \"enabled\": 1,\n        \"elements\": [\n            {\n                \"name\": \"enter a descriptive name here\",\n                \"type\": \"pick either inline or block\",\n                \"cssclasses\": \"a valid css styling alert alert-danger\",\n                \"enabled\": 1,\n                \"custom\": 0\n            },\n            ... next elements ...\n        ]\n    },\n    ... possible to add more categories ...\n]";
$string['instr_fill_heading'] = 'How to fill out the JSON file:';
$string['instr_fill_note'] = '(See below for further explanations of the different options.)';
$string['instr_good_heading'] = 'Good to know';
$string['instr_good_list'] = '<ul> <li>Categories can be imported multiple times. This avoids accidental deletions or edits.<br><strong>Suggestion:</strong> Use the example file as the basis for imports to prevent duplicates.</li> <li>All fields can be edited later via Moodle admin pages.</li> <li>Currently, icon selection must be done manually through the Moodle admin category editor.</li> </ul>';
$string['instr_structure_heading'] = 'Structure';
$string['instr_structure_text'] = 'The example.json is structured into a category array, where each category contains an element array. This is the format which any JSON file imported should follow.';
$string['instr_usage_heading'] = 'How to use the JSON';
$string['instr_usage_text'] = 'The example JSON file can be easily used for editing directly and expanded by copying it.';
$string['instr_visualized_code'] = "Categories: [ category_1, category_2 ... category_n ]\n\ncategory_1: [ element_a, element_b ... element_n ]\ncategory_2: [ element_x, element_y ...\n→ with the elements carrying the styling information";
$string['instr_visualized_label'] = 'Visualized:';
$string['instructions_heading'] = 'Instructions for the JSON file';
$string['instructions_toggle'] = 'Instructions on how to prepare the configuration file.';
$string['invalidelelmentid'] = 'Invalid element ID';
$string['invalidfiletype'] = 'Invalid file type. Please upload a JSON file';
$string['invalidjson'] = 'The file contains invalid JSON data.';
$string['invalidjsonstructure'] = 'Invalid JSON structure. The file must contain categories, elements, and the affiliation to the category (cat_elements).';
$string['jsonfilehelp'] = 'For ensuring the correct JSON format you can export current categories and use the JSON file structure as a template.';
$string['labels'] = 'Labels';
$string['manualconfig'] = 'Manual style';
$string['manualdefault'] = 'Please enter a valid CSS code here. Example: <code>color: red; font-weight: bold;</code><br>Technical note: The manual styles will be added to the selected HTML elements as <a href="https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline_styles" target="_blank">inline styles</a>.';
$string['menuitem_styles'] = 'Styles';
$string['menumode'] = 'Menu mode';
$string['menumodehdr'] = 'Presentation of category in the menu';
$string['menumodetype'] = 'Menu mode';
$string['menumodetype_help'] = '<p>Select the mode how the elements should be displayed in the styles menu:</p><ul><li><strong>Submenu</strong>: The elements are displayed as a submenu with the category\'s title as parent node.</li><li><strong>Inline</strong>: The elements are displayed directly in the styles menu, one after another.</li><li><strong>Divider</strong>: Option to separate menu content using a dividing line. This is not a category to which items can be added!</ul>';
$string['movedown'] = 'Move down';
$string['moveup'] = 'Move up';
$string['name'] = 'Name';
$string['no_selection'] = 'No selection';
$string['nocategories'] = 'No categories';
$string['noelements'] = 'No elements found.';
$string['nofileuploaded'] = 'No file was uploaded.';
$string['noiconselected'] = 'No elements selected';
$string['noiconsfound'] = 'No icons found';
$string['pluginname'] = 'TinyMCE Styles';
$string['preview'] = 'Preview';
$string['privacy:metadata'] = 'The tiny_styles plugin does not store any personal data.';
$string['searchplaceholder'] = 'Search';
$string['selectall'] = 'Select all';
$string['selectanicon'] = 'Available icons';
$string['selectdefault'] = 'Choose...';
$string['selectedicon'] = 'Selected icon';
$string['selecticon'] = 'Icon';
$string['selectjsonfile'] = 'Select JSON file to import:';
$string['showaction'] = 'Show';
$string['styles:use'] = 'Use TinyMCE custom styles';
$string['submenu'] = 'Submenu';
$string['submit'] = 'Submit';
$string['tiny_styles_admin'] = 'Styles';
$string['tiny_styles_button'] = 'Styles';
$string['type'] = 'Display Type';
$string['type:inline'] = 'Inline';
$string['type:paragraph'] = 'Block';
$string['typehelp'] = 'Display Type';
$string['typehelp_help'] = 'Choose how the style should be applied to text:<ul><li><strong>Inline</strong>: for words or short text</li><li><strong>Block</strong>: for paragraphs or larger text blocks</li></ul>';
$string['view'] = 'View';
$string['withselection'] = 'With selected...';
