<?php

namespace Facile\DoctrineMySQLComeBack\Tests\Unit;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\Statement as DriverStatement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection;
use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Statement;
use Facile\DoctrineMySQLComeBack\Tests\DeprecationTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class StatementTest extends TestCase
{
    use ProphecyTrait;
    use DeprecationTrait;

    public function testExecuteStatementShouldThrowWhenItsNotRetriable(): void
    {
        $statement = new Statement(
            $this->mockConnection(),
            $this->mockDriverStatement(),
            'SELECT 1'
        );

        $this->expectException(\LogicException::class);

        $statement->executeStatement();
    }

    private function mockConnection(): Connection
    {
        $connection = $this->prophesize(Connection::class);
        $configuration = $this->prophesize(Configuration::class);
        $databasePlatform = $this->prophesize(AbstractPlatform::class);

        $connection->getConfiguration()
            ->willReturn($configuration->reveal());
        $connection->getDatabasePlatform()
            ->willReturn($databasePlatform->reveal());
        $databasePlatform->getName()
            ->shouldNotBeCalled();

        $connection->canTryAgain(Argument::type(\LogicException::class), 'SELECT 1')
            ->shouldBeCalledOnce()
            ->willReturn(false);

        return $connection->reveal();
    }

    protected function mockDriverStatement(): DriverStatement
    {
        $statement = $this->prophesize(DriverStatement::class);
        $statement->execute(Argument::cetera())
            ->willThrow(new \LogicException('This should not be retried'));

        return $statement->reveal();
    }
}
