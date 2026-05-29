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
 * File picker for JSON importing.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/importhandler.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/import.php'));
$PAGE->set_title(get_string('importdata', 'tiny_styles'));

use core\notification;
use tiny_styles\importhandler;

if (optional_param('download_example', false, PARAM_BOOL)) {
    require_sesskey();

    $examplefile = __DIR__ . '/json/example.json';
    if (file_exists($examplefile)) {
        $content = file_get_contents($examplefile);
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="example.json"');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    } else {
        notification::error('Example file not found');
    }
}

/**
 * Standard moodle filepicker
 */
class local_import_form extends moodleform {
    /**
     * Defines the standard moodle filepicker
     * @return void
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $maxbytes = $CFG->maxbytes;
        $mform->addElement(
            'filepicker',
            'jsonfile',
            get_string('importjsonfile', 'tiny_styles', 'JSON File'),
            null,
            [
                'maxbytes' => $maxbytes,
                'accepted_types' => ['.json'],
            ]
        );

        $mform->addRule('jsonfile', null, 'required', null, 'client');

        $mform->addHelpButton('jsonfile', 'importjsonfile', 'tiny_styles');

        $this->add_action_buttons(true, get_string('import', 'tiny_styles'));
    }
}

$returnurl = new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php');

$mform = new local_import_form();

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    $content = $mform->get_file_content('jsonfile');

    if (!$content) {
        notification::error(get_string('nofileuploaded', 'tiny_styles', 'No file uploaded'));
        redirect($returnurl);
    }

    $jsondata = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        notification::error(get_string('invalidjson', 'tiny_styles', 'Invalid JSON format'));
        redirect($returnurl);
    }

    // Process the JSON data with importhandler.
    try {
        importhandler::process($jsondata);
        notification::success(get_string('importsuccess', 'tiny_styles', 'Import successful'));
    } catch (Exception $e) {
        notification::error($e->getMessage());
    }

    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importdata', 'tiny_styles'));

// Prepare template data.
$downloadurl = new moodle_url($PAGE->url, ['download_example' => 1, 'sesskey' => sesskey()]);
$templatedata = [
    'downloadurl' => $downloadurl->out(false),
    'examplefile_label' => get_string('examplefile', 'tiny_styles'),
    'examplefile_name' => 'example.json',
    'instructions0_toggle' => get_string('instructions_0_toggle', 'tiny_styles'),
    'instructions1_usage_heading' => get_string('instructions_1_usage_heading', 'tiny_styles'),
    'instructions1_1_usage_text' => get_string('instructions_1_1_usage_text', 'tiny_styles'),
    'instructions2_structure_heading' => get_string('instructions_2_structure_heading', 'tiny_styles'),
    'instructions2_1_structure_text' => get_string('instructions_2_1_structure_text', 'tiny_styles'),
    'instructions3_cat_intro' => get_string('instructions_3_cat_intro', 'tiny_styles'),
    'instructions3_1_cat_name' => get_string('instructions_3_1_cat_name', 'tiny_styles'),
    'instructions3_2_cat_description' => get_string('instructions_3_2_cat_description', 'tiny_styles'),
    'instructions3_2_2_cat_symbol' => get_string('instructions_3_2_2_cat_symbol', 'tiny_styles'),
    'instructions3_3_cat_menumode' => get_string('instructions_3_3_cat_menumode', 'tiny_styles'),
    'instructions3_4_cat_enabled' => get_string('instructions_3_4_cat_enabled', 'tiny_styles'),
    'instructions3_5_cat_showdesc' => get_string('instructions_3_5_cat_showdesc', 'tiny_styles'),
    'instructions3_6_cat_visibility_admin' => get_string('instructions_3_6_cat_visibility_admin', 'tiny_styles'),
    'instructions3_7_cat_visibility_roles' => get_string('instructions_3_7_cat_visibility_roles', 'tiny_styles'),
    'instructions4_elem_intro' => get_string('instructions_4_elem_intro', 'tiny_styles'),
    'instructions4_1_elem_name' => get_string('instructions_4_1_elem_name', 'tiny_styles'),
    'instructions4_2_elem_custom' => get_string('instructions_4_2_elem_custom', 'tiny_styles'),
    'instructions4_3_elem_type' => get_string('instructions_4_3_elem_type', 'tiny_styles'),
    'instructions4_4_elem_cssclasses' => get_string('instructions_4_4_elem_cssclasses', 'tiny_styles'),
    'instructions4_5_elem_enabled' => get_string('instructions_4_5_elem_enabled', 'tiny_styles'),
    'instructions4_6_elem_visibility_admin' => get_string('instructions_4_6_elem_visibility_admin', 'tiny_styles'),
    'instructions4_7_elem_visibility_roles' => get_string('instructions_4_7_elem_visibility_roles', 'tiny_styles'),
    'instructions5_example_heading' => get_string('instructions_5_example_heading', 'tiny_styles'),
    'instructions5_1_example_note' => get_string('instructions_5_1_example_note', 'tiny_styles'),
    'instructions5_2_example_code' => get_string('instructions_5_2_example_code', 'tiny_styles'),
    'instructions6_good_heading' => get_string('instructions_6_good_heading', 'tiny_styles'),
    'instructions6_1_good_list' => get_string('instructions_6_1_good_list', 'tiny_styles'),
];

// Render template.
echo $OUTPUT->render_from_template('tiny_styles/import_page', $templatedata);

$mform->display();
echo $OUTPUT->footer();
