Feature: Custom operators can be used to filter data

    Background:
        Given RulerZ is configured
        And I use the default dataset
        And I use the default execution context

    Scenario: Embeddable are supported
        When I filter the dataset with the rule:
            """
            address.city = 'Paoli'
            """
        Then I should have the following results:
            | pseudo   |
            | Margaret |

    Scenario: Positional parameters are supported
        Given I define the parameters:
            | Paoli |
        When I filter the dataset with the rule:
            """
            address.city = ?
            """
        Then I should have the following results:
            | pseudo   |
            | Margaret |