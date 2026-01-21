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
 * Locallib for extra methods for CATEGORY.PHP, CATEGORYSETTINGS.PHP, ELMENTS.PHP, CREATE_ELEMENT.PHP.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get all categories ordered by sortorder.
 *
 * @param string $orderby Order clause (default 'sortorder ASC')
 * @return array Array of category records
 */
function tiny_styles_get_all_categories($orderby = 'sortorder ASC') {
    global $DB;
    return $DB->get_records('tiny_styles_categories', null, $orderby);
}

/**
 * Get all bridge records for a category.
 *
 * @param int $categoryid Category ID
 * @return array Array of bridge records
 */
function tiny_styles_get_category_bridges($categoryid) {
    global $DB;
    return $DB->get_records('tiny_styles_cat_elements', ['categoryid' => $categoryid]);
}

/**
 * Count how many categories use this element.
 *
 * @param int $elementid Element ID
 * @return int Count of categories using this element
 */
function tiny_styles_count_element_usage($elementid) {
    global $DB;
    return $DB->count_records('tiny_styles_cat_elements', ['elementid' => $elementid]);
}

/**
 * Delete an element record.
 *
 * @param int $elementid Element ID
 * @return bool Success
 */
function tiny_styles_delete_element_record($elementid) {
    global $DB;
    return $DB->delete_records('tiny_styles_elements', ['id' => $elementid]);
}

/**
 * Delete all bridge records for a category.
 *
 * @param int $categoryid Category ID
 * @return bool Success
 */
function tiny_styles_delete_category_bridges($categoryid) {
    global $DB;
    return $DB->delete_records('tiny_styles_cat_elements', ['categoryid' => $categoryid]);
}

/**
 * Delete a category record.
 *
 * @param int $categoryid Category ID
 * @return bool Success
 */
function tiny_styles_delete_category_record($categoryid) {
    global $DB;
    return $DB->delete_records('tiny_styles_categories', ['id' => $categoryid]);
}

/**
 * Get a single category by ID.
 *
 * @param int $categoryid Category ID
 * @return stdClass Category record
 * @throws dml_exception If category not found
 */
function tiny_styles_get_category($categoryid) {
    global $DB;
    return $DB->get_record('tiny_styles_categories', ['id' => $categoryid], '*', MUST_EXIST);
}

/**
 * Get the maximum sortorder value for categories.
 *
 * @return int Maximum sortorder value, or 0 if no categories exist
 */
function tiny_styles_get_max_category_sortorder() {
    global $DB;
    $maxsort = $DB->get_field_sql("SELECT MAX(sortorder) FROM {tiny_styles_categories}");
    return $maxsort === null ? 0 : $maxsort;
}

/**
 * Insert a new category record.
 *
 * @param stdClass $record Category record to insert
 * @return int New category ID
 */
function tiny_styles_insert_category($record) {
    global $DB;
    return $DB->insert_record('tiny_styles_categories', $record);
}

/**
 * Update an existing category record.
 *
 * @param stdClass $record Category record with id field
 * @return bool Success
 */
function tiny_styles_update_category($record) {
    global $DB;
    return $DB->update_record('tiny_styles_categories', $record);
}

/**
 * Delete a category with its bridges, and orphaned elements.
 *
 * @param int $categoryid Category ID to delete
 * @return void
 * @throws dml_exception If database error occurs
 */
function tiny_styles_delete_category_with_cleanup($categoryid) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    try {
        // Get all bridging records for this category.
        $bridges = tiny_styles_get_category_bridges($categoryid);

        // Check each element delete if only used in this category.
        foreach ($bridges as $bridge) {
            $usagecount = tiny_styles_count_element_usage($bridge->elementid);
            if ($usagecount <= 1) {
                tiny_styles_delete_element_record($bridge->elementid);
            }
        }

        tiny_styles_delete_category_bridges($categoryid);
        tiny_styles_delete_category_record($categoryid);

        $transaction->allow_commit();
    } catch (Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
}

