<?php declare(strict_types=1);

namespace Tests\Artprima\QueryFilterBundle\Query\Condition;

use Artprima\QueryFilterBundle\Query\Condition\IsNull;
use Artprima\QueryFilterBundle\Query\Filter;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class IsNullTest
 *
 * @author Denis Voytyuk <ask@artprima.cz>
 *
 * @package Tests\Artprima\QueryFilterBundle\Query\Condition
 */
class IsNullTest extends TestCase
{
    public function testGetExpr()
    {
        $qb = self::getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $qb
            ->expects(self::once())
            ->method('expr')
            ->willReturn(new Expr());

        $qb
            ->expects(self::never())
            ->method('setParameter');

        $condition = new IsNull();

        $expr = $condition->getExpr($qb, 0, (new Filter())
            ->setField('t.dummy')
        );

        self::assertSame('t.dummy IS NULL', (string)$expr);
    }
}