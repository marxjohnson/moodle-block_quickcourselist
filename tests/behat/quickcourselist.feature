@block @block_quickcourselist 
Feature:
    As an admin
    In order to navigate to course pages quickly
    I need to be able to search for courses by name

    Background:
    Given the following "categories" exist:
        | name | category | idnumber |
        | Cat1 | 0        | CAT1     |
        | Cat2 | 0        | CAT2     |
        | Cat3 | CAT1     | CAT3     |
    And the following "courses" exist:
        | fullname         | shortname | format | category |
        | Course1          | C1        | topics | CAT1     |
        | Course1a         | C1a       | topics | CAT1     |
        | Course2          | C2        | topics | CAT2     |
        | Course3          | C3        | topics | CAT2     |
        | Course Maths 101 | Maths101  | topics | CAT3     |
    And I log in as "admin"
    And I follow "Turn editing on"
    And I add the "Quick Course List" block
    And I configure the "Quick Course List" block
    And I set the field "Page contexts" to "Display throughout the entire site"
    And I press "Save changes"
    And I log out

    @block_quickcourselist_default
    Scenario: Search with default settings
    Given I log in as "admin"
    When I set the field "quickcourselistsearch" to "Course"
    And I press "quickcoursesubmit"
    Then I should see "C1: Course1" in the "block_quickcourselist" "block"
    And I should see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should see "C2: Course2" in the "block_quickcourselist" "block"
    And I should see "C3: Course3" in the "block_quickcourselist" "block"
    And I should see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    When I set the field "quickcourselistsearch" to "Course1"
    And I press "quickcoursesubmit"
    Then I should see "C1: Course1" in the "block_quickcourselist" "block"
    And I should see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should not see "C2: Course2" in the "block_quickcourselist" "block"
    And I should not see "C3: Course3" in the "block_quickcourselist" "block"
    And I should not see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    When I set the field "quickcourselistsearch" to "1a"
    And I press "quickcoursesubmit"
    Then I should not see "C1: Course1" in the "block_quickcourselist" "block"
    And I should see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should not see "C2: Course2" in the "block_quickcourselist" "block"
    And I should not see "C3: Course3" in the "block_quickcourselist" "block"
    And I should not see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    When I set the field "quickcourselistsearch" to "Maths101"
    And I press "quickcoursesubmit"
    Then I should not see "C1: Course1" in the "block_quickcourselist" "block"
    And I should not see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should not see "C2: Course2" in the "block_quickcourselist" "block"
    And I should not see "C3: Course3" in the "block_quickcourselist" "block"
    And I should see "Maths101" in the "block_quickcourselist" "block"

    @block_quickcourselist_restrictcontext
    Scenario: Search with category restriction enabled
    Given I log in as "admin"
    And the following config values are set as admin:
        | restrictcontext | 1 | block_quickcourselist |
    And I am on homepage

    When I set the field "quickcourselistsearch" to "Course"
    And I press "quickcoursesubmit"
    Then I should see "C1: Course1" in the "block_quickcourselist" "block"
    And I should see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should see "C2: Course2" in the "block_quickcourselist" "block"
    And I should see "C3: Course3" in the "block_quickcourselist" "block"
    And I should see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    Given I follow "Courses"
    And I follow "Cat1"
    When I set the field "quickcourselistsearch" to "Course"
    And I press "quickcoursesubmit"
    Then I should see "C1: Course1" in the "block_quickcourselist" "block"
    And I should see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should not see "C2: Course2" in the "block_quickcourselist" "block"
    And I should not see "C3: Course3" in the "block_quickcourselist" "block"
    And I should not see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    Given I set the field "categoryid" to "Cat2"
    And I press "Go"
    When I set the field "quickcourselistsearch" to "Course"
    And I press "quickcoursesubmit"
    Then I should not see "C1: Course1" in the "block_quickcourselist" "block"
    And I should not see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should see "C2: Course2" in the "block_quickcourselist" "block"
    And I should see "C3: Course3" in the "block_quickcourselist" "block"
    And I should not see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    Given I set the field "categoryid" to "Cat3"
    And I press "Go"
    When I set the field "quickcourselistsearch" to "Course"
    And I press "quickcoursesubmit"
    Then I should not see "C1: Course1" in the "block_quickcourselist" "block"
    And I should not see "C1a: Course1a" in the "block_quickcourselist" "block"
    And I should not see "C2: Course2" in the "block_quickcourselist" "block"
    And I should not see "C3: Course3" in the "block_quickcourselist" "block"
    And I should see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

    @block_quickcourselist_displaymode
    Scenario: Search with different displaymodes
    Given I log in as "admin"
    And the following config values are set as admin:
        | displaymode | 1 | block_quickcourselist |
    And I am on homepage
    When I set the field "quickcourselistsearch" to "C"
    And I press "quickcoursesubmit"
    Then I should see "C1" in the "block_quickcourselist" "block"
    And I should not see "Course1" in the "block_quickcourselist" "block"

    Given the following config values are set as admin:
        | displaymode | 2 | block_quickcourselist |
    And I am on homepage
    When I set the field "quickcourselistsearch" to "C"
    And I press "quickcoursesubmit"
    Then I should see "Course1" in the "block_quickcourselist" "block"
    And I should not see "C1" in the "block_quickcourselist" "block"

    Given the following config values are set as admin:
        | displaymode | 3 | block_quickcourselist |
    And I am on homepage
    When I set the field "quickcourselistsearch" to "C"
    And I press "quickcoursesubmit"
    Then I should see "C1: Course1" in the "block_quickcourselist" "block"

    @block_quickcourselist_splitterms
    Scenario: Search with split terms enabled
    Given I log in as "admin"
    And the following config values are set as admin:
        | splitterms | Course short name | block_quickcourselist |
    And I am on homepage
    When I set the field "quickcourselistsearch" to "Course 101"
    And I press "quickcoursesubmit"
    And I should see "Maths101: Course Maths 101" in the "block_quickcourselist" "block"

