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

        // CSS classes.
        $elements = tiny_styles_get_css_classes();

        $cssoptions = [
            '_manual' => get_string('manualstyle', 'tiny_styles'),
            '' => '',
        ];

        foreach ($elements as $element) {
            $cssoptions[$element] = $element;
        }

        $mform->addElement(
            'select',
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
$mform = new element_form($formurl, ['catid' => $catid]);

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

echo $OUTPUT->footer();
