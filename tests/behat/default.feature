@editor @editor_tiny @tiny_styles
Feature: The plugin is active and visible in the administration pages
  In order to use the plugin
  As an admin
  I need to be able to view the plugin in the site administration

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > Styles" in site administration

  @javascript
  Scenario: Admin is able to view the tiny_styles settings and the pre-defined categories
    Then I should see "TinyMCE Styles"
    And I should see "Create category"
    And I should see "Import styles"
    And I should see "Export styles"
    And I should see "Labels"
    And I should see "Boxes"

  @javascript
  Scenario: Admin is able to view the predefined Label elements
    When I click on "Edit elements" "link" in the "Labels" "table_row"
    Then I should see "Elements"
    And I should see "Labels"
    And I should see "Blue Label"
    And I should see "Info Label"
    And I should see "Green Label"
    And I should see "Yellow Label"
    And I should see "Red Label"
    And I should see "Dark Label"
    And I should see "Gray Label"
    And I should see "Light Label"

  @javascript
  Scenario: Admin is able to view the predefined Box elements
    When I click on "Edit elements" "link" in the "Boxes" "table_row"
    Then I should see "Elements"
    And I should see "Boxes"
    And I should see "Blue Box"
    And I should see "Info Box"
    And I should see "Green Box"
    And I should see "Yellow Box"
    And I should see "Red Box"
    And I should see "Dark Box"
    And I should see "Grey Box"
    And I should see "Light Box"
