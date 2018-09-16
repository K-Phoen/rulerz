<?php

declare(strict_types=1);

namespace spec\RulerZ\Parser;

use Hoa\Compiler\Exception\Exception;
use PhpSpec\ObjectBehavior;
use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;

class ParserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Parser::class);
    }

    /**
     * @dataProvider validRules
     */
    public function it_returns_an_ast_for_a_valid_rule($rule)
    {
        $this->parse($rule)->shouldHaveType(Rule::class);
    }

    /**
     * @dataProvider invalidRules
     */
    public function it_throws_an_exception_for_an_invalid_rule($rule)
    {
        $this->shouldThrow(Exception::class)->duringParse($rule);
    }

    public function validRules()
    {
        return [
            ['points > 30'],
            ['some_point ∈ some_figure'],
            ['some_point ∈ :some_figure'],
            ['some_point ∈ ["some", "list", "of", "points"]'],
            ['group(user) ∈ :allowed_groups'],
            ['locked = false'],
            ['admin = true'],
            ['deleted_at = null'],
            ['user.group = "members"'],
            ["user.group = 'members'"],
            ['user.group in ["members", "admins"]'],
            ['length(name) = 4'],
            ['distance(lat1, long1, lat2, long2) < 50'],
            ['name = :user_name'],
            ['name = ?'],
            ['name = ? and group = ?'],
            ['name = ? and group = :group'],
            ['points > 30 and group = "member"'],
            ['(points > 30 and group in ["member", "guest"]) or group = "admin"'],
            ['not points > 30'],
            ['a < -1'],
            ['a > -0.05'],
        ];
    }

    public function invalidRules()
    {
        return [
            ['> 30'],
            ['name[0] = "a"'],
            ['name.foo() = "a"'],
        ];
    }

    public function it_converts_positional_parameters_to_indexed_ones()
    {
        $model = $this->parse('name = ?');
        $parameters = $model->getParameters();

        $parameters->shouldHaveCount(1);
        $parameters[0]->getName()->shouldBe(0);
    }

    public function it_can_parse_several_rules_and_generate_valid_indexes_for_parameters()
    {
        $this->parse('name = ?');
        $model = $this->parse('name = ?');
        $parameters = $model->getParameters();

        $parameters->shouldHaveCount(1);
        $parameters[0]->getName()->shouldBe(0);
    }
}
