<?php

namespace BingScraper\Console\Command\Db;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateMigration extends Command
{
    protected function configure()
    {
        $this->setName('db:create-migration')
             ->setDescription('Create new migration file.')
             ->addArgument('migration-name', InputArgument::OPTIONAL, 'Descriptive name for the migration in snake_case.');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $name = $in->getArgument('migration-name');
        if (!$name) {
            $helper = $this->getHelper('question');
            $question = new Question('Descripe the migration > ');
            do {    
                $name = $helper->ask($in, $out, $question);
            } while (!$name);
        }


        $file = $this->migrationFilename($name);
        $clazz = $this->migrationClass($name);
        $classSkeleton = $this->migrationSkeleton($clazz);

        touch($file);
        if (file_put_contents($file, $classSkeleton)) {
            $this->out->writeln("New migration file $file created.");
        } else {
            $this->out->writeln("<error>Can't create file $file.</error>");
        }
    }

    private function migrationFilename($name)
    {
        $snakeName = $this->toSnakeCase($name);
        $now = new \DateTime();

        return PROJECT_DIR . '/db/migrations/' . $now->format('Ymd_His_') . $snakeName . '.php';
    }

    private function toSnakeCase($str)
    {
        $words = preg_replace('/\s+/', ' ', $str);
        $trimmed = trim($words);
        $snake = str_replace(' ', '_', $trimmed);
        $clean = preg_replace('/[^0-9A-z_]/', '', $snake);
        return strtolower($clean);
    }

    private function migrationClass($name)
    {
        $snakeName = $this->toSnakeCase($name);
        return $this->snakeToCamel($snakeName);
    }

    private function snakeToCamel($snake)
    {
        $words = explode('_', $snake);
        $upper = array_map('ucfirst', $words);
        return implode('', $upper);
    }

    private function migrationSkeleton($className)
    {
        $content = <<<PHP
<?php

use BingScraper\Database\Migration;
use Illuminate\Database\Schema\Builder;

class {$className} implements Migration
{

    public function up(Builder \$schemaBuilder)
    {
        \$schemaBuilder->create('some table', function(\$table) {
            \$table->increments('id');
            // \$table->string('someColumn');
            \$table->timestamps();
        });
    }

}

PHP;

        return $content;
    }
}
