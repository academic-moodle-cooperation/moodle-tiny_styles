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
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// phpcs:disable moodle.Commenting.MissingDocblock

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$tinystylesaction = optional_param('tiny_styles_action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$catid = required_param('catid', PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
    'tiny_styles_action' => $tinystylesaction,
    'id'     => $id,
    'catid'  => $catid,
]));

if ($tinystylesaction === 'edit') {
    $formtitle = get_string('editelement', 'tiny_styles');
} else {
    $formtitle = get_string('create_element', 'tiny_styles');
}
$PAGE->set_title($formtitle);

require_once($CFG->libdir . '/formslib.php');

/**
 * Form class for creating or editing an Element.
 */
class element_form extends moodleform {
    /**
     * Defines the moodle form.
     * @return void
     */
    // PHPMD:suppress ExcessiveMethodLength.
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'generalsettings', get_string('generalsettings', 'admin'));

        // Name.
        $mform->addElement(
            'text',
            'name',
            get_string('name'),
            ['size' => 50, 'style' => 'width: 400px;', 'maxlength' => 255]
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $categories = tiny_styles_get_categories_for_dropdown(true);

        $mform->addElement(
            'select',
            'categoryid',
            get_string('category', 'tiny_styles'),
            $categories,
            ['size' => 1, 'style' => 'width: 400px;'],
        );
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', 'catid');
        $mform->addHelpButton('categoryid', 'categoryhelp', 'tiny_styles');

        // CSS classes - using hierarchical array for optgroups.
        $elements = tiny_styles_get_css_classes();

        // Build options array with optgroup structure.
        $cssoptions = [
            get_string('manualstyle', 'tiny_styles') => [
                '_manual' => get_string('inlinecss', 'tiny_styles'),
            ],
            get_string('bootstrapclasses', 'tiny_styles') => [],
        ];

        foreach ($elements as $element) {
            $cssoptions[get_string('bootstrapclasses', 'tiny_styles')][$element] = $element;
        }

        // Add empty placeholder option at the bottom.
        $cssoptions[] = ['' => ''];

        $mform->addElement(
            'selectgroups',
            'cssclasses',
            get_string('bootstrapclass', 'tiny_styles'),
            $cssoptions,
            ['size' => 1, 'style' => 'width: 400px;']
        );
        $mform->setType('cssclasses', PARAM_TEXT);
        $mform->addRule('cssclasses', null, 'required', null, 'client');
        $mform->addHelpButton('cssclasses', 'bootstrapclass', 'tiny_styles');

        $typeoptions = [
            'inline' => get_string('type:inline', 'tiny_styles'),
            'block'  => get_string('type:paragraph', 'tiny_styles'),
        ];
        $mform->addElement(
            'select',
            'type',
            get_string('type', 'tiny_styles'),
            $typeoptions,
            ['size' => 1, 'style' => 'width: 400px;']
        );
        $mform->setType('type', PARAM_ALPHA);
        $mform->addHelpButton('type', 'typehelp', 'tiny_styles');

        $mform->addElement(
            'textarea',
            'manualconfig',
            get_string('manualconfig', 'tiny_styles'),
            [
                'wrap' => 'virtual',
                'rows' => 7,
                'cols' => 30,
                'style' => 'width: 400px;',
                'maxlength' => 2048,
            ]
        );
        $mform->setType('manualconfig', PARAM_RAW);
        $mform->setDefault('manualconfig', '');
        $mform->addRule('manualconfig', get_string('maximumchars', '', 2048), 'maxlength', 2048, 'client');

        // Manual configuration hidden unless a manual style selected.
        $mform->hideIf('manualconfig', 'cssclasses', 'neq', '_manual');
        $mform->hideIf('type', 'cssclasses', 'neq', '_manual');

        $mform->addElement(
            'static',
            'manualconfig_help',
            '',
            get_string('manualdefault', 'tiny_styles')
        );
        $mform->hideIf('manualconfig_help', 'cssclasses', 'neq', '_manual');

        // Read parent category restrictions from custom data.
        $catvisiblityadmin = $this->_customdata['cat_visibility_admin'] ?? 'all';
        $catvisiblityroles = $this->_customdata['cat_visibility_roles'] ?? [];
        $catenabledstatus = $this->_customdata['cat_enabled'] ?? 1;
        $caturl = (new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
            'tiny_styles_action' => 'edit',
            'id'                 => $this->_customdata['catid'],
        ]))->out(false);

        $mform->addElement(
            'header',
            'restrict_availability_section',
            get_string('restrict_availability_section', 'tiny_styles')
        );
        $mform->setExpanded('restrict_availability_section', false);

        // Info alert when parent category is hidden.
        if (!$catenabledstatus) {
            $dismissbutton = html_writer::tag('button', '', [
                'type'            => 'button',
                'class'           => 'btn-close',
                'data-bs-dismiss' => 'alert',
                'aria-label'      => get_string('close', 'tiny_styles'),
            ]);
            $mform->addElement('html', html_writer::div(
                get_string('visibility_hidden_locked', 'tiny_styles', $caturl) . $dismissbutton,
                'alert alert-info alert-dismissible fade show',
                ['role' => 'alert']
            ));
        }

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
        $mform->addHelpButton('enabled', 'visibility_element', 'tiny_styles');

        // Lock to hidden when parent category is set to hide in editor.
        if (!$catenabledstatus) {
            $mform->setConstant('enabled', '0');
            $mform->freeze('enabled');
        }

        // Visibility by site admin status.
        $mform->addElement(
            'header',
            'visibilityadminsection',
            get_string('visibility_admin_section', 'tiny_styles')
        );
        $mform->setExpanded('visibilityadminsection', true);

        // Info alert when category locks this setting.
        if ($catvisiblityadmin !== 'all') {
            $dismissbutton = html_writer::tag('button', '', [
                'type'            => 'button',
                'class'           => 'btn-close',
                'data-bs-dismiss' => 'alert',
                'aria-label'      => get_string('close', 'tiny_styles'),
            ]);
            $mform->addElement('html', html_writer::div(
                get_string('visibility_admin_locked', 'tiny_styles', $caturl) . $dismissbutton,
                'alert alert-info alert-dismissible fade show',
                ['role' => 'alert']
            ));
        }

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
        $mform->addHelpButton('visibility_admin', 'visibility_admin_element', 'tiny_styles');

        // Lock to parent category value when restricted.
        if ($catvisiblityadmin !== 'all') {
            $mform->setConstant('visibility_admin', $catvisiblityadmin);
            $mform->freeze('visibility_admin');
        }

        // Hide admin section when element is set to hide in editor.
        $mform->hideIf('visibilityadminsection', 'enabled', 'eq', '0');
        $mform->hideIf('visibility_admin', 'enabled', 'eq', '0');

        // Visibility by role.
        if ($catvisiblityadmin !== 'admins_only') {
            $mform->addElement(
                'header',
                'visibilityrolessection',
                get_string('visibility_roles_section', 'tiny_styles')
            );
            $mform->setExpanded('visibilityrolessection', true);

            // Info alert when category restricts the role pool.
            if (!empty($catvisiblityroles)) {
                $dismissbutton = html_writer::tag('button', '', [
                    'type'            => 'button',
                    'class'           => 'btn-close',
                    'data-bs-dismiss' => 'alert',
                    'aria-label'      => get_string('close', 'tiny_styles'),
                ]);
                $mform->addElement('html', html_writer::div(
                    get_string('visibility_roles_restricted', 'tiny_styles', $caturl) . $dismissbutton,
                    'alert alert-info alert-dismissible fade show',
                    ['role' => 'alert']
                ));
            }

            $mform->addElement(
                'autocomplete',
                'visibility_roles',
                get_string('visibility_roles', 'tiny_styles'),
                $this->_customdata['roleoptions'],
                ['multiple' => true]
            );
            $mform->setType('visibility_roles', PARAM_INT);
            $mform->addHelpButton('visibility_roles', 'visibility_roles_element', 'tiny_styles');

            // Hide role section when user sets visibility_admin to admins_only.
            $mform->hideIf('visibilityrolessection', 'visibility_admin', 'eq', 'admins_only');
            $mform->hideIf('visibility_roles', 'visibility_admin', 'eq', 'admins_only');

            // Hide role section when element is set to Hide in editor.
            $mform->hideIf('visibilityrolessection', 'enabled', 'eq', '0');
            $mform->hideIf('visibility_roles', 'enabled', 'eq', '0');
        }

        // Hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'tiny_styles_action');
        $mform->setType('tiny_styles_action', PARAM_ALPHA);

        $mform->addElement('hidden', 'catid', $this->_customdata['catid']);
        $mform->setType('catid', PARAM_INT);

        $mform->registerNoSubmitButton('previewstyle');

        $buttons = [];

        $buttons[] = $mform->createElement(
            'submit',
            'submitbutton',
            get_string('savechanges'),
        );

        $buttons[] = $mform->createElement(
            'button',
            'previewstyle',
            get_string('preview', 'tiny_styles'),
            ['id' => 'btn-preview-element']
        );

        $buttons[] = $mform->createElement(
            'cancel',
            'cancel',
            get_string('cancel'),
        );

        $mform->addGroup($buttons, 'actionar', '', [' '], false);
        $mform->closeHeaderBefore('actionar');
    }

    /**
     * Crude validation for name length.
     * @param mixed $data User input for name.
     * @param mixed $files Required by moodle.
     * @return array
     */
    // PHPMD:suppress UnusedLocalVariable.
    public function validation($data, $files) {
        $errors = [];

        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = get_string('error:name', 'tiny_styles');
        }
        return $errors;
    }
}
$formurl = new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
    'tiny_styles_action' => $tinystylesaction,
    'id'     => $id,
    'catid'  => $catid,
]);
$allroles = get_all_roles();
$roleoptions = [];
foreach ($allroles as $role) {
    $roleoptions[$role->id] = role_get_name($role, $context);
}

