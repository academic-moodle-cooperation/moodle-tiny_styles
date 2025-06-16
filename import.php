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
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
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

/**
 * Standard filepicker
 */
class local_import_form extends moodleform {
    public function definition() {
        global $CFG;
        $mform = $this->_form;

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

        $mform->addHelpButton('jsonfile', 'importjsonfile', 'tiny_styles');

        $this->add_action_buttons(true, get_string('import', 'tiny_styles'));
    }
}

$returnurl = new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']);

$mform = new local_import_form();

if ($mform->is_cancelled()) {
    redirect($returnurl);
}
else if ($data = $mform->get_data()) {
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

    // Process the JSON data with importhandler
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
$mform->display();
//separate window for exporting import examples 
?>
<div class="mt-4 p-3 border rounded">
    <h5><?php echo get_string('examplefiles_heading', 'tiny_styles'); ?></h5>
    <p class="text-muted"><?php echo get_string('examplefiles_description', 'tiny_styles'); ?></p>

    <form method="post" action="exportexamples.php" class="d-flex align-items-center gap-2">
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
        <span class="form-text"><?php echo get_string('examplefiles_label', 'tiny_styles'); ?></span>
        <button type="submit" class="btn btn-secondary"><?php echo get_string('download_button', 'tiny_styles'); ?></button>
    </form>
</div>

<?php
echo $OUTPUT->footer();
