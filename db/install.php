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
 * Post-install script for some default values for tiny_styles
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



/**
 * Custom installation logic for the default styles.
 *
 */
function xmldb_tiny_styles_install() {
    global $DB;

    // Adding default categories.
    if (!$DB->record_exists('tiny_styles_categories', [])) {

        // Labels.
        $cat = new stdClass();
        $cat->name         = 'Labels';
        $cat->description  = 'Design Elements directly in Text';
        $cat->showdesc     = 'helptext';
        $cat->symbol       = 'label.svg';
        $cat->menumode = 'submenu';
        $cat->enabled      = 1;
        $cat->sortorder    = 1;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        $labelcatid = $DB->insert_record('tiny_styles_categories', $cat);

        // Boxes.
        $cat = new stdClass();
        $cat->name         = 'Boxes';
        $cat->description  = 'Format paragraphs';
        $cat->showdesc     = 'never';
        $cat->symbol       = 'box.svg';
        $cat->menumode = 'submenu';
        $cat->enabled      = 1;
        $cat->sortorder    = 2;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        $boxencatid = $DB->insert_record('tiny_styles_categories', $cat);

        // These two categories can be uncommented in future development.
        // Divider.
        $cat = new stdClass();
        $cat->name         = '-------';
        $cat->description  = '';
        $cat->showdesc     = 'never';
        $cat->symbol       = '';
        $cat->menumode = 'divider';
        $cat->enabled      = 1;
        $cat->sortorder    = 3;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        // Uncomment: $dividercatid = $DB->insert_record('tiny_styles_categories', $cat);.

        // Uni-Vorlagen category.
        $cat = new stdClass();
        $cat->name         = 'Uni-Vorlagen';
        $cat->description  = 'Vorlagen im Corporate Design der Uni';
        $cat->showdesc     = 'tooltip';
        $cat->symbol       = 'school.svg';
        $cat->menumode = 'inline';
        $cat->enabled      = 1;
        $cat->sortorder    = 4;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        // Uncomment: $unicatid = $DB->insert_record('tiny_styles_categories', $cat);.
    }

    // Default badge and alerts bootstrap elements.
    if (!$DB->record_exists('tiny_styles_elements', [])) {
        $elements = [
            [
                'name'       => 'Blue Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-primary text-white',
                'sortorder'  => 1,
            ],
            [
                'name'       => 'Green Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-success text-white',
                'sortorder'  => 2,
            ],
            [
                'name'       => 'Gray Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-secondary text-dark',
                'sortorder'  => 3,
            ],
            [
                'name'       => 'Yellow Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-warning text-dark',
                'sortorder'  => 4,
            ],
            [
                'name'       => 'Red Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-danger text-white',
                'sortorder'  => 5,
            ],
            [
                'name'       => 'Info Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-info text-dark',
                'sortorder'  => 6,
            ],
            [
                'name'       => 'Dark Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-dark text-white',
                'sortorder'  => 7,
            ],
            [
                'name'       => 'Light Label',
                'type'       => 'inline',
                'cssclasses' => 'badge bg-light text-dark',
                'sortorder'  => 8,
            ],
            [
                'name'       => 'Info Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-info',
                'sortorder'  => 9,
            ],
            [
                'name'       => 'Yellow Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-warning',
                'sortorder'  => 10,
            ],
            [
                'name'       => 'Red Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-danger',
                'sortorder'  => 11,
            ],
            [
                'name'       => 'Green Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-success',
                'sortorder'  => 12,
            ],
            [
                'name'       => 'Dark Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-dark',
                'sortorder'  => 13,
            ],
            [
                'name'       => 'Blue Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-primary',
                'sortorder'  => 14,
            ],
            [
                'name'       => 'Grey Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-secondary',
                'sortorder'  => 15,
            ],
            [
                'name'       => 'Light Box',
                'type'       => 'block',
                'cssclasses' => 'alert alert-light',
                'sortorder'  => 16,
            ],
            [
                'name'       => 'Divider Link',
                'type'       => '',
                'cssclasses' => '',
                'sortorder'  => 17,
            ],
        ];

        foreach ($elements as $key => $data) {
            $elem = new stdClass();
            $elem->name        = $data['name'];
            $elem->type        = $data['type'];
            $elem->cssclasses  = $data['cssclasses'];
            $elem->enabled     = 1;
            $elem->sortorder   = $data['sortorder'];
            $elem->timecreated = time();
            $elem->timemodified = time();
            $elements[$key]['id'] = $DB->insert_record('tiny_styles_elements', $elem);
        }
    }

    // Bridging table ->tiny_styles_cat_elements in the db.
    if (!$DB->record_exists('tiny_styles_cat_elements', [])) {
        // Elements to categories.
        // One category can have many elements and one element can have many parent categories.
        $links = [
            ['categoryid' => $labelcatid, 'elementid' => $elements[0]['id'], 'sortorder' => 1],
            ['categoryid' => $labelcatid, 'elementid' => $elements[1]['id'], 'sortorder' => 2],
            ['categoryid' => $labelcatid, 'elementid' => $elements[2]['id'], 'sortorder' => 3],
            ['categoryid' => $labelcatid, 'elementid' => $elements[3]['id'], 'sortorder' => 4],
            ['categoryid' => $labelcatid, 'elementid' => $elements[4]['id'], 'sortorder' => 5],
            ['categoryid' => $labelcatid, 'elementid' => $elements[5]['id'], 'sortorder' => 6],
            ['categoryid' => $labelcatid, 'elementid' => $elements[6]['id'], 'sortorder' => 7],
            ['categoryid' => $labelcatid, 'elementid' => $elements[7]['id'], 'sortorder' => 8],
            ['categoryid' => $boxencatid, 'elementid' => $elements[8]['id'], 'sortorder' => 1],
            ['categoryid' => $boxencatid, 'elementid' => $elements[9]['id'], 'sortorder' => 2],
            ['categoryid' => $boxencatid, 'elementid' => $elements[10]['id'], 'sortorder' => 3],
            ['categoryid' => $boxencatid, 'elementid' => $elements[11]['id'], 'sortorder' => 4],
            ['categoryid' => $boxencatid, 'elementid' => $elements[12]['id'], 'sortorder' => 5],
            ['categoryid' => $boxencatid, 'elementid' => $elements[13]['id'], 'sortorder' => 6],
            ['categoryid' => $boxencatid, 'elementid' => $elements[14]['id'], 'sortorder' => 7],
            ['categoryid' => $boxencatid, 'elementid' => $elements[15]['id'], 'sortorder' => 8],
        ];

        foreach ($links as $data) {
            $link = new stdClass();
            $link->categoryid   = $data['categoryid'];
            $link->elementid    = $data['elementid'];
            $link->enabled      = 1;
            $link->sortorder    = $data['sortorder'];
            $link->timecreated  = time();
            $link->timemodified = time();
            $DB->insert_record('tiny_styles_cat_elements', $link);
        }
    }
}
