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
 * @package     tiny_styles
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom installation logic
 * 
 */
function xmldb_tiny_styles_install() {
    global $DB;

    // default categories
    if (!$DB->record_exists('tiny_styles_categories', array())) {
        
        // Label
        $cat = new stdClass();
        $cat->name         = 'Labels';
        $cat->description  = 'Design Elements directly in Text';
        $cat->showdesc     = 'helptext';
        $cat->symbol       = 'fa fa-tag';
        $cat->presentation = 'submenu';
        $cat->enabled      = 1;
        $cat->sortorder    = 1;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        $labelcatid = $DB->insert_record('tiny_styles_categories', $cat);

        // Boxes
        $cat = new stdClass();
        $cat->name         = 'Boxes';
        $cat->description  = 'Format paragraphs';
        $cat->showdesc     = 'never';
        $cat->symbol       = 'fa fa-cube';
        $cat->presentation = 'submenu';
        $cat->enabled      = 1;
        $cat->sortorder    = 2;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        $boxencatid = $DB->insert_record('tiny_styles_categories', $cat);

        // Divider
        $cat = new stdClass();
        $cat->name         = '-------';
        $cat->description  = '';
        $cat->showdesc     = 'never';
        $cat->symbol       = '';
        $cat->presentation = 'divider';
        $cat->enabled      = 1;
        $cat->sortorder    = 3;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        $dividercatid = $DB->insert_record('tiny_styles_categories', $cat);

        // "Uni-Vorlagen" category
        $cat = new stdClass();
        $cat->name         = 'Uni-Vorlagen';
        $cat->description  = 'Vorlagen im Corporate Design der Uni';
        $cat->showdesc     = 'tooltip';
        $cat->symbol       = 'fa fa-university';
        $cat->presentation = 'inline';
        $cat->enabled      = 1;
        $cat->sortorder    = 4;
        $cat->timecreated  = time();
        $cat->timemodified = time();
        $unicatid = $DB->insert_record('tiny_styles_categories', $cat);
    }

    // some default elements
    if (!$DB->record_exists('tiny_styles_elements', array())) {
        $elements = [
            [
                'name'       => 'Gray Label',
                'type'       => 'inline',
                'cssclasses' => 'badge badge-secondary',
                'sortorder'  => 1,
            ],
            [
                'name'       => 'Blue Label',
                'type'       => 'inline',
                'cssclasses' => 'badge badge-primary',
                'sortorder'  => 2,
            ],
            [
                'name'       => 'Green Label',
                'type'       => 'inline',
                'cssclasses' => 'badge badge-success',
                'sortorder'  => 3,
            ],
            [
                'name'       => 'Yellow Label',
                'type'       => 'inline',
                'cssclasses' => 'badge badge-warning',
                'sortorder'  => 4,
            ],
            [
                'name'       => 'Red Label',
                'type'       => 'inline',
                'cssclasses' => 'badge badge-danger',
                'sortorder'  => 5,
            ],
            [
                'name'       => 'Info Box',
                'type'       => 'inline',
                'cssclasses' => 'alert alert-info',
                'sortorder'  => 1,
            ],[
                'name'       => 'Warning Box',
                'type'       => 'inline',
                'cssclasses' => 'alert alert-warning',
                'sortorder'  => 2,
            ],[
                'name'       => 'Danger Box',
                'type'       => 'inline',
                'cssclasses' => 'alert alert-danger',
                'sortorder'  => 3,
            ],[
                'name'       => 'Success Box',
                'type'       => 'inline',
                'cssclasses' => 'alert alert-success',
                'sortorder'  => 4,
            ],[
                'name'       => 'Dark Box',
                'type'       => 'inline',
                'cssclasses' => 'alert alert-dark',
                'sortorder'  => 5,
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
            $elem->timemodified= time();
            $elements[$key]['id'] = $DB->insert_record('tiny_styles_elements', $elem);
        }
    }

    // bridging table ->tiny_styles_cat_elements in the db
    if (!$DB->record_exists('tiny_styles_cat_elements', array())) {
        // elements to categories:
        // one category can have many elements and one element can have many parent categories
        $links = [
            ['categoryid' => $labelcatid, 'elementid' => $elements[0]['id'], 'sortorder' => 1],
            ['categoryid' => $labelcatid, 'elementid' => $elements[1]['id'], 'sortorder' => 2],
            ['categoryid' => $labelcatid, 'elementid' => $elements[2]['id'], 'sortorder' => 3],
            ['categoryid' => $labelcatid, 'elementid' => $elements[3]['id'], 'sortorder' => 4],
            ['categoryid' => $labelcatid, 'elementid' => $elements[4]['id'], 'sortorder' => 5],
            ['categoryid' => $boxencatid, 'elementid' => $elements[5]['id'], 'sortorder' => 1],
            ['categoryid' => $boxencatid, 'elementid' => $elements[6]['id'], 'sortorder' => 2],
            ['categoryid' => $boxencatid, 'elementid' => $elements[7]['id'], 'sortorder' => 3],
            ['categoryid' => $boxencatid, 'elementid' => $elements[8]['id'], 'sortorder' => 4],
            ['categoryid' => $boxencatid, 'elementid' => $elements[9]['id'], 'sortorder' => 5]
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
