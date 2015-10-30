Feature: RulerZ can filter a Doctrine query builder

    Scenario: It works with a query builder
        Given RulerZ is configured
        And I use the query builder dataset
        When I filter the dataset with the rule:
            """
            gender = "F" and points > 9000
            """
        Then I should have the following results:
            | pseudo | points |
            | Ada    | 10000  |