/**
 * Prepare a category record for saving to database.
 * Handles both create and edit actions.
 *
 * @param stdClass $formdata Form data from moodleform
 * @param string $action 'create' or 'edit'
 * @return stdClass Prepared category record
 */
function tiny_styles_prepare_category_for_save($formdata, $action = 'create') {
    $record = new stdClass();
    $record->name = $formdata->name;
    $record->description = $formdata->description ?? '';
    $record->symbol = $formdata->selectedicon ?? '';
    $record->menumode = $formdata->menumode ?? 'submenu';
    $record->timemodified = time();

    if ($action === 'edit' && !empty($formdata->id)) {
        // Editing existing category preserves existing values.
        $old = tiny_styles_get_category($formdata->id);
        $record->id = $old->id;
        $record->enabled = $old->enabled;
        $record->sortorder = $old->sortorder;
        $record->timecreated = $old->timecreated;
    } else {
        // Creating new category sets defaults.
        $record->enabled = 0;
        $record->sortorder = tiny_styles_get_max_category_sortorder() + 1;
        $record->timecreated = time();
    }

    return $record;
}

/**
 * Load category data formatted for form display.
 *
 * @param int $categoryid Category ID
 * @return stdClass Form data object
 * @throws dml_exception If category not found
 */
function tiny_styles_load_category_for_form($categoryid) {
    $category = tiny_styles_get_category($categoryid);

    $formdata = new stdClass();
    $formdata->id = $category->id;
    $formdata->tiny_styles_action = 'edit';
    $formdata->name = $category->name;
    $formdata->description = $category->description;
    $formdata->selectedicon = $category->symbol;
    $formdata->menumode = $category->menumode;

    return $formdata;
}

/**
 * Get list of available icon files from pix directory.
 *
 * @return array Array of icon filenames (e.g. ['book.svg', 'box.svg', ...])
 */
function tiny_styles_get_available_icons() {
    global $CFG;

    $iconpath = $CFG->dirroot . '/lib/editor/tiny/plugins/styles/pix';
    $icons = [];

    if (is_dir($iconpath)) {
        $files = scandir($iconpath);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'icon.svg' || $file === 'remove.svg') {
                continue;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext === 'svg') {
                $icons[] = $file;
            }
        }
    }

    return $icons;
}


/**
 * Get all elements for a specific category.
 *
 * @param int $categoryid Category ID
 * @return array Array of element records
 */
function tiny_styles_get_elements_by_category($categoryid) {
    global $DB;

    $sql = "SELECT e.*, ce.sortorder as bridge_sortorder
              FROM {tiny_styles_elements} e
              JOIN {tiny_styles_cat_elements} ce ON ce.elementid = e.id
             WHERE ce.categoryid = :catid
             ORDER BY ce.sortorder ASC, e.id";

    return $DB->get_records_sql($sql, ['catid' => $categoryid]);
}

/**
 * Get category name by ID.
 *
 * @param int $categoryid Category ID
 * @return string Category name, or empty string if not found
 */
function tiny_styles_get_category_name($categoryid) {
    global $DB;
    $name = $DB->get_field('tiny_styles_categories', 'name', ['id' => $categoryid]);
    return $name ? $name : '';
}

/**
 * Delete all bridge records for an element.
 *
 * @param int $elementid Element ID
 * @return bool Success
 */
function tiny_styles_delete_element_bridges($elementid) {
    global $DB;
    return $DB->delete_records('tiny_styles_cat_elements', ['elementid' => $elementid]);
}


/**
 * Delete an element with all its bridge records.
 *
 * @param int $elementid Element ID to delete
 * @return void
 * @throws dml_exception If database error occurs
 */
function tiny_styles_delete_element_with_bridges($elementid) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    try {
        tiny_styles_delete_element_bridges($elementid);
        tiny_styles_delete_element_record($elementid);

        $transaction->allow_commit();
    } catch (Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
}

