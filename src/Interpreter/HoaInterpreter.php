<?php

namespace RulerZ\Interpreter;

use Hoa\Compiler;
use Hoa\File;
use Hoa\Ruler;
use Hoa\Ruler\Visitor\Interpreter as RulerInterpreter;
use Hoa\Visitor;

use RulerZ\Model\Parameter;

/**
 * Interpretes a rule.
 */
class HoaInterpreter implements Interpreter, Visitor\Visit
{
    /**
     * Compiler.
     *
     * @var \Hoa\Compiler\Llk\Parser $compiler
     */
    private $compiler;

    /**
     * Root.
     *
     * @var \Hoa\Ruler\Model object
     */
    private $root;

    /**
     * Current node.
     *
     * @var \Hoa\Ruler\Visitor\Interpreter object
     */
    private $current;

    /**
     * Next positional parameter index.
     *
     * @var int
     */
    private $nextParameterIndex = 0;

    public function __construct()
    {
        $this->compiler = Compiler\Llk::load(
            new File\Read(__DIR__ .'/../Grammar.pp')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function interpret($rule)
    {
        return $this->visit($this->compiler->parse($rule));
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     *
     * @return  \Hoa\Ruler\Model
     *
     * @throw   \Hoa\Ruler\Exception\Interpreter
     */
    public function visit(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $id       = $element->getId();
        $variable = false !== $eldnah;

        switch ($id) {
            case '#expression':
                $this->root             = new Ruler\Model();
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
                    array($left, $right),
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
                $out = array();

                foreach ($element->getChildren() as $child) {
                    $out[] = $child->accept($this, $handle, $eldnah);
                }

                return $out;

            case '#function_call':
                $children = $element->getChildren();
                $name     = $children[0]->accept($this, $handle, false);
                array_shift($children);

                $arguments = array();

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

                return $this->root->operation(
                    $name,
                    array($left, $right)
                );

            case '#not':
                return $this->root->operation(
                    'not',
                    array($element->getChild(0)->accept($this, $handle, $eldnah))
                );

            case 'token':
                $token = $element->getValueToken();
                $value = $element->getValueValue();

                switch ($token) {
                    case 'identifier':
                        return true === $variable ? $this->root->variable($value) : $value;

                    case 'named_parameter':

                        return new Parameter(substr($value, 1));

                    case 'positional_parameter':
                        $index = $this->nextParameterIndex++;

                        return new Parameter($index);

                    case 'true':
                        return true;

                    case 'false':
                        return false;

                    case 'null':
                        return null;

                    case 'float':
                        return floatval($value);

                    case 'integer':
                        return intval($value);

                    case 'string':
                        return str_replace(
                            '\\' . $value[0],
                            $value[0],
                            substr($value, 1, -1)
                        );

                    default:
                        throw new Ruler\Exception\Interpreter('Token %s is unknown.', 0, $token);
                }
              break;

            default:
                throw new Ruler\Exception\Interpreter('Element %s is unknown.', 1, $id);
        }

        return;
    }
}
