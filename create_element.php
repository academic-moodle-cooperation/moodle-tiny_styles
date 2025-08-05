<?php

use Dom\Text;
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

        // Name
        $mform->addElement(
            'text',
            'name',
            get_string('name'),
            ['size' => 50, 'style' => 'width: 400px;', 'maxlength' => 100]
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');


        $categories = $DB->get_records_menu('tiny_styles_categories', null, 'sortorder ASC', 'id,name');

        // todo: remove the divider more efficiently by a conditional query
        foreach ($categories as $id => $name) {
            $menumode = $DB->get_field('tiny_styles_categories', 'menumode', ['id' => $id]);
            if ($menumode === 'divider') {
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
        $mform->addHelpButton('categoryid', 'categoryhelp', 'tiny_styles');

        // CSS classes
        $elements = $DB->get_fieldset_sql("
            SELECT cssclasses
            FROM (
                SELECT cssclasses
                FROM {tiny_styles_elements}
                GROUP BY cssclasses
                ORDER BY MIN(id) ASC
                LIMIT 16
            ) sub
        ");

        $cssoptions = [
            'Manual style' => 'Manual style',
            '' => ''
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
            'select', 'type',
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

        // Manual configuration hidden unless "Manual style" selected.
        $mform->hideIf('manualconfig', 'cssclasses', 'neq', 'Manual style');
        $mform->hideIf('type', 'cssclasses', 'neq', 'Manual style');

        $mform->addElement(
            'static',
            'manualconfig_help',
            '',
            get_string('manualdefault', 'tiny_styles')
        );
        $mform->hideIf('manualconfig_help', 'cssclasses', 'neq', 'Manual style');

        // Hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

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

    // todo: expand validation
    public function validation($data, $files) {
        $errors = array();

        if (strlen(trim($data['name'])) < 3) {
            $errors['name'] = get_string('error_nametooshort', 'tiny_styles');
        }
        return $errors;
    }
}
$formurl = new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
    'action' => $action,
    'id'     => $id,
    'catid'  => $catid
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
    global $DB;
    $record = new stdClass();

    if (!isset($data->manualconfig)) {
        $data->manualconfig = '';
    }

    if($data->cssclasses == 'Manual style') {
        $record->cssclasses = $data->manualconfig;
        $record->custom     = 1;
        $record->type       = $data->type;
    } else {
        $record->cssclasses = $data->cssclasses;
        $record->custom = 0;
        if (strpos($record->cssclasses, 'alert') !== false) {
            $record->type = 'block';
        } else {
            $record->type = 'inline';
        }
    }

    $record->name       = $data->name;
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
            $formdata->cssclasses = 'Manual style';
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

$PAGE->requires->js_amd_inline("
// Vanilla JS solution that mimics the jQuery pattern very closely
require([], function() {
    // Define our function exactly like in the jQuery version
    function checkForAlertClass() {
        var selectedClass = document.getElementById('id_cssclasses').value;
        if (selectedClass && selectedClass.indexOf('alert') !== -1) {
            document.getElementById('id_type').value = 'block';
        }
        if (selectedClass && selectedClass.indexOf('badge') !== -1) {
            document.getElementById('id_type').value = 'inline';
        }
    }
    
    // This simulates $(document).ready()
    function docReady(fn) {
        // If document is already loaded, run the function now
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(fn, 1); // Slight delay to ensure DOM is fully available
            return;
        }
        
        // Otherwise, wait for DOMContentLoaded
        document.addEventListener('DOMContentLoaded', fn);
    }
    
    // This is our equivalent to $(document).ready(function() {...})
    docReady(function() {
        // Get the element
        var cssClassesField = document.getElementById('id_cssclasses');
        
        if (cssClassesField) {
            // Add the change event listener - equivalent to $('#id_cssclasses').on('change', ...)
            cssClassesField.addEventListener('change', function() {
                checkForAlertClass();
            });
            
            // Run once immediately after DOM is ready
            checkForAlertClass();
        } else {
            // If the element wasn't found, try again after a short delay
            setTimeout(function() {
                cssClassesField = document.getElementById('id_cssclasses');
                if (cssClassesField) {
                    cssClassesField.addEventListener('change', function() {
                        checkForAlertClass();
                    });
                    checkForAlertClass();
                }
            }, 100);
        }
    });
});
");

echo $OUTPUT->footer();
