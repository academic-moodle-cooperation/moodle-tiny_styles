CHANGELOG
=========

5.2.0 (2026-08-06)
-----------------
* Moodle 5.2 compatible version
* [FIXED] #120 Fix icons are not always colored to match the Moodle detail color [github #27]
* [FIXED] #121 Fix default colors do not match during editing and viewing [github #26]
* [FIXED] #124 Remove expired function print_error
* [FIXED] #157 Fix categories visibility by role not functioning correctly
* [FIXED] #158 Fix moving elements from one category to another not being possible
* [FIXED] #160 Fix special characters not displayed correctly in category and element names
* [FEATURE] #27 Menu/toolbar: show description on category entry
* [FEATURE] #31 Menu/toolbar: preview of the element entry [github #12]
* [FEATURE] #62 Characters can be formatted as label style without highlighting [github #2]
* [FEATURE] #106 Enable selection of further Font Awesome symbols for categories
* [FEATURE] #132 Change layout of category settings
* [FEATURE] #133 Add restriction settings to category settings
* [FEATURE] #134 Restrict category visibility based on status or role
* [FEATURE] #135 Change layout of element settings
* [FEATURE] #136 Add restriction settings to element settings
* [FEATURE] #137 Restrict element visibility based on status or role
* [FEATURE] #138 Restrict element visibility based on category settings
* [FEATURE] #139 Add general availability setting to category settings
* [FEATURE] #140 Add general availability setting to element settings
* [FEATURE] #143 Update import instructions in plugin settings
* [FEATURE] #144 Update example JSON file
* [FEATURE] #145 Update available settings in export files
* [FEATURE] #151 Add apply style via preview window

5.1.1 (2026-02-06)
-----------------
* [FEATURE] #117 Removed German langstrings from the repository (now located in AMOS).
* [FEATURE] #118 Updated readme to include information on creating categories and elements, importing and exporting, and the support of multilang filter v2.

5.1.0 (2026-01-22)
-----------------
* Moodle 5.1 compatible version
* [FIXED] #111 Fix list custom block styling not updating
* [FIXED] #112 Fix extra blank space after adding inline style
* [FIXED] #115 Fix removal of default bootstrap class
* [FEATURE] #20 Add support for multilang filter v2
* [FEATURE] #57 Update import instructions
* [FEATURE] #61 Implement simple way to create newline within box style [github #8]
* [FEATURE] #64 Add ability to update styling on a styled text without highlighting [github #4]
* [FEATURE] #66 Implement way to create newline after box style [github #9]
* [FEATURE] #79 Add export & import templates to include information on category order and symbol
* [FEATURE] #90 Update ajax implementation to external services [github #24]
* [FEATURE] #92 Add transition to templates [github #20]
* [FEATURE] #96 Add export template to include information on element order and visibility
* [FEATURE] #105 Add structure in the dropdown for manual styles and bootstrap classes (elements)
* [FEATURE] #109 Add unique ids for custom styles
* [FEATURE] #110 Update import instructions

5.0.2 (2025-10-16)
-----------------
* [FIXED] #84 Fix editor list styling behavior [github #14]
* [FIXED] #85 Fix a missing language string [github #23]
* [FIXED] #86 Fix hard-coded language strings [github #18]
* [FIXED] #87 Fix PHP doc tags [github #19]
* [FIXED] #88 Fix undefined constant [github #21]
* [FIXED] #89 Fix header information [github #22]
* [FEATURE] #55 Plugin added into Tiny plugins list
* [FEATURE] #74 Plugin permission available at course level
* [FEATURE] #75 Plugin permission available at activity level
* [FEATURE] #76 Implement language strings for Remove styles-button
* [FEATURE] #77 Reset checkboxes after bulk actions
* [FEATURE] #78 Elements page heading adjusted
* [FEATURE] #81 Adjusted default order of preloaded items [github #15]
* [FEATURE] #91 Error log removal confirmed [github #25]

5.0.1 (2025-08-27)
------------------
* [FIXED] #83 Fix Problems with Boost Union [github #16]

5.0.0 (2025-08-06)
------------------
* Moodle 5.0 compatible version
* [FIXED] #58 Fix broken button styles on "Add element" page [github #6]
* [FIXED] #63 Fix missing bootstrap styling for select widget [github #5]
* [FIXED] #67 Fix formatting multiple paragraps at once [github #10]
* [FIXED] #69 Fix error message about missing standard rights
* [FIXED] #71 Fix error message when cancelling import
* [FEATURE] #51 Icon selection works with a search bar
* [FEATURE] #59 Paragraph can be formatted as box without highlighting it [github #1]
* [FEATURE] #65 Applied styles can be easily removed [github #3]
* [FEATURE] #68 New more detailed instructions for inline css [github #7]
* [FEATURE] #72 Database naming changed from presentation to menumode
