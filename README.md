# TinyMCE-Gestaltungsvorlagen - Tiny Styles 

#### A plugin for the TinyMCE text editor to enable bootstrap styling.

Gives **User** the ability to select text in the editor and add a bootstrap style of their choosing from the format tab menu, or alternatively from the toolbar menu *(FA Icon)*. 

Default style options are
- Boxes (bootstrap class: 'alert')
- Labels (bootstrap class: 'badge')

Admin/Settings site enables the creation for new categories and style options, and the ability to edit existing categories and styles.
- Currently the icon selection feature is not enabled. The program will add a default icon for any created category.  

## Current version ##

### Database ###


**Three tables**

Categories, Elements, and a bridging table for category elements

For a detailed structure see: 
[Database details](Documentation/database.md)

***Possible expansion:** A new table for svg icons for user to select from and the tinyMCE editor to use.*

-----

### Admin pages ###

**Fully implemented for usage:**

- Settings page (see section: [Suggested improvements](#suggested-improvements))
- Create/Edit element (see section: [Suggested improvements](#suggested-improvements))

**Functional, but missing features:**

- Create/Edit category

   **Missing features:**
  - Icon selection (category)

------
### Editor ###

Functional, but requires changes
 
Currently user has to clear formatting manually from the menu, before applying a new style due to the text selection logic of TinyMCE

***Possible fix:*** 
- Taking steps up in the text selection to expand 'view area' for selected text, then removing styling. 
- Alternatively a solution could be modifying the TinyMCE built-in method:
    
   ```
   editor.Formatter()
   ```    
---
### Next planned updates: ###

**Admin:**
- Icon selection for category

**Editor:**
- Text wrapping logic improvements
- Dynamically loaded icons
- Preview for styles

**General improvements:**
- Refactoring
- Code optimization

-----

### Suggested improvements ###

**Create/Edit element**
- Button layout for a pleasing look and usability

## Default installing options

Make sure to set up the plugin under: 

```
    {your/moodle/dirroot}/lib/editor/tiny/plugins/styles
```


#### Installing via uploaded ZIP file ####

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/lib/editor/tiny/plugins/styles

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

Creator: 2025 Karri Pajarinen <pajarinenk66@univie.ac.at>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
