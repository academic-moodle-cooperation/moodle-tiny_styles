@editor @editor_tiny @tiny_styles @tiny
Feature: Elements are visible in the TinyMCE editor
  In order to apply styles to content
  As an admin
  I should be able to see configured categories and elements in the editor

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > Styles" in site administration
    When I click on "Create category" "link"
    And I set the field "Name" to "Category 1"
    And I set the field "Description" to "A description for category."
    And I press "Save changes"
    And I click on "Edit elements" "link" in the "Category 1" "table_row"
    And I click on "Add element" "link"
    And I set the field "Name" to "Label element"
    And I set the field "CSS style / Bootstrap class" to "badge bg-primary text-white"
    And I press "Save changes"

  @javascript
  Scenario: Admin can see category and element in the TinyMCE styles menu
    Given I open my profile in edit mode
    When I click on the "Styles" button for the "Description" TinyMCE editor
    Then I should see "Category 1" in the ".tsm-panel" "css_element"
    Then I should see "Labels" in the ".tsm-panel" "css_element"
    Then I should see "Boxes" in the ".tsm-panel" "css_element"
    When I click on "//div[contains(@class,'tsm-panel')]//span[contains(@class,'tsm-label') and text()='Category 1']" "xpath_element"
    Then I should see "Label element" in the ".tsm-submenu--open" "css_element"
