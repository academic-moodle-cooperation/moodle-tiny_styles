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
 * Methods for fetching data from the DB to the editor.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_styles\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');
use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use stdClass;
use context_system;

/**
 * Fetches and returns all enabled categories and associated elements.
 */
class fetch_categories extends external_api {
    /**
     * Returns the external function parameters required by execute().
     * Additional parameters can be defined later.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(
                PARAM_INT,
                'Editor page context id',
                VALUE_DEFAULT,
                0
            ),
        ]);
    }

    /**
     * Fetches and returns all enabled categories and associated elements.
     *
     * @param int $contextid Context id of the page hosting the editor, for role-based visibility
     * @return array Nested array of categories with associated elements
     */
    public static function execute($contextid = 0) {
        global $DB, $USER;

        ['contextid' => $contextid] = self::validate_parameters(
            self::execute_parameters(),
            ['contextid' => $contextid]
        );

        // Resolve the page context.
        try {
            $context = $contextid ? \context::instance_by_id($contextid) : context_system::instance();
        } catch (\Throwable $e) {
            $context = context_system::instance();
        }
        self::validate_context($context);

        $sql = "SELECT c.id, c.name, c.symbol, c.menumode, c.description, c.showdesc,
                       c.visibility_admin, c.visibility_roles,
                       e.id AS elemid, e.name AS elemname, e.type, e.cssclasses, e.custom,
                       e.visibility_admin AS elemvisibility_admin,
                       e.visibility_roles AS elemvisibility_roles
                  FROM {tiny_styles_categories} c
             LEFT JOIN {tiny_styles_cat_elements} ce ON ce.categoryid = c.id
             LEFT JOIN {tiny_styles_elements} e ON e.id = ce.elementid
                 WHERE c.enabled = 1
                   AND (e.enabled = 1 OR c.menumode = 'divider')
              ORDER BY c.sortorder, ce.sortorder";
        $recordset = $DB->get_recordset_sql($sql);

        $categories = [];
        foreach ($recordset as $r) {
            $catid = $r->id;
            if (!isset($categories[$catid])) {
                $categories[$catid] = [
                    'id'               => $catid,
                    'name'             => tiny_styles_format_name($r->name, $context),
                    'symbol'           => $r->symbol,
                    'description'      => tiny_styles_format_name($r->description, $context),
                    'showdesc'         => $r->showdesc,
                    'menumode'         => $r->menumode,
                    'visibility_admin' => $r->visibility_admin ?? 'all',
                    'visibility_roles' => $r->visibility_roles ?? '',
                    'elements'         => [],
                ];
            }
            if (!empty($r->elemid)) {
                $categories[$catid]['elements'][] = [
                    'id'               => $r->elemid,
                    'name'             => tiny_styles_format_name($r->elemname, $context),
                    'type'             => $r->type,
                    'cssclasses'       => $r->cssclasses,
                    'custom'           => $r->custom,
                    'visibility_admin' => $r->elemvisibility_admin ?? 'all',
                    'visibility_roles' => $r->elemvisibility_roles ?? '',
                ];
            }
        }
        $recordset->close();

        // Visibility scoped to user admin status and the roles they hold in the context.
        $isadmin = is_siteadmin($USER);
        $userroles = get_user_roles($context, $USER->id, true);
        $userroleids = array_values(array_unique(array_map(
            static fn($role) => (int)$role->roleid,
            $userroles
        )));

        // Filter categories and their elements based on visibility settings.
        foreach ($categories as $catid => $cat) {
            if (!tiny_styles_user_can_see($cat, $isadmin, $userroleids)) {
                unset($categories[$catid]);
                continue;
            }
            foreach ($cat['elements'] as $elemkey => $elem) {
                // Parent category can tighten the restriction the elements are filtered on.
                $effective = tiny_styles_effective_visibility($cat, $elem);
                if (!tiny_styles_user_can_see($effective, $isadmin, $userroleids)) {
                    unset($categories[$catid]['elements'][$elemkey]);
                }
            }
            $categories[$catid]['elements'] = array_values($categories[$catid]['elements']);
        }

        return array_values($categories);
    }

    /**
     * Defines the structure of the data returned by the external function.
     * @return external_multiple_structure
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'           => new external_value(PARAM_INT, 'Category ID'),
                'name'         => new external_value(PARAM_TEXT, 'Category name'),
                'symbol'       => new external_value(PARAM_RAW, 'Optional FA symbol', VALUE_OPTIONAL),
                'description'  => new external_value(PARAM_TEXT, 'Category description'),
                'showdesc'     => new external_value(PARAM_TEXT, 'never/helptext/tooltip'),
                'menumode' => new external_value(PARAM_TEXT, 'divider/submenu/inline/'),
                'elements'     => new external_multiple_structure(
                    new external_single_structure([
                        'id'         => new external_value(PARAM_INT, 'Element ID'),
                        'name'       => new external_value(PARAM_TEXT, 'Element name'),
                        'type'       => new external_value(PARAM_TEXT, 'inline/block'),
                        'cssclasses' => new external_value(PARAM_RAW, 'e.g. "alert alert-info"'),
                        'custom'     => new external_value(PARAM_INT, 'Custom CSS class boolean'),
                    ]),
                    'list of bridging elements',
                    VALUE_OPTIONAL
                ),
            ])
        );
    }
}
