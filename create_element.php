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
 * Plugin administration: element editing and creation
 *
 * @package     tiny_styles
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../../../../config.php');
require_login();

$context = context_system::instance();
$PAGE->set_context($context);

$action = optional_param('action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/element.php', [
    'action' => $action,
    'id'     => $id,
    'catid'  => $catid
]));

if ($action === 'edit') {
    $formtitle = get_string('editelement', 'tiny_styles');
} else {
    $formtitle = get_string('create_element', 'tiny_styles');
}
$PAGE->set_title($formtitle);

// 3) Load Moodle forms library
require_once($CFG->libdir . '/formslib.php');

/**
 * Form class for creating or editing an Element (tiny_styles_elements).
 */
class element_form extends moodleform {
    public function definition() {
        global $DB;

        $mform = $this->_form;

        // Header
        $mform->addElement('header', 'elementsettings', get_string('elementsettings', 'tiny_styles'));

        // Name
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // (Optional) Category selection? If you want to assign an element to one category at creation time:
        // We fetch categories from the DB to populate a select.
        $categories = $DB->get_records_menu('tiny_styles_categories', null, 'sortorder ASC', 'id,name');
        // If no categories found, you might handle that scenario or ensure there's at least one
        // For the sake of example, let's allow user to pick "none" or so:
        $catoptions = [0 => get_string('none', 'tiny_styles')] + $categories;

        $mform->addElement('select', 'categoryid', get_string('category', 'tiny_styles'), $catoptions);
        $mform->setType('categoryid', PARAM_INT);

        // Type (inline, block, etc.)
        // Possibly from DB in real usage, or a fixed set of choices:
        $typeoptions = [
            'inline' => 'Inline',
            'block'  => 'Block',
            'other'  => 'Other'
        ];
        $mform->addElement('select', 'type', get_string('type', 'tiny_styles'), $typeoptions);
        $mform->setType('type', PARAM_ALPHA);

        // CSS classes field
        $mform->addElement('text', 'cssclasses', get_string('bootstrapclass', 'tiny_styles'));
        $mform->setType('cssclasses', PARAM_TEXT);
        $mform->addRule('cssclasses', null, 'required', null, 'client');

        // Hidden fields: 'id' for the record ID, 'action' for create/edit
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        // Buttons: Save, or "Preview" if you want, etc. 
        // The 2nd param in add_action_buttons can be an alternative label for "Save changes."
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files) {
        $errors = array();

        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = get_string('error_nametooshort', 'tiny_styles');
        }
        // Additional checks for cssclasses or type can go here
        return $errors;
    }
}

// 4) Instantiate the form
$mform = new element_form(null, []);

// If Cancel button pressed
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section'=>'tiny_styles_admin']));
    exit;
}

// If data was submitted
if ($data = $mform->get_data()) {
    global $DB;

    // Build record to insert or update
    $record = new stdClass();
    $record->name       = $data->name;
    $record->type       = $data->type;
    $record->cssclasses = $data->cssclasses;
    $record->timemodified = time();

    // We do not have bridging logic directly here. If you want to do bridging right away:
    // $data->categoryid => used for tiny_styles_cat_elements bridging. 
    // For example, you might do that after the element is created.
    
    if ($data->action === 'edit' && !empty($data->id)) {
        // Update existing element
        if ($old = $DB->get_record('tiny_styles_elements', ['id' => $data->id], '*', MUST_EXIST)) {
            $record->id          = $old->id;
            $record->enabled     = $old->enabled;
            $record->sortorder   = $old->sortorder;
            $record->timecreated = $old->timecreated;

            $DB->update_record('tiny_styles_elements', $record);

            // Optionally handle bridging if user selected a category
            if (!empty($data->categoryid)) {
                // e.g. check tiny_styles_cat_elements, insert or update the bridging row
                // up to you
            }

            redirect(new moodle_url('/admin/settings.php', ['section'=>'tiny_styles_admin']),
                get_string('elementupdated', 'tiny_styles'), 2);
        }
        print_error('Invalidelementid', 'tiny_styles');

    } else {
        // CREATE new element
        $record->enabled     = 1; // default
        $record->sortorder   = 0; // or some logic
        $record->timecreated = time();
        $elemid = $DB->insert_record('tiny_styles_elements', $record);

        // If bridging to category is desired:
        if (!empty($data->categoryid)) {
            // Insert into tiny_styles_cat_elements bridging table
            $link = new stdClass();
            $link->categoryid   = $data->categoryid;
            $link->elementid    = $elemid;
            $link->enabled      = 1;
            $link->sortorder    = 0;
            $link->timecreated  = time();
            $link->timemodified = time();
            $DB->insert_record('tiny_styles_cat_elements', $link);
        }

        redirect(new moodle_url('/admin/settings.php', ['section'=>'tiny_styles_admin']),
            get_string('elementcreated', 'tiny_styles'), 2);
    }
    exit;
}

// 5) If GET request => load data if "edit" + id
if ($action === 'edit' && $id > 0) {
    if ($element = $DB->get_record('tiny_styles_elements', ['id'=>$id], '*', MUST_EXIST)) {
        $formdata = new stdClass();
        $formdata->id          = $element->id;
        $formdata->action      = 'edit';
        $formdata->name        = $element->name;
        $formdata->type        = $element->type;
        $formdata->cssclasses  = $element->cssclasses;

        // If you want to auto‐detect bridging, find the first category bridging row and set categoryid
        // e.g.
        $catlink = $DB->get_record('tiny_styles_cat_elements', ['elementid' => $element->id], '*', IGNORE_MULTIPLE);
        if ($catlink) {
            $formdata->categoryid = $catlink->categoryid;
        } else {
            $formdata->categoryid = 0;
        }

        $mform->set_data($formdata);
    } else {
        print_error('Invalidelementid', 'tiny_styles');
    }
} else {
    // "create" scenario
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->action = 'create';
    $formdata->categoryid = 0;
    $mform->set_data($formdata);
}

// 6) Output
echo $OUTPUT->header();
echo $OUTPUT->heading($formtitle);
$mform->display();
echo $OUTPUT->footer();
