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
require_once('locallib.php');
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Parameters for editing/creating category entries.
$tinystylesaction = optional_param('tiny_styles_action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
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
            ['size' => 1, 'style' => 'width: 400px;', 'maxlength' => 255],
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement(
            'textarea',
            'description',
            get_string('description'),
            [
                'wrap' => 'virtual',
                'rows' => 4,
                'cols' => 30,
                'style' => 'width: 400px;',
                'maxlength' => 1000,
            ]
        );
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', get_string('maximumchars', '', 1000), 'maxlength', 1000, 'client');

        // Prepare template context for icon selector (icon list comes from the AMD module).
        $iconselectorcontext = [
            'selecticon_label' => get_string('selecticon', 'tiny_styles'),
            'searchplaceholder' => get_string('searchplaceholder', 'tiny_styles'),
            'selectanicon_label' => get_string('selectanicon', 'tiny_styles'),
            'clearsearch_label' => get_string('clearsearch', 'tiny_styles'),
            'noiconsfound_label' => get_string('noiconsfound', 'tiny_styles'),
            'close_label' => get_string('close', 'tiny_styles'),
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

        $showdescoptions = [
            'never'    => get_string('showdesc_never', 'tiny_styles'),
            'helptext' => get_string('showdesc_helptext', 'tiny_styles'),
            'tooltip'  => get_string('showdesc_tooltip', 'tiny_styles'),
        ];
        $mform->addElement(
            'select',
            'showdesc',
            get_string('showdesc', 'tiny_styles'),
            $showdescoptions,
            ['size' => 1, 'style' => 'width: 300px;']
        );
        $mform->addHelpButton('showdesc', 'showdesc', 'tiny_styles');

        // Restrict visibility section.
        $mform->addElement(
            'header',
            'restrictvisibilitysection',
            get_string('restrict_visibility_section', 'tiny_styles')
        );
        $mform->setExpanded('restrictvisibilitysection', false);

        $visibilityoptions = [
            '1' => get_string('visibility_show', 'tiny_styles'),
            '0' => get_string('visibility_hide', 'tiny_styles'),
        ];
        $mform->addElement(
            'select',
            'enabled',
            get_string('visibility', 'tiny_styles'),
            $visibilityoptions
        );
        $mform->setType('enabled', PARAM_INT);
        $mform->setDefault('enabled', '1');
        $mform->addHelpButton('enabled', 'visibility', 'tiny_styles');

        // Visibility by site admin status.
        $mform->addElement(
            'header',
            'visibilityadminsection',
            get_string('visibility_admin_section', 'tiny_styles')
        );
        $mform->setExpanded('visibilityadminsection', false);

        $adminvisibilityoptions = [
            'all'         => get_string('visibility_admin_all', 'tiny_styles'),
            'admins_only' => get_string('visibility_admin_admins_only', 'tiny_styles'),
            'non_admins'  => get_string('visibility_admin_non_admins', 'tiny_styles'),
        ];
        $mform->addElement(
            'select',
            'visibility_admin',
            get_string('visibility_admin', 'tiny_styles'),
            $adminvisibilityoptions
        );
        $mform->setType('visibility_admin', PARAM_ALPHANUMEXT);
        $mform->setDefault('visibility_admin', 'all');
        $mform->addHelpButton('visibility_admin', 'visibility_admin', 'tiny_styles');

        // Visibility by role.
        $mform->addElement(
            'header',
            'visibilityrolessection',
            get_string('visibility_roles_section', 'tiny_styles')
        );
        $mform->setExpanded('visibilityrolessection', false);

        $mform->addElement(
            'autocomplete',
            'visibility_roles',
            get_string('visibility_roles', 'tiny_styles'),
            $this->_customdata['roleoptions'],
            ['multiple' => true]
        );
        $mform->setType('visibility_roles', PARAM_INT);
        $mform->addHelpButton('visibility_roles', 'visibility_roles', 'tiny_styles');

        // Hide the role section entirely when admins_only.
        $mform->hideIf('visibilityrolessection', 'visibility_admin', 'eq', 'admins_only');
        $mform->hideIf('visibility_roles', 'visibility_admin', 'eq', 'admins_only');

        // Hide admin and role sections when category is set to hide in editor.
        $mform->hideIf('visibilityadminsection', 'enabled', 'eq', '0');
        $mform->hideIf('visibility_admin', 'enabled', 'eq', '0');
        $mform->hideIf('visibilityrolessection', 'enabled', 'eq', '0');
        $mform->hideIf('visibility_roles', 'enabled', 'eq', '0');

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

$allroles = get_all_roles();
$roleoptions = [];
foreach ($allroles as $role) {
    $roleoptions[$role->id] = role_get_name($role, $context);
}

$mform = new category_form(null, ['roleoptions' => $roleoptions]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php'));
    exit;
}

if ($data = $mform->get_data()) {
    try {
        // Prepare category record from form data.
        $record = tiny_styles_prepare_category_for_save($data, $data->tiny_styles_action);

        if ($data->tiny_styles_action === 'edit' && !empty($data->id)) {
            // Update existing category.
            tiny_styles_update_category($record);
        } else {
            // Create new category.
            tiny_styles_insert_category($record);
        }

        redirect(
            new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php'),
            get_string('category_saved', 'tiny_styles'),
            2
        );
    } catch (Exception $e) {
        redirect(
            new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php'),
            get_string('error') . ': ' . $e->getMessage(),
            2,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    exit;
}

// Load category data for editing.
if ($tinystylesaction === 'edit' && $id > 0) {
    try {
        $formdata = tiny_styles_load_category_for_form($id);
        $mform->set_data($formdata);
    } catch (Exception $e) {
        throw new moodle_exception('invalidid', 'tiny_styles');
    }
} else {
    // Set default values for create form.
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
