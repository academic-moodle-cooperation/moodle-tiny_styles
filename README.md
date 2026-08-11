TinyMCE Styles
==============================================

This file is part of the tiny_styles plugin for Moodle - <http://moodle.org/>

*Author:*    Karri Pajarinen

*Copyright:* [Academic Moodle Cooperation](http://www.academic-moodle-cooperation.org)

*License:*   [GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html)


Description
-----------

The purpose of this TinyMCE plugin is to let users apply predefined bootstrap classes or custom CSS classes to block or inline text.

Default styles are:
- Boxes (bootstrap class: 'alert')
- Labels (bootstrap class: 'badge')


Usage
-------

A teacher wants to organise a course more clearly. For this purpose, they format the content in a standardised way by
selecting a text in the TinyMCE view and choosing a suitable style out of the predefined list.
For example, they mark additional notes on a topic with the style 'Blue box'.


## Export and Import

### Exporting Styles

To backup or share your style configuration:

1. Go to **Site administration > Plugins > Text editors > TinyMCE editor > Styles**
2. Click the **Export** button
3. A JSON file (`tiny_styles_export.json`) will be downloaded containing all categories and elements (visible and hidden)

### Importing Styles

To restore or import a style configuration:

1. Go to **Site administration > Plugins > Text editors > TinyMCE editor > Styles**
2. Click the **Import** button
3. Upload a valid JSON file with the required file structure (see "JSON Format" below)
4. The import will add the categories and elements from the file

**Please note:**
* Importing does not delete or overwrite existing styles; it adds the imported styles alongside them.
* If a category is imported with an already existing category name in the category overview, it will be added unchanged, i.e. there will be two categories with the same name.
* There is a **template JSON file** available for download in the import area (`example.json`) which is in the required structure for importing styles.
* The `symbol` field uses a Font Awesome 6 icon name (for example `book` or `star`). All fontawesome icons can be used, and should be entered with no prefix and no `.svg` ending, as in earlier releases. Icon names can be browsed at [fontawesome.com](https://fontawesome.com/search?ic=free-collection).


### JSON Format

The import/export file uses this structure:

```json
{
  "categories": [
    {
      "name": "My Category",
      "description": "Optional description",
      "symbol": "book",
      "showdesc": "never",
      "menumode": "submenu",
      "availability": 1,
      "visibility_admin": "all",
      "visibility_roles": [],
      "elements": [
        {
          "name": "Red Box",
          "custom": 0,
          "type": "block",
          "cssclasses": "alert alert-danger",
          "availability": 1,
          "visibility_admin": "all",
          "visibility_roles": []
        }
      ]
    }
  ]
}
```

**What the fields mean:**
* `symbol`: a Font Awesome 6 icon name for the category. Leave empty for no icon.
* `showdesc`: how the category description is shown in the editor: `never`, `helptext` (a **?** button), or `tooltip` (shown on hover).
* `availability`: `1` shows the category or element in the TinyMCE editor, `0` hides it. Default: `1`.
* `visibility_admin`: who can see the category or element, based on admin status: `all`, `admins_only`, or `non_admins`. Default: `all`.
* `visibility_roles`: a list of Moodle role IDs that can see the category or element, e.g. `[3, 5]`. An empty list means all roles can see it.

## Multilanguage Support (Multilang Filter v2)

Category names, descriptions, and element names support the Multilang filter v2 syntax for multilingual content.

### Requirements

The [Multi-Language Content (v2)](https://moodle.org/plugins/filter_multilang2) plugin must be installed and enabled on your Moodle site.

### Usage

Use the `{mlang}` syntax in text fields:

```
{mlang en}Blue Label{mlang}{mlang de}Blaues Etikett{mlang}{mlang fr}Etiquette bleue{mlang}
```

This displays:

- "Blue Label" for English users
- "Blaues Etikett" for German users
- "Etiquette bleue" for French users

### Example in JSON Import

```json
{
  "name": "{mlang en}Warning Box{mlang}{mlang de}Warnhinweis{mlang}",
  "description": "{mlang en}Use for important warnings{mlang}{mlang de}Fur wichtige Warnungen verwenden{mlang}"
}
```


Changing Applied Style Elements
------------------------------

### Bootstrap Classes

Any changes (edit, hide, delete) to a bootstrap class style will have no effect on any text that already had the style applied to it.

### Custom CSS Classes

Any changes (edit, hide, delete) to a custom CSS class style will have no effect on any text that already had the style applied to it **until the text is newly saved**. Only then will the change take effect:

* **Style was edited:** The text will be displayed styled with the edited custom CSS class.
* **Style was hidden or deleted:** The text will no longer be displayed styled with that custom CSS class.


Installation
------------

* Copy the module code directly to the lib/editor/tiny/plugins/styles directory.

* Log into Moodle as administrator.

* Open the administration area (http://your-moodle-site/admin) to start the installation
  automatically.


Privacy API
-------------

The plugin fully implements the Moodle Privacy API.


Documentation
-------------

You can find a documentation for the plugin on the [AMC website](https://academic-moodle-cooperation.org/tiny_styles/)


Bug Reports / Support
---------------------

We try our best to deliver bug-free plugins, but we can not test the plugin for every platform,
database, PHP and Moodle version. If you find any bug please report it on
[GitHub](https://github.com/academic-moodle-cooperation/moodle-tiny_styles/issues). Please
provide a detailed bug description, including the plugin and Moodle version and, if applicable, a
screenshot.

You may also file a request for enhancement on GitHub. If we consider the request generally useful
and if it can be implemented with reasonable effort we might implement it in a future version.

You may also post general questions on the plugin on GitHub, but note that we do not have the
resources to provide detailed support.


License
-------

This plugin is free software: you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

The plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License with Moodle. If not, see
<http://www.gnu.org/licenses/>.


Good luck and have fun!
