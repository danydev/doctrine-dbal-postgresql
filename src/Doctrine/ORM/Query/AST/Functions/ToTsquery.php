<?php

namespace Opsway\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class ToTsquery extends FunctionNode
{
    private $config = null;

    private $expr1;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr1 = $parser->StringPrimary();

        if ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->config = $this->expr1;
            $this->expr1 = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        if (null === $this->config) {
            return sprintf(
                'to_tsquery(%s)',
                $this->expr1->dispatch($sqlWalker)
            );
        } else {
            return sprintf(
                'to_tsquery(%s, %s)',
                $this->config->dispatch($sqlWalker),
                $this->expr1->dispatch($sqlWalker)
            );
        }
    }
}
