<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Collection;
use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\Value;
use function file_exists;
use function file_put_contents;
use Illuminate\Console\Command;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use RuntimeException;
use Str;

class MakeValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:value
        {name : Value object name}
        {--f|factory : Create an accompanying Factory class}
        {--c|collection : Create an accompanying Collection class}
        {--force : Create the class even if the Value already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Value object class';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('context')) {
            $this->error('You must specify a context for the Value object.');

            return 0;
        }

        $namespace = 'App\\Domain\\' . ucfirst($this->option('context')) . '\\Values';
        $directory = app_path('Domain/' . ucfirst($this->option('context')) . '/Values');
        $classFile = $directory . '/' . $this->argument('name') . '.php';

        if (file_exists($classFile) && !$this->option('force')) {
            $this->components->error('The Value object already exists.');

            return 0;
        }

        $file = new PhpFile();
        $file->setStrictTypes();
        $classNamespace = $file->addNamespace($namespace);
        if (Str::lower($this->option('context')) !== 'shared') {
            $file->addUse(Value::class);
        }

        $class = $classNamespace->addClass($this->argument('name'));
        $class->setExtends(Value::class);
        $class->addMethod('__construct')
            ->setPublic();

        if ($this->option('factory')) {
            $factoryDirectory = $directory . '/Factories';
            if (!file_exists($factoryDirectory)) {
                mkdir($factoryDirectory);
            }

            $classNamespace->addUse(HasValueFactory::class);
            $class->addTrait(HasValueFactory::class);

            $factoryFile = new PhpFile();
            $factoryFile->setStrictTypes();
            $factoryNamespace = $factoryFile->addNamespace($namespace . '\\Factories');
            $factoryNamespace->addUse(Factory::class);

            $factory = $factoryNamespace->addClass($this->argument('name') . 'Factory');
            $factory->setExtends(Factory::class);
            $factory->addMethod('definition')
                ->setPublic()
                ->setReturnType('array')
                ->setBody('return [];');

            $factoryFilename = $factoryDirectory . '/' . $this->argument('name') . 'Factory.php';
            file_put_contents($factoryFilename, (new PsrPrinter)->printFile($factoryFile));

            $this->components->info(sprintf('Value Factory [%s] created successfully.', $factoryFilename));
        }

        if ($this->option('collection')) {
            $collectionsDirectory = $directory . '/Collections';
            if (!file_exists($collectionsDirectory)) {
                if (!mkdir($collectionsDirectory) && !is_dir($collectionsDirectory)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $collectionsDirectory));
                }
            }

            $classNamespace->addUse(HasValueCollection::class);
            $class->addTrait(HasValueCollection::class);

            $collectionFile = new PhpFile();
            $collectionFile->setStrictTypes();
            $collectionNamespace = $collectionFile->addNamespace($namespace . '\\Collections');
            $collectionNamespace->addUse(Collection::class);

            $collection = $collectionNamespace->addClass($this->argument('name') . 'Collection');
            $collection->setExtends(Collection::class);

            $collectionFilename = $collectionsDirectory . '/' . $this->argument('name') . 'Collection.php';
            file_put_contents($collectionFilename, (new PsrPrinter)->printFile($collectionFile));

            $this->components->info(sprintf('Value Collection [%s] created successfully.', $collectionFilename));
        }

        file_put_contents($classFile, (new PsrPrinter)->printFile($file));

        $this->components->info(sprintf('Value object [%s] created successfully.', $classFile));

        return 0;
    }
}
