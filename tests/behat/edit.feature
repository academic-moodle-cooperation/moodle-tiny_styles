@editor @editor_tiny @tiny_styles @tiny
Feature: Admin can edit categories and elements
  In order to customize the plugin
  As an admin
  I should be able to edit existing categories and elements

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > Styles" in site administration

  @javascript
  Scenario: Admin edits a category
    When I click on "Edit category" "link" in the "Labels" "table_row"
    Then I should see "Edit category"
    And I set the field "Name" to "Edited Category"
    And I press "Save changes"
    Then I should see "Edited Category"
    And I should not see "Labels"

  @javascript
  Scenario: Admin edits an element from a category
    When I click on "Edit elements" "link" in the "Labels" "table_row"
    Then I should see "Blue Label"
    When I click on "Edit" "link" in the "Blue Label" "table_row"
    Then I should see "Edit element"
    And I set the field "Name" to "Edited Label"
    And I set the field "CSS style / Bootstrap class" to "badge bg-info text-dark"
    And I press "Save changes"
    Then I should see "Edited Label"
    And I should see "badge bg-info text-dark"
    And I should not see "Blue Label"
    And I should not see "badge bg-primary text-white"
