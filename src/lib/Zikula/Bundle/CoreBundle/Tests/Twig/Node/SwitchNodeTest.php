<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\Bundle\CoreBundle\Tests\Twig\Node;

use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;
use Zikula\Bundle\CoreBundle\Twig\Node\SwitchNode;

class SwitchNodeTest extends NodeTestCase
{
    /**
     * @covers SwitchNode::__construct
     */
    public function testConstructor()
    {
        $expression = new NameExpression('foo', 0);
        $default = null;
        $cases = new Node();
        $cases->setNode(0, new Node([
            'expression' => new ConstantExpression(0, 0),
            'body' => new TextNode('case 0', 0)
        ]));
        $cases->setNode(1, new Node([
            'expression' => new ConstantExpression(1, 0),
            'body' => new TextNode('case 1', 0)
        ]));
        $cases->getNode(1)->setAttribute('break', true);

        $node = new SwitchNode($cases, $default, $expression, 0);

        $this->assertEquals($expression, $node->getNode('expression'));
        $this->assertEquals($default, $node->getNode('default'));
        $this->assertEquals($cases, $node->getNode('cases'));

        $default = new TextNode('default case', 0);
        $node = new SwitchNode($cases, $default, $expression, 0);
        $this->assertEquals($default, $node->getNode('default'));
    }

    public function getTests()
    {
        $tests = [];

        // #1 switch with one case, without break
        $expression = new NameExpression('foo', 0);
        $default = null;
        $cases = new Node();
        $cases->setNode(0, new Node([
            'expression' => new ConstantExpression(0, 0),
            'body' => new TextNode('case 0', 0)
        ]));
        $node = new SwitchNode($cases, $default, $expression, 0);

        $tests[] = [$node, <<<EOF
switch ({$this->getVariableGetter('foo')}) {
    case 0:
        echo "case 0";
}
EOF
        ];

        // #2 switch with two cases, second with break
        $expression = new NameExpression('foo', 0);
        $default = null;
        $cases = new Node();
        $cases->setNode(0, new Node([
            'expression' => new ConstantExpression(0, 0),
            'body' => new TextNode('case 0', 0)
        ]));
        $cases->setNode(1, new Node([
            'expression' => new ConstantExpression(1, 0),
            'body' => new TextNode('case 1', 0)
        ]));
        $cases->getNode(1)->setAttribute('break', true);
        $node = new SwitchNode($cases, $default, $expression, 0);

        $tests[] = [$node, <<<EOF
switch ({$this->getVariableGetter('foo')}) {
    case 0:
        echo "case 0";
    case 1:
        echo "case 1";
        break;
}
EOF
        ];

        // #3 switch with two cases (second with break) and default
        $expression = new NameExpression('foo', 0);
        $default = new TextNode('default case', 0);
        $cases = new Node();
        $cases->setNode(0, new Node([
            'expression' => new ConstantExpression(0, 0),
            'body' => new TextNode('case 0', 0)
        ]));
        $cases->setNode(1, new Node([
            'expression' => new ConstantExpression(1, 0),
            'body' => new TextNode('case 1', 0)
        ]));
        $cases->getNode(1)->setAttribute('break', true);
        $node = new SwitchNode($cases, $default, $expression, 0);

        $tests[] = [$node, <<<EOF
switch ({$this->getVariableGetter('foo')}) {
    case 0:
        echo "case 0";
    case 1:
        echo "case 1";
        break;
    default:
        echo "default case";
}
EOF
        ];

        // #4 switch with two cases (first without body, second with break) and default
        $expression = new NameExpression('foo', 0);
        $default = new TextNode('default case', 0);
        $cases = new Node();
        $cases->setNode(0, new Node([
            'expression' => new ConstantExpression(0, 0),
            'body' => new Node()
        ]));
        $cases->setNode(1, new Node([
            'expression' => new ConstantExpression(1, 0),
            'body' => new TextNode('case 1', 0)
        ]));
        $cases->getNode(1)->setAttribute('break', true);
        $node = new SwitchNode($cases, $default, $expression, 0);

        $tests[] = [$node, <<<EOF
switch ({$this->getVariableGetter('foo')}) {
    case 0:
    case 1:
        echo "case 1";
        break;
    default:
        echo "default case";
}
EOF
        ];

        return $tests;
    }
}
