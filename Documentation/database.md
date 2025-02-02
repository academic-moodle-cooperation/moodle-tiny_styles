Table: tiny_styles_categories
pgsql
Copy
Edit
+----------------+-------------------------+---------------------------------------------------------+
| Field          | Type                    | Description                                             |
+----------------+-------------------------+---------------------------------------------------------+
| id             | int(10) PK AI           | Primary key, auto-increment                             |
| name           | char(30) NOT NULL       | Category name (e.g. “Labels,” “Boxen,” etc.)            |
| description    | text                    | Longer descriptive text for the category                |
| showdesc       | char(10) NOT NULL       | How to display description: “never,” “helptext,” etc.   |
| symbol         | char(30)                | Optional icon (e.g. “fa fa-bolt”)                       |
| presentation   | char(10) NOT NULL       | “submenu,” “inline,” or “divider”                       |
| enabled        | int(1) NOT NULL         | 1 = category is active, 0 = hidden                      |
| sortorder      | int(10) NOT NULL        | Ordering priority                                       |
| timecreated    | int(10) NOT NULL        | Unix timestamp for creation                             |
| timemodified   | int(10) NOT NULL        | Unix timestamp for last modification                    |
+----------------+-------------------------+---------------------------------------------------------+
Table: tiny_styles_elements
pgsql
Copy
Edit
+----------------+-------------------------+-------------------------------------------------------------------+
| Field          | Type                    | Description                                                       |
+----------------+-------------------------+-------------------------------------------------------------------+
| id             | int(10) PK AI           | Primary key, auto-increment                                       |
| name           | char(30) NOT NULL       | Element’s display name (e.g. “Label grey,” “Alert info,” etc.)    |
| type           | char(20) NOT NULL       | “inline,” “block,” or other classification                        |
| cssclasses     | char(255) NOT NULL      | CSS classes (e.g. “badge badge-success”)                          |
| enabled        | int(1) NOT NULL         | 1 = globally active; 0 = globally hidden                          |
| sortorder      | int(10) NOT NULL        | Ordering priority (global fallback)                               |
| timecreated    | int(10) NOT NULL        | Unix timestamp for creation                                       |
| timemodified   | int(10) NOT NULL        | Unix timestamp for last modification                              |
+----------------+-------------------------+-------------------------------------------------------------------+
Table: tiny_styles_cat_elements (Bridging Many-to-Many)
sql
Copy
Edit
+----------------+-------------------------+--------------------------------------------------------------+
| Field          | Type                    | Description                                                  |
+----------------+-------------------------+--------------------------------------------------------------+
| id             | int(10) PK AI           | Primary key, auto-increment                                  |
| categoryid     | int(10) NOT NULL        | Foreign key → tiny_styles_categories.id                      |
| elementid      | int(10) NOT NULL        | Foreign key → tiny_styles_elements.id                        |
| enabled        | int(1) NOT NULL         | 1 = element active in this category, 0 = hidden in this cat  |
| sortorder      | int(10) NOT NULL        | Ordering of element within this category                     |
| timecreated    | int(10) NOT NULL        | Unix timestamp for creation of this link                     |
| timemodified   | int(10) NOT NULL        | Unix timestamp for last modification    