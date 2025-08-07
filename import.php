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
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

$returnurl = new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']);

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
$downloadurl = new moodle_url($PAGE->url, ['download_example' => 1, 'sesskey' => sesskey()]);
?>
<div style="margin-bottom: 20px;">
    <div class="row align-items-center">
        <div class="col-md-3">
            <label class="form-label mb-0"><?php echo get_string('examplefile', 'tiny_styles'); ?></label>
        </div>
        <div class="col-md-9">
            <a href="<?php echo $downloadurl->out(); ?>" class="btn btn-link p-0">
                example.json
            </a>
            <details class="mt-2">
                <summary style="cursor: pointer;"><?php echo get_string('instructions_toggle', 'tiny_styles'); ?></summary>
                <div class="mt-2 p-2 bg-light border rounded">
                    <p><strong><?php echo get_string('instructions_heading', 'tiny_styles'); ?></strong></p>

                    <h3><?php echo get_string('instr_structure_heading', 'tiny_styles'); ?></h3>
                    <p><?php echo get_string('instr_structure_text', 'tiny_styles'); ?></p>
                    
                    <p><strong><?php echo get_string('instr_visualized_label', 'tiny_styles'); ?></strong></p>
                    <pre><code><?php echo get_string('instr_visualized_code', 'tiny_styles'); ?></code></pre>
                    
                    <h3><?php echo get_string('instr_usage_heading', 'tiny_styles'); ?></h3>
                    <p><?php echo get_string('instr_usage_text', 'tiny_styles'); ?></p>
                    
                    <h4><?php echo get_string('instr_fill_heading', 'tiny_styles'); ?></h4>
                    <p><?php echo get_string('instr_fill_note', 'tiny_styles'); ?></p>
                    <pre><code><?php echo get_string('instr_fill_code', 'tiny_styles'); ?></code></pre>
                    
                    <h3><?php echo get_string('instr_expl_heading', 'tiny_styles'); ?></h3>
                    <p><?php echo get_string('instr_expl_intro', 'tiny_styles'); ?></p>
                    <p><strong><?php echo get_string('instr_fields_title', 'tiny_styles'); ?></strong></p>
                    
                    <h5><?php echo get_string('instr_cat_heading', 'tiny_styles'); ?></h5>
                    <?php echo get_string('instr_cat_list', 'tiny_styles'); ?>

                    <h5><?php echo get_string('instr_elem_heading', 'tiny_styles'); ?></h5>
                    <?php echo get_string('instr_elem_list', 'tiny_styles'); ?>
                    <hr>
                    <h3><?php echo get_string('instr_good_heading', 'tiny_styles'); ?></h3>
                    <?php echo get_string('instr_good_list', 'tiny_styles'); ?>

                </div>
            </details>
        </div>
    </div>
</div>
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
 * Repeated for the AMC pipeline
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$mform->display();
echo $OUTPUT->footer();