// Load parent category visibility to restrict element form fields accordingly.
$parentcategory = ($catid > 0) ? tiny_styles_get_category($catid) : null;
$catvisiblityadmin = $parentcategory ? ($parentcategory->visibility_admin ?? 'all') : 'all';
$catvisiblityroles = $parentcategory ? (json_decode($parentcategory->visibility_roles ?? '', true) ?? []) : [];
$catenabledstatus = $parentcategory ? (int)($parentcategory->enabled ?? 1) : 1;

// Narrow the role picker to only the roles allowed by the parent category.
if (!empty($catvisiblityroles)) {
    $roleoptions = array_intersect_key($roleoptions, array_flip($catvisiblityroles));
}

$mform = new element_form($formurl, [
    'catid'                => $catid,
    'roleoptions'          => $roleoptions,
    'cat_visibility_admin' => $catvisiblityadmin,
    'cat_visibility_roles' => $catvisiblityroles,
    'cat_enabled'          => $catenabledstatus,
]);

if ($mform->is_cancelled()) {
    $returnurl = new moodle_url(
        '/lib/editor/tiny/plugins/styles/elements.php',
        ['catid' => $catid]
    );
    redirect($returnurl, get_string('elementcancel', 'tiny_styles'), 2);
    exit;
}

if ($data = $mform->get_data()) {
    try {
        if ($data->tiny_styles_action === 'edit' && !empty($data->id)) {
            tiny_styles_update_element_full($data);
            redirect(
                new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                    'catid' => $data->categoryid,
                ]),
                get_string('elementupdated', 'tiny_styles'),
                2
            );
        } else {
            tiny_styles_create_element_with_bridge($data, $data->categoryid);
            redirect(
                new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                    'catid' => $data->categoryid,
                ]),
                get_string('elementcreated', 'tiny_styles'),
                2
            );
        }
    } catch (Exception $e) {
        redirect(
            new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', ['catid' => $catid]),
            get_string('error') . ': ' . $e->getMessage(),
            1,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    exit;
}

// Loading data for editing an existing element.
if ($tinystylesaction === 'edit' && $id > 0) {
    $formdata = tiny_styles_load_element_for_form($id);
    $mform->set_data($formdata);
} else {
    // Create new style element.
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->tiny_styles_action = 'create';
    $formdata->categoryid = $catid;
    $formdata->cssclasses = '';
    $mform->set_data($formdata);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($formtitle);
$mform->display();

// Require form preview js for the preview dialog.
$PAGE->requires->js_call_amd(
    'tiny_styles/form_preview',
    'init',
    ['#btn-preview-element'],
);

// Automatically update the cssclass type inline/block for predefined elements.
$PAGE->requires->js_call_amd('tiny_styles/element_type', 'init');

// Disable the empty placeholder option in the dropdown.
$PAGE->requires->js_call_amd('tiny_styles/disable_placeholder', 'init');

echo $OUTPUT->footer();
