<?php

namespace BingScraper\Console\Command\Db;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon as DateTime;
use Exception;
use BingScraper\Database\Migration;

class Migrate extends Command
{
    protected function configure()
    {
        $this->setName('db:migrate')
             ->setDescription('Run database migrations');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $lastRun = $this->checkLatestMigrationTime();
        $newMigrations = $this->listNewMigrations($lastRun);
        if ($newMigrations) {
            $this->executeMigrations($newMigrations);
        } else {
            $this->out->writeln('No new migrations found.');
        }

        $this->out->writeln('Done.');
    }

    private function checkLatestMigrationTime()
    {
        $container = $this->getContainer();
        $db = $container['db'];
        $schema = $container['db.schema'];

        if (!$schema->hasTable('migrations')) {
            $this->out->writeln('Migrations table doesn\'t exist.');
            $this->createMigrationsTable($schema, $db);
        }

        $lastRun = $db->table('migrations')->value('lastRun');

        // $lastRun should already be DateTime/Carbon instance?
        if (is_string($lastRun)) {
            return new DateTime($lastRun);
        } else {
            return $lastRun;
        }
    }

    private function createMigrationsTable($schema, $db)
    {
        $schema->create('migrations', function($table) {
            $table->dateTime('lastRun');
        });
        $this->out->writeln('Created migrations table.');

        $db->table('migrations')->insert([
            'lastRun' => new DateTime('1970-01-01 00:00:00')
        ]);
    }

    private function listNewMigrations(DateTime $lastRun)
    {
        $files = $this->allMigrationFiles();
        $newer = [];

        foreach ($files as $f) {
            $date = $this->getMigrationDateTime($f);
            if ($date > $lastRun) {
                $newer[] = $f;
            }
        }

        return $newer;
    }

    private function allMigrationFiles()
    {
        return glob(PROJECT_DIR . '/db/migrations/*.php');
    }

    private function getMigrationDateTime($filename)
    {
        $pathinfo = pathinfo($filename);
        $capture = [];
        preg_match('/^[0-9]{8}_[0-9]{6}/', $pathinfo['basename'], $capture);

        if (count($capture) === 1) {
            $timestamp = $capture[0];
            $date = DateTime::createFromFormat('Ymd_His', $timestamp);

            if ($date) {
                return $date;
            }
        }

        throw new Exception("Can't extract date time from migration filename $filename.");
    }

    private function executeMigrations($fileList)
    {
        $container = $this->getContainer();
        $db = $container['db'];
        $schema = $container['db.schema'];

        foreach ($fileList as $migrationFile) {
            $clazz = $this->getClassNameIn($migrationFile);
            $migration = new $clazz();

            if (!$migration instanceof Migration) {
                throw new Exception("Invalid migration class $clazz. Migration class should implement " . Migration::class . ".");
            }

            $this->out->writeln("Running migration $migrationFile.");
            $migration->up($schema);
        }

        $last = $fileList[count($fileList) - 1];
        $lastDateTime = $this->getMigrationDateTime($last);

        $db->table('migrations')->update([
            'lastRun' => $lastDateTime
        ]);
    }

    private function getClassNameIn($file)
    {
        $classesDefined = get_declared_classes();
        include $file;
        $newClasses = array_diff(get_declared_classes(), $classesDefined);

        if (count($newClasses) !== 1) {
            throw new Exception("Invalid migration file $file. Migration class should only declare one class.");
        }
        return end($newClasses);
    }
}