/**
 * Get categories suitable for dropdown selection.
 *
 * @param bool $excludedividers Whether to exclude divider categories
 * @return array Array of id => name pairs
 */
function tiny_styles_get_categories_for_dropdown($excludedividers = true) {
    global $DB;

    if ($excludedividers) {
        $sql = "SELECT id, name
                  FROM {tiny_styles_categories}
                 WHERE menumode != 'divider'
                 ORDER BY sortorder ASC";
        $records = $DB->get_records_sql($sql);
    } else {
        $records = $DB->get_records(
            'tiny_styles_categories',
            null,
            'sortorder ASC',
            'id, name'
        );
    }

    // Apply format_string to category names.
    $formatted = [];
    foreach ($records as $record) {
        $formatted[$record->id] = format_string($record->name, true, ['context' => context_system::instance()]);
    }

    return $formatted;
}

/**
 * Get predefined CSS classes for create_element.php.
 *
 * @return array Array of the CSS class strings
 */
function tiny_styles_get_css_classes() {
    return [
        // Labels.
        'badge bg-primary text-white',
        'badge bg-info text-dark',
        'badge bg-success text-white',
        'badge bg-warning text-dark',
        'badge bg-danger text-white',
        'badge bg-dark text-white',
        'badge bg-secondary text-dark',
        'badge bg-light text-dark',
        // Boxes.
        'alert alert-primary',
        'alert alert-info',
        'alert alert-success',
        'alert alert-warning',
        'alert alert-danger',
        'alert alert-dark',
        'alert alert-secondary',
        'alert alert-light',
    ];
}

/**
 * Get a single element by ID.
 *
 * @param int $elementid Element ID
 * @return stdClass Element record
 * @throws dml_exception If element not found
 */
function tiny_styles_get_element($elementid) {
    global $DB;
    return $DB->get_record('tiny_styles_elements', ['id' => $elementid], '*', MUST_EXIST);
}

/**
 * Get the bridge record linking an element to its category.
 *
 * @param int $elementid Element ID
 * @return stdClass|false Bridge record, or false if not found
 */
function tiny_styles_get_element_category_link($elementid) {
    global $DB;
    return $DB->get_record(
        'tiny_styles_cat_elements',
        ['elementid' => $elementid],
        '*',
        IGNORE_MULTIPLE
    );
}

/**
 * Insert a new element record.
 *
 * @param stdClass $record Element record to insert
 * @return int New element ID
 */
function tiny_styles_insert_element($record) {
    global $DB;
    return $DB->insert_record('tiny_styles_elements', $record);
}

/**
 * Update an existing element record.
 *
 * @param stdClass $record Element record with id field
 * @return bool Success
 */
function tiny_styles_update_element($record) {
    global $DB;
    return $DB->update_record('tiny_styles_elements', $record);
}

/**
 * Create a bridge record linking element to category.
 *
 * @param int $categoryid Category ID
 * @param int $elementid Element ID
 * @param int $sortorder Sort order value
 * @return int New bridge record ID
 */
function tiny_styles_create_bridge($categoryid, $elementid, $sortorder) {
    global $DB;

    $bridge = new stdClass();
    $bridge->categoryid = $categoryid;
    $bridge->elementid = $elementid;
    $bridge->enabled = 1;
    $bridge->sortorder = $sortorder;
    $bridge->timecreated = time();
    $bridge->timemodified = time();

    return $DB->insert_record('tiny_styles_cat_elements', $bridge);
}

/**
 * Get the maximum bridge sortorder for a category.
 *
 * @param int $categoryid Category ID
 * @return int Maximum sortorder value, or 0 if no bridges exist
 */
function tiny_styles_get_max_bridge_sortorder($categoryid) {
    global $DB;

    $maxsort = $DB->get_field_sql(
        "SELECT MAX(sortorder)
           FROM {tiny_styles_cat_elements}
          WHERE categoryid = ?",
        [$categoryid]
    );

    return $maxsort === null ? 0 : $maxsort;
}


