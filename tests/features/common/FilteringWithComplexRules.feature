Feature: RulerZ can filter a target with all kind of rules

    Background:
        Given RulerZ is configured
        And I use the default dataset
        And I use the default execution context

    Scenario: It works a simple equality
        When I filter the dataset with the rule:
            """
            gender = "F"
            """
        Then I should have the following results:
            | pseudo   |
            | Ada      |
            | Margaret |
            | Alice    |

    Scenario: It works a simple equality
        When I filter the dataset with the rule:
            """
            pseudo IN ["Ada", "Bob"]
            """
        Then I should have the following results:
            | pseudo |
            | Ada    |
            | Bob    |

    Scenario: It works a parameter
        When I define the parameters:
            | gender | M |
        And I filter the dataset with the rule:
            """
            gender = :gender
            """
        Then I should have the following results:
            | pseudo |
            | Joe    |
            | Bob    |
            | Kévin  |

    Scenario: Conjunctions can be used
        When I filter the dataset with the rule:
            """
            gender = "F" and points > 9000
            """
        Then I should have the following results:
            | pseudo | points |
            | Ada    | 10000  |

    Scenario: Disjunctions can be used
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
        When I define the parameters:
            | gender | M |
        And I filter the dataset with the rule:
            """
            not(gender = :gender)
            """
        Then I should have the following results:
            | pseudo   |
            | Ada      |
            | Margaret |
            | Alice    |
