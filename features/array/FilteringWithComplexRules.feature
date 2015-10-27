Feature: RulerZ can filter an array with all kind of rules

    Scenario: It works a simple equality
        Given RulerZ is configured
        And I use the array of arrays dataset
        When I filter the dataset with the rule:
            """
            gender = "F"
            """
        Then I should have the following results:
            | pseudo   |
            | Ada      |
            | Margaret |
            | Alice    |

    Scenario: It works a parameter
        Given RulerZ is configured
        And I use the array of arrays dataset
        And I define the parameters:
            | gender | M |
        When I filter the dataset with the rule:
            """
            gender = :gender
            """
        Then I should have the following results:
            | pseudo |
            | Joe    |
            | Bob    |
            | Kévin  |

    Scenario: Conjunctions can be used
        Given RulerZ is configured
        And I use the array of arrays dataset
        When I filter the dataset with the rule:
            """
            gender = "F" and points > 9000
            """
        Then I should have the following results:
            | pseudo | points |
            | Ada    | 10000  |

    Scenario: Disjunctions can be used
        Given RulerZ is configured
        And I use the array of arrays dataset
        When I filter the dataset with the rule:
            """
            gender = "F" or points > 9000
            """
        Then I should have the following results:
            | pseudo   | points |
            | Ada      | 10000  |
            | Margaret | 5000   |
            | Alice    | 175    |
            | Bob      | 9001   |

    Scenario: Negations can be used
        Given RulerZ is configured
        And I use the array of arrays dataset
        And I define the parameters:
            | gender | M |
        When I filter the dataset with the rule:
            """
            not(gender = :gender)
            """
        Then I should have the following results:
            | pseudo   |
            | Ada      |
            | Margaret |
            | Alice    |

    Scenario: Custom operators can be used
        Given RulerZ is configured
        And I use the array of arrays dataset
        When I filter the dataset with the rule:
            """
            length(pseudo) = 3
            """
        Then I should have the following results:
            | pseudo |
            | Ada    |
            | Joe    |
            | Bob    |
