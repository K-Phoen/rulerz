Feature: RulerZ can filter an array

    Scenario: It works with an array of arrays
        Given RulerZ is configured
        And I use the array of arrays dataset
        When I filter the dataset with the rule:
            """
            gender = "F" and points > 9000
            """
        Then I should have the following results:
            | pseudo | points |
            | Ada    | 10000  |

    Scenario: It works with an array of objects
        Given RulerZ is configured
        And I use the array of objects dataset
        When I filter the dataset with the rule:
            """
            gender = "F" and points > 9000
            """
        Then I should have the following results:
            | pseudo | points |
            | Ada    | 10000  |
