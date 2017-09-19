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

    Scenario: It works with nested objects
        Given RulerZ is configured
        And I use the array of objects dataset
        When I filter the dataset with the rule:
            """
            group.name = "Estasia"
            """
        Then I should have the following results:
            | pseudo   |
            | Joe      |
            | Margaret |


    Scenario: It works with birth date bigger than an other one
        Given RulerZ is configured
        And I use the array of objects dataset
        When I define a birthDate parameter to "2005-01-04"
        When I filter the dataset with the rule:
            """
            birthDate > :birthDate
            """
        Then I should have the following results:
            | pseudo   |
            | Joe      |
            | Margaret |

    Scenario: It works with birth date smaller or equal to an other one
        Given RulerZ is configured
        And I use the array of objects dataset
        When I define a birthDate parameter to "2005-01-04"
        When I filter the dataset with the rule:
            """
            birthDate <= :birthDate
            """
        Then I should have the following results:
            | pseudo    |
            | Bob       |
            | Ada       |
            | Kévin     |
            | Alice     |
            | Louise    |
            | Francis   |
            | John      |
            | Arthur    |
            | Moon Moon |
