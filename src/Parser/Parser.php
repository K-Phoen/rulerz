<?php

namespace RulerZ\Parser;

use Hoa\Compiler;
use Hoa\File;
use Hoa\Ruler;
use Hoa\Visitor;

use RulerZ\Model;

/**
 * Parses a rule.
 *
 * A valid rule returns an AST:
 * ```
 * $parser = new Parser;
 * $ast = $parser->parse('foo = 42');
 * ```
 *
 * And an invalid one throw an exception:
 * ```should_throw
 * $parser = new Parser;
 * $parser->parse('foo = ');
 * ```
 */
class Parser implements Visitor\Visit
{
    /**
     * Parser.
     *
     * @var \Hoa\Compiler\Llk\Parser $parser
     */
    private $parser;

    /**
     * Root.
     *
     * @var \RulerZ\Model\Rule object
     */
    private $root;

    /**
     * Next positional parameter index.
     *
     * @var int
     */
    private $nextParameterIndex = 0;

    /**
     * Parses the rule into an equivalent AST.
     *
     * @param string $rule The rule represented as a string.
     *
     * @return \RulerZ\Model\Rule
     */
    public function parse($rule)
    {
        if ($this->parser === null) {
            $this->parser = Compiler\Llk::load(
                new File\Read(__DIR__ .'/../Grammar.pp')
            );
        }

        $this->nextParameterIndex = 0;

        return $this->visit($this->parser->parse($rule));
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     *
     * @return  \RulerZ\Model\Rule
     *
     * @throws  \Hoa\Ruler\Exception\Interpreter
     */
    public function visit(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        /** @var \Hoa\Compiler\Llk\TreeNode $element */
        $id       = $element->getId();
        $variable = false !== $eldnah;

        switch ($id) {
            case '#expression':
                $this->root             = new Model\Rule();
                $this->root->expression = $element->getChild(0)->accept(
                    $this,
                    $handle,
                    $eldnah
                );

                return $this->root;

            case '#operation':
                $children = $element->getChildren();
                $left     = $children[0]->accept($this, $handle, $eldnah);
                $right    = $children[2]->accept($this, $handle, $eldnah);
                $name     = $children[1]->accept($this, $handle, false);

                return $this->root->_operator(
                    $name,
                    [$left, $right],
                    false
                );

            case '#variable_access':
                $children = $element->getChildren();
                $name     = $children[0]->accept($this, $handle, $eldnah);
                array_shift($children);

                foreach ($children as $child) {
                    $_child = $child->accept($this, $handle, $eldnah);

                    switch ($child->getId()) {
                        case '#attribute_access':
                            $name->attribute($_child);
                            break;
                    }
                }

                return $name;

            case '#attribute_access':
                return $element->getChild(0)->accept($this, $handle, false);

            case '#array_declaration':
                $out = [];

                foreach ($element->getChildren() as $child) {
                    $out[] = $child->accept($this, $handle, $eldnah);
                }

                return $out;

            case '#function_call':
                $children = $element->getChildren();
                $name     = $children[0]->accept($this, $handle, false);
                array_shift($children);

                $arguments = [];

                foreach ($children as $child) {
                    $arguments[] = $child->accept($this, $handle, $eldnah);
                }

                return $this->root->_operator(
                    $name,
                    $arguments,
                    true
                );

            case '#and':
            case '#or':
            case '#xor':
                $name     = substr($id, 1);
                $children = $element->getChildren();
                $left     = $children[0]->accept($this, $handle, $eldnah);
                $right    = $children[1]->accept($this, $handle, $eldnah);

                return $this->root->operation($name, [$left, $right]);

            case '#not':
                return $this->root->operation(
                    'not',
                    [$element->getChild(0)->accept($this, $handle, $eldnah)]
                );

            case 'token':
                $token = $element->getValueToken();
                $value = $element->getValueValue();

                switch ($token) {
                    case 'identifier':
                        return true === $variable ? $this->root->variable($value) : $value;

                    case 'named_parameter':

                        return new Model\Parameter(substr($value, 1));

                    case 'positional_parameter':
                        $index = $this->nextParameterIndex++;

                        return new Model\Parameter($index);

                    case 'true':
                        return true;

                    case 'false':
                        return false;

                    case 'null':
                        return null;

                    case 'float':
                        return (float) $value;

                    case 'integer':
                        return (int) $value;

                    case 'string':
                        return str_replace(
                            '\\' . $value[0],
                            $value[0],
                            substr($value, 1, -1)
                        );

                    default:
                        throw new Ruler\Exception\Interpreter('Token %s is unknown.', 0, $token);
                }

            default:
                throw new Ruler\Exception\Interpreter('Element %s is unknown.', 1, $id);
        }
    }
}
