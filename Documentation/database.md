# Database details #

### Table: `tiny_styles_categories`
---

| Field        | Type               | Description                                      |
|-------------|------------------|--------------------------------------------------|
| `id`         | `int(10) PK AI`   | Primary key, auto-increment.                     |
| `name`       | `char(30) NOT NULL` | Category’s display name (e.g., "Labels", "Boxen"). |
| `description` | `text`             | Longer descriptive text for the category.       |
| `showdesc`   | `char(10) NOT NULL` | How to display the category description (e.g., "never", "tooltip"). |
| `symbol`     | `char(30) NULL`    | Optional icon reference (e.g., "fa fa-bolt").   |
| `presentation` | `char(10) NOT NULL` | "submenu", "inline", or "divider".              |
| `enabled`    | `int(1) NOT NULL`  | `1` = category is active, `0` = disabled.       |
| `sortorder`  | `int(10) NOT NULL` | Used for ordering categories in the UI.        |
| `timecreated` | `int(10) NOT NULL` | Unix timestamp for when the category was created. |
| `timemodified` | `int(10) NOT NULL` | Unix timestamp for last modification.          |

---

### Table: `tiny_styles_elements`
---
| Field        | Type               | Description                                      |
|-------------|------------------|--------------------------------------------------|
| `id`         | `int(10) PK AI`   | Primary key, auto-increment.                     |
| `name`       | `char(30) NOT NULL` | Element’s display name (e.g., "Label grey", "Alert info"). |
| `type`       | `char(20) NOT NULL` | Defines if it’s "inline", "block", etc.        |
| `cssclasses` | `char(255) NOT NULL` | The CSS classes to apply (e.g., "badge badge-success"). |
| `enabled`    | `int(1) NOT NULL`  | `1` = globally active; `0` = globally hidden.   |
| `custom`    | `int(1) NOT NULL`  | `1` =  custom class; `0` = bootstrap class   |
| `sortorder`  | `int(10) NOT NULL` | Default sort order or fallback.                 |
| `timecreated` | `int(10) NOT NULL` | Creation timestamp.                            |
| `timemodified` | `int(10) NOT NULL` | Last update timestamp.                         |

---

### Table: `tiny_styles_cat_elements` (Bridging Many-to-Many)
---
| Field        | Type               | Description                                      |
|-------------|------------------|--------------------------------------------------|
| `id`         | `int(10) PK AI`   | Primary key, auto-increment.                     |
| `categoryid` | `int(10) NOT NULL` | References `tiny_styles_categories.id`.         |
| `elementid`  | `int(10) NOT NULL` | References `tiny_styles_elements.id`.           |
| `enabled`    | `int(1) NOT NULL`  | `1` = element is active in this category, `0` = hidden. |
| `sortorder`  | `int(10) NOT NULL` | Category-specific ordering of this element.     |
| `timecreated` | `int(10) NOT NULL` | Timestamp when the link was created.           |
| `timemodified` | `int(10) NOT NULL` | Timestamp for last modification.               |

---