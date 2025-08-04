# Instructions for the import form fillout (example.json)

### Structure
The example.json is structured into a category array, where each category contains an element array. 

This is the format which any json file imported should follow.

Visualized:
```
Categories: [ category_1, category_2 ... category_n ]

category_1: [ element_a, element_b ... element_n ]
category_2: [ element_x, element_y ...
-> with the elements carrying the styling information
```

### How to use the json
The example json can be easily used for editing it directly, and expanded by copying it.
> The user should follow a correct json syntax and formatting for the file to work expectedly.


#### How to fillout the *example.json*:

*(see below for further explanations of enabled, type, etc.)*
```
"categories": [
    {
    "name": "Enter a minimum 3 characters long name here.",
    "description": "Write a short category description here.",
    "showdesc": "pick one of the following: helptext/tooltip/never",
    "menumode": "pick one of the following: submenu/inline/divider",
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
    },
    ... possible to add more categories ...
```

### Explanations

The naming and description should be self-explanatory.

**The other fields are:**

Category:
- showdesc: how the description is shown for the users, or if at all
- menumode: how the elements are displayed in the editor. Under a submenu, or directly in the menu. 
  - *Or is this category a divider (no elements should be added)*
- enabled: either enabled: 1 or disabled: 0, suggested default disabled => 0.
----
Element:
- type: inline or block
    - *inline is meant mainly for styling short lines of texts and words.*
    - *block styling for paragraphs and longer snippets of text.*
- cssclasses: css styling for the text
    - *this can be either a valid bootstrap styling*
    - *or a valid css inline code like: color: red; font-weight: bold;*
- enabled: same rules as for category, suggested value disabled => 0
- custom: if the styling is a css inline code this should be marked as enabled => 1

----

### Good to know
- It is possible to import duplicate categories multiple times, this is allowed to avoid accidental deletions or unwanted edits
of the existing categories and elements. 
> Therefore, it is suggested that only the example json is used for creating imports,
to avoid any accidental duplicate categories from showing up.

- All of these fields can be edited/changed later from the Moodle admin pages.

- Currently, any icon selection needs to be done from the Moodle admin pages by editing the category.

- Further examples and usage can be examined from the entire exported json.

