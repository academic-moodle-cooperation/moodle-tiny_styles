@editor @editor_tiny @tiny_styles
Feature: Admin can create new categories and elements
  In order to customize the plugin
  As an admin
  I should be able to create new categories and elements

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > Styles" in site administration
    When I click on "Create category" "link"
    And I set the field "Name" to "Category 1"
    And I set the field "Description" to "A description for category."
    And I press "Save changes"
  
  @javascript
  Scenario: Admin fills the new category with predefined elements
    Given I should see "Categories"
    And I should see "Category 1"
    And I should see "A description for category."
    When I click on "Edit elements" "link" in the "Category 1" "table_row"
    And I click on "Add element" "link"
    And I set the field "Name" to "Label element"
    And I set the field "CSS style / Bootstrap class" to "badge bg-primary text-white"
    And I press "Save changes"
    Then I should see "Label element"
    When I click on "Add element" "link"
    And I set the field "Name" to "Box element"
    And I set the field "CSS style / Bootstrap class" to "alert alert-primary"
    And I press "Save changes"
    Then I should see "Box element"

  @javascript
  Scenario: Admin fills the new category with custom elements
    Given I should see "Categories"
    And I should see "Category 1"
    When I click on "Edit elements" "link" in the "Category 1" "table_row"
    And I click on "Add element" "link"
    And I set the field "Name" to "Custom Label element"
    And I set the field "CSS style / Bootstrap class" to "Inline CSS style"
    And I set the field "Manual style" to "color: red; font-weight: bold;"
    And I press "Save changes"
    Then I should see "Custom Label element"
    When I click on "Add element" "link"
    And I set the field "Name" to "Custom Box element"
    And I set the field "CSS style / Bootstrap class" to "Inline CSS style"
    And I set the field "Display type" to "Block"
    And I set the field "Manual style" to "color: blue; font-weight: bold;"
    And I press "Save changes"
    Then I should see "Custom Box element"
