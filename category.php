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
 * Plugin administration: category editing and creation (REFACTORED VERSION)
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Parameters for editing/creating category entries.
$tinystylesaction = optional_param('tiny_styles_action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/category2.php', [
    'tiny_styles_action' => $tinystylesaction,
    'id' => $id,
]));

// Dynamic naming for the site.
if ($tinystylesaction === 'edit') {
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
    /**
     * Defines the moodle form.
     * @return void
     */
    public function definition() {
        global $OUTPUT, $CFG;
        $mform = $this->_form;

        $mform->addElement('header', 'generalsettings', get_string('generalsettings', 'admin'));

        $mform->addElement(
            'text',
            'name',
            get_string('name'),
            ['size' => 1, 'style' => 'width: 400px;', 'maxlength' => 100],
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $mform->addElement(
            'textarea',
            'description',
            get_string('description'),
            [
                'wrap' => 'virtual',
                'rows' => 4,
                'cols' => 30,
                'style' => 'width: 400px;',
                'maxlength' => 400,
            ]
        );
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', get_string('maximumchars', '', 400), 'maxlength', 400, 'client');

        // Prepare icon selector data.
        $iconpath = $CFG->dirroot . '/lib/editor/tiny/plugins/styles/pix';
        $iconurlbase = $CFG->wwwroot . '/lib/editor/tiny/plugins/styles/pix';

        $icons = [];
        $iconnames = [];

        if (is_dir($iconpath)) {
            $files = scandir($iconpath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'icon.svg' || $file === 'remove.svg') {
                    continue;
                }
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($ext === 'svg') {
                    $iconnames[] = $file;
                    $iconname = pathinfo($file, PATHINFO_FILENAME);
                    $icons[] = [
                        'file' => $file,
                        'url' => $iconurlbase . '/' . $file,
                        'name' => $iconname,
                    ];
                }
            }
        }

        // Prepare template context for icon selector.
        $iconselectorcontext = [
            'selecticon_label' => get_string('selecticon', 'tiny_styles'),
            'searchplaceholder' => get_string('searchplaceholder', 'tiny_styles'),
            'selectanicon_label' => get_string('selectanicon', 'tiny_styles'),
            'clearsearch_label' => get_string('clearsearch', 'tiny_styles'),
            'noiconsfound_label' => get_string('noiconsfound', 'tiny_styles'),
            'close_label' => get_string('close', 'tiny_styles'),
            'icons' => $icons,
            'iconnames_json' => json_encode($iconnames),
        ];

        // Render icon selector using template.
        $iconselectorhtml = $OUTPUT->render_from_template('tiny_styles/icon_selector', $iconselectorcontext);
        $mform->addElement('html', $iconselectorhtml);

        $menumodeoptions = [
            'submenu' => get_string('submenu', 'tiny_styles'),
            'inline'  => get_string('inline', 'tiny_styles'),
            'divider' => get_string('divider', 'tiny_styles'),
        ];
        $mform->addElement(
            'select',
            'menumode',
            get_string('menumodetype', 'tiny_styles'),
            $menumodeoptions,
            ['size' => 1, 'style' => 'width: 300px;']
        );
        $mform->addHelpButton('menumode', 'menumodetype', 'tiny_styles');

        // Hidden $id field for edit form.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Hidden $tiny_styles_action field -> create or edit.
        $mform->addElement('hidden', 'tiny_styles_action');
        $mform->setType('tiny_styles_action', PARAM_ALPHA);

        // Hidden selected icon field for storing to db.
        $mform->addElement('hidden', 'selectedicon', '');
        $mform->setType('selectedicon', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * TODO: extend validation.
     * Eg. name must be at least 3 chars.
     * @param mixed $data User input for name.
     * @param mixed $files Required by moodle.
     */
    public function validation($data, $files) {
        $errors = [];
        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = get_string('error:name', 'tiny_styles');
        }
        return $errors;
    }
}

$mform = new category_form(null, []);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php'));
    exit;
}

if ($data = $mform->get_data()) {
    global $DB;

    $record = new stdClass();
    $record->name         = $data->name;
    $record->description  = $data->description;
    $record->showdesc     = 'null';
    $record->symbol       = $data->selectedicon;
    $record->menumode = $data->menumode;
    $record->timemodified = time();

    // UPDATE of existing category.
    if ($data->tiny_styles_action === 'edit' && !empty($data->id)) {
        if ($old = $DB->get_record('tiny_styles_categories', ['id' => $data->id], '*', MUST_EXIST)) {
            $record->id          = $old->id;
            $record->enabled     = $old->enabled;
            $record->sortorder   = $old->sortorder;
            $record->timecreated = $old->timecreated;

            $DB->update_record('tiny_styles_categories', $record);
            redirect(
                new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php'),
                get_string('category_saved', 'tiny_styles'),
                2
            );
        }
        // TODO: edit this.
        throw new moodle_exception(get_string('invalidid', 'tiny_styles'));
    } else {
        // CREATE new category.
        $maxsort = $DB->get_field_sql("SELECT MAX(sortorder)
                                 FROM {tiny_styles_categories}");
        $record->enabled     = 0;
        $record->sortorder   = $maxsort + 1;
        $record->timecreated = time();
        $newid = $DB->insert_record('tiny_styles_categories', $record);
        redirect(
            new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php'),
            get_string('category_saved', 'tiny_styles'),
            2
        );
    }
    exit;
}

// Get the category from db and set row to form data.
if ($tinystylesaction === 'edit' && $id > 0) {
    global $DB;
    if ($category = $DB->get_record('tiny_styles_categories', ['id' => $id], '*', MUST_EXIST)) {
        $formdata = new stdClass();
        $formdata->id           = $category->id;
        $formdata->tiny_styles_action       = 'edit';
        $formdata->name         = $category->name;
        $formdata->description  = $category->description;
        // To uncommment: $formdata->showdesc     = $category->showdesc;.
        // Removed for not being implemented in editor.
        $formdata->selectedicon = $category->symbol;
        $formdata->menumode = $category->menumode;

        $mform->set_data($formdata);
    } else {
        // TODO: edit this.
        throw new moodle_exception(get_string('invalidid', 'tiny_styles'));
    }
} else {
    // Ensures hidden fields are set.
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->tiny_styles_action = 'create';
    $mform->set_data($formdata);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($formtype);
$mform->display();
$PAGE->requires->js_call_amd('tiny_styles/iconselector', 'init');
echo $OUTPUT->footer();
