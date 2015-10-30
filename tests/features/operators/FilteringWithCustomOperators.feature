Feature: Custom operators can be used to filter data

    Background:
        Given RulerZ is configured
        And I use the default dataset
        And I use the default execution context

    Scenario: Custom operators can be used
        When I filter the dataset with the rule:
            """
            length(pseudo) = 3
            """
        Then I should have the following results:
            | pseudo |
            | Ada    |
            | Joe    |
            | Bob    |
