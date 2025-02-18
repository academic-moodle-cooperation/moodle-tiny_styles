# TinyMCE-Gestaltungsvorlagen #

#### Tiny styles plugin to enable css bootstrap styling for the TinyMCE text editor.


## Current version ##


### Database ###


**Three tables**

Categories, Elements, and a bridging table for category elements

For a detailed structure see: 
[Database details](Documentation/database.md)

**Next:** A new table for svg icons for user to select from and the tinyMCE editor to use. 

-----

### Admin pages ###

**Fully implemented:**

- none

**Functional, but missing features:**

- Settings page
- Create category 
- View category elements
- Edit category
- Create element
- Edit element

**Missing features from admin**

- Up/Down buttons
- Delete button
- Styles dropdown menu
- Icon selection (category)
- View style preview
- Visible / Hidden

- *all admin pages need to be changed to admin settings context*

~~**To be implemented:**~~

------
### Editor ###

Functional, requires changes:
 
- missing icons

***Note:*** currently user has to clear formatting before applying a new style due to the text selection logic of TinyMCE

***Fix suggestion (for later):*** manually take steps up in the text selection to expand 'view area' for selected text, then remove styling

-----


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

2024 Karri Pajarinen <pajarinenk66@univie.ac.at>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
