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
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
            ['size' => 1, 'style' => 'width: 400px;', 'maxlength' => 100,]
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
        $descdisplayoptions = [
            'never'    => 'Never',
            'helptext' => 'Help text',
            'tooltip'  => 'Tooltip'
        ];
        
        /**
         * Removed for not being implemented in editor side.
         *
         * $mform->addElement(
         * 'select',
         * 'showdesc',
         * get_string('descriptiondisp', 'tiny_styles'),
         * $descdisplayoptions,
         * ['size' => 1, 'style' => 'width: 300px;']
         * );
         */

        $mform->addElement('header', 'presentationhdr', get_string('presentationhdr','tiny_styles'));

        // Button to open an icon selection popup.
        $mform->addElement('html', '
        <div class="form-group row">
            <div class="col-md-3 col-form-label d-flex pb-0 pe-md-0">
                <label for="open-icon-popup">' . get_string('selecticon', 'tiny_styles') . '</label>
            </div>
            <div class="col-md-9">
                <button type="button" id="open-icon-popup" class="btn btn-outline-primary"
        style="padding: 7px 14px; font-size: 15px;">
                    <span id="button-icon-name">' . get_string('selecticon', 'tiny_styles') . '</span>
                </button>
            </div>
        </div>
        ');

        // The popup, scanning the plugin’s pix folder for .svg icons.
        global $CFG;
        $iconpath = $CFG->dirroot . '/lib/editor/tiny/plugins/styles/pix';
        $iconurlbase = $CFG->wwwroot . '/lib/editor/tiny/plugins/styles/pix';
        $iconpopuphtml = '<div id="icon-popup" style="display:none; position:fixed;top:35%; left:28%; width:14%; height:25%;
                      background-color:#fff; border:1px solid #ccc; z-index:1000; overflow:auto; padding:20px;">';
        $iconpopuphtml .= '<h4>' . get_string('selectanicon', 'tiny_styles') . '</h4>';
        $iconpopuphtml .= '<div style="display:flex; flex-wrap:wrap; gap:15px;">';

        // icons for the popup
        if (is_dir($iconpath)) {
            $files = scandir($iconpath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'icon.svg') {
                    continue;
                }
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($ext === 'svg') {
                    $iconurl = $iconurlbase . '/' . $file;
                    $iconpopuphtml .= '<div class="icon-grid-item" data-icon="' . s($file) . '"
                                     style="cursor:pointer; text-align:center;">';
                    $iconpopuphtml .= '<img src="' . $iconurl . '" alt="' . s($file) . '"
                                      style="width:23px; height:23px; display:block; margin:auto;" />';
                    //$iconpopuphtml .= '<div style="font-size:0.9em; margin-top:5px;">' . s($file) . '</div>';
                    $iconpopuphtml .= '</div>';
                }
            }
        }
        $iconpopuphtml .= '</div>';

        // Close button for popup
        $iconpopuphtml .= '<div style="margin-top: 20px; display: flex; justify-content: center;">
        <button type="button" id="close-icon-popup" class="btn btn-primary">'
            . get_string('close', 'tiny_styles') . '</button>
        </div>';
        $iconpopuphtml .= '</div>';

        // Popup HTML into the form.
        $mform->addElement('html', $iconpopuphtml);

        $presentationoptions = [
            'submenu' => get_string('submenu', 'tiny_styles'),
            'inline'  => get_string('inline', 'tiny_styles'),
            'divider' => get_string('divider', 'tiny_styles'),
        ];
        $mform->addElement(
            'select',
            'presentation',
            get_string('presentationtype', 'tiny_styles'),
            $presentationoptions,
            ['size' => 1, 'style' => 'width: 300px;']
        );
        $mform->addHelpButton('presentation', 'presentationhelp', 'tiny_styles');

        // Hidden $id field for edit form.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Hidden $action field -> create or edit.
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        // Hidden selected icon field for storing to db.
        $mform->addElement('hidden', 'selectedicon', '');
        $mform->setType('selectedicon', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * TODO: validation
     * eg. name must be at least 3 chars
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
    $record->showdesc     = 'null';//$data->showdesc;
    $record->symbol       = $data->selectedicon;
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
        $record->enabled     = 0;
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
        $formdata->id           = $category->id;
        $formdata->action       = 'edit';
        $formdata->name         = $category->name;
        //$formdata->description  = $category->description; // removed for not being implemented in editor.
        $formdata->showdesc     = $category->showdesc;
        $formdata->selectedicon = $category->symbol;
        $formdata->presentation = $category->presentation;

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
$PAGE->requires->js_call_amd('tiny_styles/iconselector', 'init');
echo $OUTPUT->footer();
