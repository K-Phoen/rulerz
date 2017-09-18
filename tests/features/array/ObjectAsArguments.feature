Feature: Operators can receive objects as arguments

    Scenario: Operators can receive objects as arguments
        Given RulerZ is configured
        And I use the array of arrays dataset
        When I filter the dataset with the rule:
            """
            is_leap_year(birthday)
            """
        Then I should have the following results:
            | pseudo   |
            | Joe      |
            | Margaret |
