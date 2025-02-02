<?php
// placeholder page for the eventual element creation
require_once(__DIR__ . '/../../../../../config.php');
require_login();

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php'));
$PAGE->set_title(get_string('create_element', 'tiny_styles'));
$PAGE->set_heading(get_string('create_element', 'tiny_styles'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('create_element', 'tiny_styles'));
echo $OUTPUT->footer();
