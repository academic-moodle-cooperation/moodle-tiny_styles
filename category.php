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
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// phpcs:disable moodle.Commenting.MissingDocblock

require_once(__DIR__ . '/../../../../../config.php');
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Parameters for editing/creating category entries.
$action = optional_param('action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
    'action' => $action,
    'id' => $id,
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

    /**
     * Defines the moodle form.
     * @return void
     */
    // phpcs:disable Generic.Metrics.CyclomaticComplexity,Generic.Metrics.NestingLevel,Generic.Files.LineLength
    public function definition() {
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

        // Description field stored in DB as "showdesc".
        // Commented out for not being implemented in editor side.
        /*
        $descdisplayoptions = [
            'never'    => 'Never',
            'helptext' => 'Help text',
            'tooltip'  => 'Tooltip'
        ];
        */
        // Commented out for not being implemented in editor side.
        /*
         * $mform->addElement(
         * 'select',
         * 'showdesc',
         * get_string('descriptiondisp', 'tiny_styles'),
         * $descdisplayoptions,
         * ['size' => 1, 'style' => 'width: 300px;']
         * );
         */

        $mform->addElement('html', '<div class="form-group row">
            <div class="col-md-3 col-form-label d-flex pb-0 pe-md-0">
                <label for="icon-search-input">' . get_string('selecticon', 'tiny_styles') . '</label>
            </div>
            <div class="col-md-9" style="position: relative;" id="search-container">
                <input type="text" id="icon-search-input"
                placeholder="' . get_string('searchplaceholder', 'tiny_styles') . '"
                style="width: 300px; cursor: text;" class="form-control">
            </div>
        </div>');

        // The popup with search functionality.
        global $CFG;
        $iconpath = $CFG->dirroot . '/lib/editor/tiny/plugins/styles/pix';
        $iconurlbase = $CFG->wwwroot . '/lib/editor/tiny/plugins/styles/pix';

        $iconpopuphtml = '<div id="icon-popup" style="display:none; position:absolute; top:100%; left:0;
        width:400px; max: height 450px; background-color:#fff;
        border:1px solid #ccc; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15);
        z-index:1000; overflow:hidden; padding:0; margin-top:5px;">';

        $iconpopuphtml .= '<div style="padding:20px; border-bottom:1px solid #eee; display:flex;
            justify-content:space-between; align-items:center;">
            <h4 style="margin:0;">' . get_string('selectanicon', 'tiny_styles') . '</h4>
            <button type="button" id="clear-search-btn" class="btn btn-secondary btn-sm">'
            . get_string('clearsearch', 'tiny_styles') . '</button>
            </div>';

        $iconpopuphtml .= '<div id="icon-grid-container" style="max-height:300px; overflow-y:auto; padding:20px;">
            <div id="icon-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap:10px;">';

        // Icons and names for the popup.
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
                    $iconurl = $iconurlbase . '/' . $file;
                    $iconname = pathinfo($file, PATHINFO_FILENAME);

                    $iconpopuphtml .= '<div class="icon-grid-item" data-icon="' . s($file) . '"
                        data-icon-name="' . s($iconname) . '"
                        style="cursor:pointer; display:flex; flex-direction:column; align-items:center;
                        justify-content:center; padding:12px; border-radius:12px;"
                        title="' . s($iconname) . '">';

                    $iconpopuphtml .= '<img src="' . $iconurl . '" alt="' . s($file) . '"
                        style="width:24px; height:24px; display:block; margin: 0 auto;" />';
                    $iconpopuphtml .= '<div style="font-size:0.75em; margin-top:4px;
                        color:#666; text-align:center;">' . s($iconname) . '</div>';
                    $iconpopuphtml .= '<style>
                        #icon-grid .icon-grid-item:hover {
                            background-color: #e7f1ff;
                            border-color: #007bff;
                        }
                    </style>';
                    $iconpopuphtml .= '</div>';
                }
            }
        }

        $iconpopuphtml .= '</div>
            <div id="no-icons-found" style="display:none; text-align:center; padding:20px; color:#666;">
            ' . get_string('noiconsfound', 'tiny_styles') . '
            </div>
            </div>';

        // Close button for popup.
        $iconpopuphtml .= '<div style="padding:15px 20px; border-top:1px solid #eee;
             display:flex; justify-content:center; align-items:center;">
            <button type="button" id="close-icon-popup" class="btn btn-primary">'
            . get_string('close', 'tiny_styles') . '</button>
            </div>';
        $iconpopuphtml .= '</div>';

        // Add popup HTML to form.
        $mform->addElement('html', $iconpopuphtml);

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

        // Hidden $action field -> create or edit.
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        // Hidden selected icon field for storing to db.
        $mform->addElement('hidden', 'selectedicon', '');
        $mform->setType('selectedicon', PARAM_TEXT);

        // Add available icon names as a data attribute for JavaScript validation.
        $mform->addElement('html', '<script>window.availableIcons = ' . json_encode($iconnames) . ';</script>');

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * TODO: validation.
     * Eg. name must be at least 3 chars.
     * @param mixed $data User input for name.
     * @param mixed $files Required by moodle.
     */
    public function validation($data, $files) {
        $errors = [];
        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = get_string('errorname', 'tiny_styles');
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
    $record->showdesc     = 'null';
    $record->symbol       = $data->selectedicon;
    $record->menumode = $data->menumode;
    $record->timemodified = time();

    // UPDATE of existing category.
    if ($data->action === 'edit' && !empty($data->id)) {
        if ($old = $DB->get_record('tiny_styles_categories', ['id' => $data->id], '*', MUST_EXIST)) {
            $record->id          = $old->id;
            $record->enabled     = $old->enabled;
            $record->sortorder   = $old->sortorder;
            $record->timecreated = $old->timecreated;

            $DB->update_record('tiny_styles_categories', $record);
            redirect(new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']), 'Category updated!', 2);
        }
        // TODO: edit this.
        print_error('Invalid category ID');
    } else {
        // CREATE new category.
        $maxsort = $DB->get_field_sql("SELECT MAX(sortorder)
                                 FROM {tiny_styles_categories}");
        $record->enabled     = 0;
        $record->sortorder   = $maxsort + 1;
        $record->timecreated = time();
        $newid = $DB->insert_record('tiny_styles_categories', $record);
        redirect(new moodle_url(
            '/admin/settings.php',
            ['section' => 'tiny_styles_admin']),
            get_string('category_saved', 'tiny_styles'), 2
        );
    }
    exit;
}

// Get the category from db and set row to form data.
if ($action === 'edit' && $id > 0) {
    global $DB;
    if ($category = $DB->get_record('tiny_styles_categories', ['id' => $id], '*', MUST_EXIST)) {
        $formdata = new stdClass();
        $formdata->id           = $category->id;
        $formdata->action       = 'edit';
        $formdata->name         = $category->name;
        $formdata->description  = $category->description;
        // To uncommment: $formdata->showdesc     = $category->showdesc;.
        // Removed for not being implemented in editor.
        $formdata->selectedicon = $category->symbol;
        $formdata->menumode = $category->menumode;

        $mform->set_data($formdata);
    } else {
        // TODO: edit this.
        print_error('Invalid category ID');
    }
} else {
    // Ensures hidden fields are set.
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->action = 'create';
    $mform->set_data($formdata);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($formtype);
$mform->display();
$PAGE->requires->js_call_amd('tiny_styles/iconselector', 'init');
echo $OUTPUT->footer();
