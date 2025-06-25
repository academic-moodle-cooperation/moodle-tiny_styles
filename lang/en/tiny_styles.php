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
$string['privacy:metadata'] = 'Font case does not store any personal data';

// Main menu item
$string['menuitem_styles'] = 'Styles';
$string['tiny_styles_button'] = 'Styles';

// Toolbar button tooltips or text
$string['boxes'] = 'Boxes';
$string['labels'] = 'Labels';

// settings
$string['tiny_styles_admin'] = 'Tiny Styles';
$string['managecategories'] = 'Manage Categories';
$string['manageelements'] = 'Manage Elements';
$string['createelement'] = 'Create New Element';
$string['createcategory'] = 'Create New Category';

// categories
$string['categories'] = 'Categories';
$string['createcategory'] = 'Create category';
$string['name'] = 'Name';
$string['description'] = 'Description   ';
$string['presentation'] = 'Presentation';
$string['actions'] = 'Actions';
$string['view'] = 'View';
$string['elements'] = 'Edit elements';
$string['moveup'] = 'Move up';
$string['movedown'] = 'Move down';
$string['editcategory'] = 'Edit category';
$string['deletecategory'] = 'Delete category';
$string['nocategories'] = 'No categories';
$string['category_saved'] = 'Category saved';
$string['presentationhdr'] = 'Presentation of Category';

$string['descriptiondisp'] = 'Description display';
$string['presentationtype'] = 'Presentation type';

// elements
$string['elementstitle'] = 'Category Elements';
$string['elementsheading'] = 'Elements';
$string['type'] = 'Type';
$string['bootstrapclass'] = 'Bootstrap Class / CSS Style';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['noelements'] = 'No elements found.';
$string['no_selection'] = 'No selection';
$string['category'] = 'Category';

$string['submit'] = 'Submit';
$string['type'] = 'Type';
$string['create_element'] = 'Create Element';
$string['selectall'] = 'Select all';
$string['details'] = 'View details';
$string['manualconfig'] = 'Manual configuration';
$string['manualdefault'] = 'Please enter a valid inline CSS code. Example: <code>color: red; font-weight: bold;</code><br>Learn more about inline styles <a href="https://www.freecodecamp.org/news/inline-style-in-html/" target="_blank" rel="noopener">here</a>.';

// still to be defined
$string['elementsettings'] = 'Element settings';
$string['back_overview'] = 'Back to overview'; 
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

$string['exportdata'] = 'Export data';
$string['importdata'] = 'Import data';
$string['importsuccess'] = 'Import success';
$string['import'] = 'Import';
$string['close'] = 'Close';

$string['nofileuploaded'] = 'No file was uploaded.';
$string['invalidjson'] = 'Invalid JSON file.';

$string['selectjsonfile'] = 'Select JSON file to import:';
$string['jsonfilehelp'] = 'For ensuring the correct JSON format you can export current categories and use the JSON file structure as a template.';
$string['invalidfiletype'] = 'Invalid file type. Please upload a JSON file';
$string['invalidjsonstructure'] = 'Invalid JSON structure. The file must contain categories, elements, and cat_elements';

$string['reorder'] = 'Reorder';

$string['withselection'] = 'With selection: ';
$string['selectdefault'] = 'Select';
$string['showaction'] = 'Show';
$string['hideaction'] = 'Hide';
$string['duplicateaction'] = 'Duplicate';
$string['deleteaction'] = 'Delete';

$string['submenu'] = 'Submenu';
$string['inline'] = 'Inline';
$string['divider'] = 'Divider';

$string['symbol'] = 'Symbol';
$string['selecticon'] = 'Select icon';
$string['noiconselected'] = 'No elements selected';
$string['selectanicon'] = 'Select an icon';
$string['selectedicon'] = 'Selected icon';
$string['bulkactionmustselect'] = 'Please select at least one element.';

$string['categoryhelp'] = 'Category';
$string['typehelp'] = 'Type';
$string['cssclasseshelp'] = 'cssclasses';
$string['categoryhelp_help'] = 'Select the category to which this element belongs.';
$string['typehelp_help'] = 'Choose whether this style should be applied as inline or block.';
$string['cssclasseshelp_help'] = 'Choose styling from predefined classes or use manual style sheet.';

$string['iconhelp'] = 'Icon';
$string['presentationhelp'] = 'Presentation';
$string['iconhelp_help'] = 'Choose an icon to be displayed with the category in the Editor.';
$string['presentationhelp_help'] = 'Select whether to display style elements directly in the Styles menu or organize them under a submenu within the editor.';

$string['elementshown'] = 'Element(s) set visible successfully';
$string['elementhidden'] = 'Element(s) hidden successfully';
$string['elementduplicated'] = 'Element(s) duplicated successfully';
$string['elementdeleted'] = 'Element(s) deleted successfully';

$string['importdatainfo'] = 'Upload JSON files containing style configurations for the TinyMCE editor.';
$string['importjsonfile'] = 'JSON configuration file';
$string['importinstructions'] = 'Upload one JSON file containing the style configurations. The file should contain a valid JSON format compatible with the TinyMCE styles plugin.';
$string['importsuccess'] = 'Styling file imported successfully.';
$string['importfailed'] = 'Failed to import configuration from file.';
$string['invalidjson'] = 'The file contains invalid JSON data.';
$string['importjsonfile_help'] = 'Upload one JSON file containing the style configurations.';


$string['examplefiles_heading'] = 'Download Example Files';
$string['examplefiles_description'] = 'Download example JSON files and instructions to help you understand the import format.';
$string['examplefiles_label'] = 'Download example files';
$string['download_button'] = 'Download';

$string['searchplaceholder'] = 'Search';
$string['noiconsfound'] = 'No icons found';

$string['examplefile'] = 'Example file';
$string['instructions_toggle'] = 'Instructions on how to prepare an import configuration file.';
$string['instructions_heading'] = 'Instructions for the import form fillout (example.json)';
