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
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$action = optional_param('action', 'create', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$catid = required_param('catid', PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
    'action' => $action,
    'id'     => $id,
    'catid'  => $catid,
]));

if ($action === 'edit') {
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
    public function definition() {
        global $DB;
        $mform = $this->_form;


        // Header
        //$mform->addElement('header', 'elementsettings', get_string('elementsettings', 'tiny_styles'));

        // Name
        $mform->addElement(
            'text',
            'name',
            get_string('name'),
            ['size' => 50, 'style' => 'width: 400px;']
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $categories = $DB->get_records_menu('tiny_styles_categories', null, 'sortorder ASC', 'id,name');

        // todo: remove the divider more efficiently by a conditional query
        foreach ($categories as $id => $name) {
            $presentation = $DB->get_field('tiny_styles_categories', 'presentation', ['id' => $id]);
            if ($presentation === 'divider') {
                unset($categories[$id]);
            }
        }

        $mform->addElement(
            'select',
            'categoryid',
            get_string('category', 'tiny_styles'),
            $categories,
            ['size' => 1, 'style' => 'width: 400px;']
        );
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', 'catid');
        $mform->addRule('categoryid', null, 'required', null, 'client');

        // todo: dynamically set based on style selected
        // ->to avoid accidental styling errors (inline styling for a box vice versa)
        $typeoptions = [
            'inline' => 'Inline',
            'block'  => 'Block',
        ];
        $mform->addElement(
            'select', 'type',
            get_string('type', 'tiny_styles'),
            $typeoptions,
            ['size' => 1, 'style' => 'width: 400px;']
        );
        $mform->setType('type', PARAM_ALPHA);

        // CSS classes
        $elements = $DB->get_fieldset_sql("
            SELECT cssclasses
            FROM {tiny_styles_elements} 
            GROUP BY cssclasses
            ORDER BY cssclasses ASC
        ");

        $elements[] = 'Manual style sheet';
        $cssoptions = array_combine($elements, $elements);

        $mform->addElement(
            'select', 'cssclasses',
            get_string('bootstrapclass', 'tiny_styles'),
            $cssoptions,
            ['size' => 1, 'style' => 'width: 400px;']
        );
        $mform->setType('cssclasses', PARAM_TEXT);
        $mform->addRule('cssclasses', null, 'required', null, 'client');

        $mform->addElement(
            'textarea',
            'manualconfig',
            get_string('manualconfig', 'tiny_styles'),
            [
                'wrap' => 'virtual',
                'rows' => 7,
                'cols' => 30,
                'style' => 'width: 400px;',
            ]
        );
        $mform->setType('manualconfig', PARAM_RAW);
        $mform->setDefault('manualconfig', '');

        // Manual configuration hidden unless "Manual style sheet" selected.
        $mform->hideIf('manualconfig', 'cssclasses', 'neq', 'Manual style sheet');

        $mform->setDefault('manualconfig', get_string('manualdefault', 'tiny_styles'));
        // $mform->addRule('manualconfig', null, 'required', null, 'client');

        // Hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'catid');
        $mform->setType('catid', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges'));

        $mform->registerNoSubmitButton('previewstyle');

        $mform->addElement(
            'button',
            'previewstyle',
            get_string('preview', 'tiny_styles'),
            ['id' => 'btn-preview-element']
        );
    }

    // todo: expand validation
    public function validation($data, $files) {
        $errors = array();

        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = get_string('error_nametooshort', 'tiny_styles');
        }
        return $errors;
    }
}

$mform = new element_form(null);

// todo: redirect back to category and not the main page
if ($mform->is_cancelled()) {
    redirect(new moodle_url(
        '/admin/settings.php',
        ['section'=>'tiny_styles_admin']),
        get_string('elementcancel', 'tiny_styles'), 2
    );
    exit;
}

if ($data = $mform->get_data()) {
    global $DB;
    $record = new stdClass();

    if (!isset($data->manualconfig)) {
        $data->manualconfig = '';
    }

    if($data->cssclasses == 'Manual style sheet') {
        $record->cssclasses = $data->manualconfig;
        $record->custom = 1;
    } else {
        $record->cssclasses = $data->cssclasses;
        $record->custom = 0;
    }

    $record->name       = $data->name;
    $record->type       = $data->type;
    $record->timemodified = time();

    if ($data->action === 'edit' && !empty($data->id)) {
        if ($old = $DB->get_record('tiny_styles_elements', ['id' => $data->id], '*', MUST_EXIST)) {
            $record->id          = $old->id;
            $record->enabled     = $old->enabled;
            $record->sortorder   = $old->sortorder;
            $record->timecreated = $old->timecreated;

            $DB->update_record('tiny_styles_elements', $record);

            if (!empty($data->categoryid)) {
                // todo: validate
            }

            redirect((new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                'catid' => $data->categoryid,
                'sesskey' => sesskey()
            ])
            )->out(false), get_string('elementupdated', 'tiny_styles'), 2);
        }
        print_error('invalidelementid', 'tiny_styles');

    } else {
        // New element addition.
        $catid = $data->categoryid;
        $exists = $DB->record_exists('tiny_styles_cat_elements', ['categoryid' => $catid]);

        if ($exists) {
            $maxsort = $DB->get_field_sql("
                SELECT MAX(sortorder)
                FROM {tiny_styles_cat_elements}
                WHERE categoryid = ?",
                [$catid]);
        } else {
            $maxsort = 0;
        }
        $record->enabled     = 0;
        $record->sortorder   = $maxsort+1;
        $record->timecreated = time();
        $elemid = $DB->insert_record('tiny_styles_elements', $record);

        // Bridging table logic.
        if (!empty($data->categoryid)) {
            $link = new stdClass();
            $link->categoryid   = $data->categoryid;
            $link->elementid    = $elemid;
            $link->enabled      = 1;
            $link->sortorder    = $maxsort+1;
            $link->timecreated  = time();
            $link->timemodified = time();
            $DB->insert_record('tiny_styles_cat_elements', $link);
        }

        redirect((new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
            'catid' => $data->categoryid,
            'sesskey' => sesskey()
        ])
        )->out(false), get_string('elementcreated', 'tiny_styles'), 2);
    }
    exit;
}

// Loading data for editing an existing element.
if ($action === 'edit' && $id > 0) {
    if ($element = $DB->get_record('tiny_styles_elements', ['id'=>$id], '*', MUST_EXIST)) {
        $formdata = new stdClass();
        $formdata->id          = $element->id;
        $formdata->action      = 'edit';
        $formdata->name        = $element->name;
        $formdata->type        = $element->type;

        if ($element->custom === '1') {
            $formdata->cssclasses = 'Manual style sheet';
            $formdata->manualconfig = $element->cssclasses;
        } else {
            $formdata->cssclasses  = $element->cssclasses;
        }

        $catlink = $DB->get_record('tiny_styles_cat_elements', ['elementid' => $element->id], '*', IGNORE_MULTIPLE);
        if ($catlink) {
            $formdata->categoryid = $catlink->categoryid;
        } else {
            $formdata->categoryid = 0;
        }

        $mform->set_data($formdata);
    } else {
        print_error('invalidelementid', 'tiny_styles');
    }
} else {
    // Create new style element.
    $formdata = new stdClass();
    $formdata->id = 0;
    $formdata->action = 'create';
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
    ['#btn-preview-element']
);

echo $OUTPUT->footer();