/**
 * Prepare an element record for saving to database.
 * Handles both create and edit actions, manual and predefined CSS.
 *
 * @param stdClass $formdata Form data from moodleform
 * @param string $action 'create' or 'edit'
 * @return stdClass Prepared element record
 */
function tiny_styles_prepare_element_for_save($formdata, $action = 'create') {
    $record = new stdClass();
    $record->name = $formdata->name;
    $record->timemodified = time();

    // Determine if manual config or predefined CSS class.
    if ($formdata->cssclasses == '_manual') {
        $record->cssclasses = $formdata->manualconfig ?? '';
        $record->custom = 1;
        $record->type = $formdata->type;
    } else {
        $record->cssclasses = $formdata->cssclasses;
        $record->custom = 0;
        $record->type = tiny_styles_check_for_style_type($formdata->cssclasses) ?? 'inline';
    }

    if ($action === 'edit' && !empty($formdata->id)) {
        // Editing existing element preserves existing values.
        $old = tiny_styles_get_element($formdata->id);
        $record->id = $old->id;
        $record->enabled = $old->enabled;
        $record->sortorder = $old->sortorder;
        $record->timecreated = $old->timecreated;
    } else {
        // Creating new element sets defaults.
        $record->enabled = 0;
        $record->timecreated = time();
        // Placeholder.
        $record->sortorder = 0;
    }

    return $record;
}

/**
 * Create a new element and link it to a category.
 *
 * @param stdClass $formdata Form data from moodleform
 * @param int $categoryid Category ID to link element to
 * @return int New element ID
 * @throws dml_exception If database error occurs
 */
function tiny_styles_create_element_with_bridge($formdata, $categoryid) {
    global $DB;

    $transaction = $DB->start_delegated_transaction();
    try {
        $record = tiny_styles_prepare_element_for_save($formdata, 'create');
        $maxsort = tiny_styles_get_max_bridge_sortorder($categoryid);
        $nextsort = $maxsort + 1;
        $record->sortorder = $nextsort;

        $elementid = tiny_styles_insert_element($record);
        tiny_styles_create_bridge($categoryid, $elementid, $nextsort);

        $transaction->allow_commit();
        return $elementid;
    } catch (Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
}

/**
 * Update an existing element.
 *
 * @param stdClass $formdata Form data from moodleform
 * @return void
 * @throws dml_exception If database error occurs
 */
function tiny_styles_update_element_full($formdata) {
    $record = tiny_styles_prepare_element_for_save($formdata, 'edit');
    tiny_styles_update_element($record);
}

/**
 * Load element data formatted for form display.
 *
 * @param int $elementid Element ID
 * @return stdClass Form data object
 * @throws dml_exception If element not found
 */
function tiny_styles_load_element_for_form($elementid) {
    $element = tiny_styles_get_element($elementid);

    $formdata = new stdClass();
    $formdata->id = $element->id;
    $formdata->tiny_styles_action = 'edit';
    $formdata->name = $element->name;
    $formdata->type = $element->type;

    // Handle custom vs predefined CSS classes.
    if ($element->custom == 1) {
        $formdata->cssclasses = '_manual';
        $formdata->manualconfig = $element->cssclasses;
    } else {
        $formdata->cssclasses = $element->cssclasses;
        $formdata->manualconfig = '';
    }

    // Get category link.
    $catlink = tiny_styles_get_element_category_link($element->id);
    if ($catlink) {
        $formdata->categoryid = $catlink->categoryid;
    } else {
        $formdata->categoryid = 0;
    }

    return $formdata;
}

/**
 * Dynamic type checking for the predefined CSS classes.
 *
 * @param string $style the predefined CSS class
 * @return string Given type, either inline or block
 */
function tiny_styles_check_for_style_type($style) {
    if (strpos($style, 'alert') !== false) {
            return 'block';
    }
    return 'inline';
}
