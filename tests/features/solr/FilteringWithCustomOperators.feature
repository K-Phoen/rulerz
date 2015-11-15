Feature: Custom operators can be used to filter data

    Background:
        Given RulerZ is configured
        And I use the default dataset
        And I use the default execution context

    Scenario: Custom operators can be used
        When I filter the dataset with the rule:
            """
            boost(gender = "F", 3) OR points > 9000
            """
        Then I should have the following results:
            | pseudo   |
            | Ada      |
            | Margaret |
            | Alice    |
            | Bob      |
