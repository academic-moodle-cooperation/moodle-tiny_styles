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
 * Plugin administration: category editing and creation
 *
 * @package     tiny_styles
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Parameters for editing/creating category entries.
$action = optional_param('action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/category.php',[
    'action' => $action,
    'id' => $id
]));

// Dynamic naming for the site.
if ($action === 'edit') {
    $formtype = get_string('editcategory', 'tiny_styles');
} else {
    $formtype = get_string('createcategory', 'tiny_styles');
}
$PAGE->set_title($formtype);
$heading = $formtype;


require_once($CFG->libdir . '/formslib.php');

/**
 * Form for creating/editing category.
 */
class category_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'generalsettings', get_string('generalsettings', 'admin'));

        $mform->addElement(
            'text',
            'name',
            get_string('name'),
            ['size' => 1, 'style' => 'width: 400px;']
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement(
            'textarea',
            'description',
            get_string('description'),
            [
                'wrap' => 'virtual',
                'rows' => 4,
                'cols' => 30,
                'style' => 'width: 400px;',
            ]
        );
        $mform->setType('description', PARAM_TEXT);

        // Description field stored in DB as "showdesc".
        $descdisplayoptions = [
            'never'    => 'Never',
            'helptext' => 'Help text',
            'tooltip'  => 'Tooltip'
        ];
        $mform->addElement(
            'select',
            'showdesc',
            'Description display',
            $descdisplayoptions,
            ['size' => 1, 'style' => 'width: 300px;']
        );

        $mform->addElement('header', 'presentationhdr', get_string('presentationhdr','tiny_styles'));

        // FA-symbol
        // todo: symbol selection dropdown
        $mform->addElement(
            'text',
            'symbol',
            'Symbol',
            ['size' => 1, 'style' => 'width: 300px;']
        );
        $mform->setType('symbol', PARAM_TEXT);

        // todo: langstrings for presentation type
        $presentationoptions = [
            'submenu' => 'Submenu',
            'inline'  => 'Inline',
            'divider' => 'Divider'
        ];
        $mform->addElement(
            'select',
            'presentation',
            'Presentation type',
            $presentationoptions,
            ['size' => 1, 'style' => 'width: 300px;']
        );

        // Hidden $id field for edit form.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Hidden $action field -> create or edit.
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * TODO: validation
     * eg. name must be at least 3 chars
     */
    public function validation($data, $files) {
        $errors = [];
        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = 'Name must be at least 3 characters.';
        }
        return $errors;
    }
}

$mform = new category_form(null, []);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']));
    exit;
}

if ($data = $mform->get_data()) {
    global $DB;

    $record = new stdClass();
    $record->name         = $data->name;
    $record->description  = $data->description;
    $record->showdesc     = $data->showdesc;
    $record->symbol       = $data->symbol;
    $record->presentation = $data->presentation;
    $record->timemodified = time();

    
    //  UPDATE of existing category 
    if ($data->action === 'edit' && !empty($data->id)) {
        if ($old = $DB->get_record('tiny_styles_categories', ['id' => $data->id], '*', MUST_EXIST)) {
            $record->id          = $old->id;
            $record->enabled     = $old->enabled;
            $record->sortorder   = $old->sortorder;
            $record->timecreated = $old->timecreated;

            $DB->update_record('tiny_styles_categories', $record);
            redirect(new moodle_url('/admin/settings.php', ['section'=>'tiny_styles_admin']), 'Category updated!', 2);
        }
        // todo: edit this
        print_error('Invalid category ID');
    } else {
        // CREATE new category
        $maxsort = $DB->get_field_sql("SELECT MAX(sortorder)
                                 FROM {tiny_styles_categories}");
        $record->enabled     = 1;
        $record->sortorder   = $maxsort+1;
        $record->timecreated = time();
        $newid = $DB->insert_record('tiny_styles_categories', $record);
        redirect(new moodle_url(
            '/admin/settings.php',
            ['section'=>'tiny_styles_admin']),
            get_string('category_saved', 'tiny_styles'), 2
        );
    }
    exit;
}

// Get the category from db and set row to form data.
if ($action === 'edit' && $id > 0) {
    global $DB;
    if ($category = $DB->get_record('tiny_styles_categories', ['id'=>$id], '*', MUST_EXIST)) {
        $formdata = new stdClass();
        $formdata->id          = $category->id;
        $formdata->action      = 'edit';
        $formdata->name        = $category->name;
        $formdata->description = $category->description;
        $formdata->showdesc    = $category->showdesc;
        $formdata->symbol      = $category->symbol;
        $formdata->presentation= $category->presentation;

        $mform->set_data($formdata);
    } else {
        // TODO: edit this
        print_error('Invalid category ID');
    }
} else {
    // ensures hidden fields are set
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->action = 'create';
    $mform->set_data($formdata);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($formtype);
$mform->display();
echo $OUTPUT->footer();
