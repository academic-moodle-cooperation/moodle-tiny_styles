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
$downloadurl = new moodle_url($PAGE->url, ['download_example' => 1, 'sesskey' => sesskey()]);
?>
<div class="mt-3 mb-4 p-3 border rounded">
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
                    <p>
                    <h3>Structure</h3>
<p>
    The <code>example.json</code> is structured into a category array, where each category contains an element array. 
    This is the format which any JSON file imported should follow.
</p>

<p>Visualized:</p>
<pre><code>Categories: [ category_1, category_2 ... category_n ]

category_1: [ element_a, element_b ... element_n ]
category_2: [ element_x, element_y ...
→ with the elements carrying the styling information
</code></pre>

<h3>How to use the JSON</h3>
<p>
    The example JSON can be easily used for editing directly and expanded by copying it.
    <br><strong>Note:</strong> The user should follow correct JSON syntax and formatting for the file to work properly.
</p>

<h4>How to fill out the <em>example.json</em>:</h4>
<p>(See below for further explanations of <code>enabled</code>, <code>type</code>, etc.)</p>

<pre><code>"categories": [
    {
        "name": "Enter a minimum 3 characters long name here.",
        "description": "Write a short category description here.",
        "showdesc": "pick one of the following: helptext/tooltip/never",
        "presentation": "pick one of the following: submenu/inline/divider",
        "enabled": 1,
        "elements": [
            {
                "name": "enter a descriptive name here",
                "type": "pick either inline or block",
                "cssclasses": "a valid css styling alert alert-danger",
                "enabled": 1,
                "custom": 0
            },
            ... next elements ...
        ]
    },
    ... possible to add more categories ...
]</code></pre>

<h3>Explanations</h3>
<p>The naming and description fields are self-explanatory.</p>

<strong>The other fields are:</strong>

<h5>Category:</h5>
<ul>
    <li><strong>showdesc:</strong> how the description is shown to users, or if at all</li>
    <li><strong>presentation:</strong> how elements are displayed in the editor (submenu / inline / divider)</li>
    <li><strong>enabled:</strong> either <code>1</code> (enabled) or <code>0</code> (disabled), default: <code>0</code></li>
</ul>

<h5>Element:</h5>
<ul>
    <li><strong>type:</strong> <code>inline</code> or <code>block</code><br>
        <em>inline is for styling short text or words<br>
        block is for paragraphs or larger text blocks</em>
    </li>
    <li><strong>cssclasses:</strong> CSS styling for the text<br>
        <em>This can be Bootstrap classes or inline CSS, eg. <code>color: red; font-weight: bold;</code></em>
    </li>
    <li><strong>enabled:</strong> same logic as category</li>
    <li><strong>custom:</strong> <code>1</code> if using custom CSS inline code</li>
</ul>

<hr>

<h3>Good to Know</h3>
<ul>
    <li>
        Duplicate categories can be imported multiple times. This avoids accidental deletions or edits.
        <br><strong>Suggestion:</strong> Use the example JSON as the basis for imports to prevent duplicates.
    </li>
    <li>All fields can be edited later via Moodle admin pages.</li>
    <li>Currently, icon selection must be done manually through the Moodle admin category editor.</li>
    <li>See the full exported JSON for more examples and detailed usage.</li>
</ul>
</p>
                </div>
            </details>
        </div>
    </div>
</div>

<?php
$mform->display();
echo $OUTPUT->footer();
