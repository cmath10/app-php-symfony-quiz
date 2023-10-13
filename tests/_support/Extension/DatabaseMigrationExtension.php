<?php /** @noinspection PhpUnused */

namespace App\Tests\Infrastructure\Extension;

use Codeception\Events;
use Codeception\Extension;
use Codeception\Module\Cli;

final class DatabaseMigrationExtension extends Extension
{
    public static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite(): void
    {
        try {
            /** @var Cli $cli */
            $cli = $this->getModule('Cli');

            $this->writeln('ℹ Executing doctrine migrations');

            $cli->runShellCommand('php bin/console doctrine:migrations:migrate --env=test --no-interaction');
            $cli->seeResultCodeIs(0);

            $this->writeln('✔ Test database migrated to the latest version');
        } catch (\Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred while rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
