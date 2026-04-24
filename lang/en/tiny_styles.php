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
$string['bootstrapclasses'] = 'Bootstrap classes';
$string['boxes'] = 'Boxes';
$string['bulkactionmustselect'] = 'Please select at least one element.';
$string['categories'] = 'Categories';
$string['categoriesorder'] = 'Category order updated successfully';
$string['category'] = 'Category';
$string['category_saved'] = 'Category saved';
$string['categorydeleted'] = 'Category deleted';
$string['categoryhelp'] = 'Category';
$string['categoryhelp_help'] = 'Select the category to which this element belongs.';
$string['clearsearch'] = "Clear";
$string['close'] = 'Close';
$string['confirmdeletebulk'] = 'Are you sure you want to delete the selected elements?';
$string['confirmdeletecategory'] = 'Are you sure you want to delete this category?';
$string['confirmdeleteelement'] = 'Are you sure you want to delete this element?';
$string['create_element'] = 'Add element';
$string['createcategory'] = 'Create category';
$string['delete'] = 'Delete';
$string['deleteaction'] = 'Delete';
$string['deletecategory'] = 'Delete category';
$string['description'] = 'Description';
$string['descriptiondisp'] = 'Description display';
$string['details'] = 'View details';
$string['divider'] = 'Divider';
$string['duplicateaction'] = 'Duplicate';
$string['edit'] = 'Edit';
$string['editcategory'] = 'Edit category';
$string['editelement'] = 'Edit element';
$string['editor:clearstyle'] = 'Clear styles';
$string['elementcancel'] = 'Entry/ Editing of style cancelled';
$string['elementcreated'] = 'Element created';
$string['elementdeleted'] = 'Element(s) deleted successfully';
$string['elementduplicated'] = 'Element(s) duplicated successfully';
$string['elementhidden'] = 'Element(s) hidden successfully';
$string['elements'] = 'Edit elements';
$string['elements_updated'] = 'Elements updated';
$string['elementsettings'] = 'Element settings';
$string['elementsheading'] = 'Elements';
$string['elementsheadingname'] = 'Styles';
$string['elementshown'] = 'Element(s) set visible successfully';
$string['elementsorder'] = 'Order of elements updated successfully';
$string['elementstitle'] = 'Category elements';
$string['elementupdated'] = 'Element updated';
$string['error:invalidaction'] = 'Invalid action provided';
$string['error:missingelements'] = 'No elements selected';
$string['error:missingparam'] = 'Missing parameters';
$string['error:recordnotfound'] = 'Record not found';
$string['error:name'] = 'Name must be at least 3 characters long.';
$string['examplefile'] = 'Example file';
$string['exportdata'] = 'Export styles';
$string['hideaction'] = 'Hide';
$string['import'] = 'Import';
$string['importdata'] = 'Import styles';
$string['importdatainfo'] = 'Upload a JSON file containing style configurations for the TinyMCE editor.';
$string['importfailed'] = 'Failed to import configuration from file.';
$string['importinstructions'] = 'Upload one JSON file containing the style configurations. The file should contain a valid JSON format compatible with the TinyMCE styles plugin.';
$string['importjsoncategories'] = 'Invalid JSON format: the "categories" section is missing or malformed.';
$string['importjsonfile'] = 'JSON configuration file';
$string['importjsonfile_help'] = 'Upload a JSON file containing the style configurations.';
$string['importsuccess'] = 'Styling file imported successfully.';
$string['inline'] = 'Inline';
$string['inlinecss'] = 'Inline CSS style';
$string['instructions_0_toggle'] = 'Instructions on how to prepare the configuration file for import.';
$string['instructions_1_usage_heading'] = 'Use the example file as a template';
$string['instructions_1_1_usage_text'] = 'The example JSON file can be easily used for editing and expanding directly.';
$string['instructions_2_structure_heading'] = 'Structure';
$string['instructions_2_1_structure_text'] = 'The JSON file for TinyMCE Styles has to follow a specific structure. It consists of a category array, where each category contains further element arrays. The categories and elements will be imported in the order they are defined in the file.';
$string['instructions_3_cat_intro'] = 'The following options are available for <strong>categories:</strong>';
$string['instructions_3_1_cat_name'] = 'name of the category, is visible in the editor';
$string['instructions_3_2_cat_description'] = 'description of the category, currently only visible for admins';
$string['instructions_3_2_2_cat_symbol'] = 'an icon for the category';
$string['instructions_3_3_cat_menumode'] = 'the elements of a category can be displayed as <code>submenu</code> (submenu with the category\'s name as parent node), <code>inline</code> (displayed directly in the editor) or the category may be used as a <code>divider</code> (to separate content, where no elements can be added)';
$string['instructions_3_4_cat_enabled'] = 'either <code>1</code> (enabled) or <code>0</code> (disabled), default: 0';
$string['instructions_4_elem_intro'] = 'The following options are available for <strong>elements:</strong>';
$string['instructions_4_1_elem_name'] = 'name of the element, is visible in the editor';
$string['instructions_4_2_elem_custom'] = 'either <code>1</code> (if a manual style was defined, using a valid CSS code) or <code>0</code> (if a predefined Bootstrap classes is used)';
$string['instructions_4_3_elem_type'] = '<code>inline</code> (for styling short text or words) or <code>block</code> (for paragraphs or larger text blocks)';
$string['instructions_4_4_elem_cssclasses'] = 'this can be Bootstrap classes (alerts and badges, e.g. <code>alert alert-info</code> or <code>badge bg-primary</code>) or a valid CSS code (e.g. <code>color: red; font-weight: bold;</code>), which will be added as <a href="https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline_styles" target="_blank">inline styles</a>';
$string['instructions_4_5_elem_enabled'] = 'either <code>1</code> (enabled) or <code>0</code> (disabled), default: 0';
$string['instructions_5_example_heading'] = 'Example';
$string['instructions_5_1_example_note'] = 'See below an example of how to fill out the JSON file:';
$string['instructions_5_2_example_code'] = "\"categories\": [\n    {\n        \"name\": \"Category 1\",\n        \"description\": \"Description of category 1\",\n        \"symbol\": \"Select an Icon\",\n        \"menumode\": \"submenu\",\n        \"enabled\": 1,\n        \"elements\": [\n            {\n                \"name\": \"Element A\",\n                \"type\": \"block\",\n                \"cssclasses\": \"alert alert-info\",\n                \"enabled\": 1,\n                \"custom\": 0\n            },\n            {\n                \"name\": \"Element B\",\n                \"type\": \"inline\",\n                \"cssclasses\": \"color: red; font-weight: bold;\",\n                \"enabled\": 1,\n                \"custom\": 1\n            },\n        ]\n    },\n    {\n        \"name\": \"Category n\",\n        ...\n    },\n]";
$string['instructions_6_good_heading'] = 'Good to know';
$string['instructions_6_1_good_list'] = '<ul> <li>Categories can be imported multiple times. This avoids accidental deletions or edits.</li> <li>All fields can be edited later via the administration site of the plugin.</li><li>Categories and elements  keep their sorting order automatically during export and import. The order stored in the JSON file is used directly.</li><li>This plugin supports the Multilang2 filter plugin. All text fields (category names, descriptions, and element names) can contain multilang tags to display content in multiple languages. See <a href="https://moodle.org/plugins/filter_multilang2" target="_blank">documentation</a> for syntax and usage.</li></ul>';
$string['invalidelelmentid'] = 'Invalid element ID';
$string['invalidfiletype'] = 'Invalid file type. Please upload a JSON file';
$string['invalidid'] = 'Invalid category ID';
$string['invalidjson'] = 'The file contains invalid JSON data.';
$string['invalidjsonstructure'] = 'Invalid JSON structure. The file must contain categories, elements, and the affiliation to the category (cat_elements).';
$string['jsonfilehelp'] = 'For ensuring the correct JSON format you can export current categories and use the JSON file structure as a template.';
$string['labels'] = 'Labels';
$string['manualconfig'] = 'Manual style';
$string['manualdefault'] = 'Please enter a valid CSS code here. Example: <code>color: red; font-weight: bold;</code><br>Technical note: The manual styles will be added to the selected HTML elements as <a href="https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline_styles" target="_blank">inline styles</a>.';
$string['manualstyle'] = 'Manual style';
$string['menuitem_styles'] = 'Styles';
$string['menumode'] = 'Menu mode';
$string['menumodehdr'] = 'Category display in the menu';
$string['menumodetype'] = 'Menu mode';
$string['menumodetype_help'] = '<p>Select how the elements should be displayed in the styles menu:</p><ul><li><strong>Submenu</strong>: The elements are displayed as a submenu with the category\'s name as the parent node.</li><li><strong>Inline</strong>: The elements are displayed directly in the styles menu, one after another.</li><li><strong>Divider</strong>: Option to separate menu content using a dividing line. Items cannot be added to this category type!</ul>';
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
$string['showdesc'] = 'Show description';
$string['showdesc_help'] = '<p>Controls whether the category description is shown to editors:</p><ul><li><strong>Never</strong>: The description is not shown.</li><li><strong>Help text (? button)</strong>: A <strong>?</strong> button appears next to the category label. Clicking it shows the description as a pop-up help text.</li><li><strong>Tooltip</strong>: The description appears as a tooltip when hovering over the category label.</li></ul>';
$string['showdesc_helptext'] = 'Help text (? button)';
$string['showdesc_never'] = 'Never';
$string['showdesc_tooltip'] = 'Tooltip';
$string['styles:use'] = 'Use TinyMCE styles';
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
