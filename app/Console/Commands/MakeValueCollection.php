<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Values\Collection;
use function file_exists;
use function file_get_contents;
use Illuminate\Console\Command;
use Nette\InvalidStateException;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use RuntimeException;
use Str;

class MakeValueCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:value-collection
                                {name : Value object or Collection name}
                                {--u|update : Update Value object to add Trait}
                                {--f|force : Create the class even if the Collection already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Value Collection class';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('context')) {
            $this->error('You must specify a context for the Value object.');

            return 0;
        }

        $className = $this->argument('name');
        if (!Str::endsWith($className, 'Collection')) {
            $className .= 'Collection';
        }

        $namespace = 'App\\Domain\\' . ucfirst($this->option('context')) . '\\Values\\Collections';
        $directory = app_path('Domain/' . ucfirst($this->option('context')) . '/Values/Collections');

        $filename = $directory . '/' . $className . '.php';
        if (file_exists($filename) && !$this->option('force')) {
            $this->error('The Value Collection already exists.');

            return 0;
        }

        if (!file_exists($directory)) {
            if (!mkdir($directory) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        $valueClassName = Str::before($className, 'Collection');
        $valueFilename = app_path('Domain/' . ucfirst($this->option('context')) . '/Values/' . $valueClassName . '.php');
        $valueNamespace = Str::before($namespace, 'Collections');

        $file = new PhpFile();
        $file->setStrictTypes();
        $collectionNamespace = $file->addNamespace($namespace);
        $collectionNamespace->addUse(Collection::class);

        $class = $collectionNamespace->addClass($className);
        $class->setExtends(Collection::class);

        file_put_contents($filename, (string) $file);

        $this->components->info(sprintf('Value Collection [%s] created successfully.', $filename));

        if ($this->option('update')) {
            if (!file_exists($valueFilename)) {
                $this->components->error('The Value object does not exist.');

                return 0;
            }

            $valueFile = PhpFile::fromCode(file_get_contents($valueFilename));

            try {
                $valueFile->getNamespaces()[Str::replaceEnd('\\', '', $valueNamespace)]->addUse(HasValueCollection::class);

                $valueFile->getClasses()[$valueNamespace . $valueClassName]->addTrait(HasValueCollection::class);

                file_put_contents($valueFilename, (new PsrPrinter)->printFile($valueFile));
            } catch (InvalidStateException) {
            }

            $this->components->info(sprintf('Value object [%s] updated successfully.', $valueFilename));
        }

        return 0;
    }
}
