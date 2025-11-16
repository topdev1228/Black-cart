<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use function class_basename;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use function is_callable;
use Nette\InvalidStateException;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use ReflectionClass;
use RuntimeException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;
use Str;

class MakeValueFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:value-factory 
                                {name : Value object or Factory name}
                                {--u|update : Update Value object to add Trait}
                                {--force : Create the class even if the Value already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Value object Factory class';

    /**
     * Execute the console command.
     *
     * @psalm-suppress UndefinedMethod
     */
    public function handle(): int
    {
        if (!$this->option('context')) {
            $this->error('You must specify a context for the Value object.');

            return 0;
        }

        $className = $this->argument('name');
        if (!Str::endsWith($className, 'Factory')) {
            $className .= 'Factory';
        }

        $namespace = 'App\\Domain\\' . ucfirst($this->option('context')) . '\\Values\\Factories';
        $directory = app_path('Domain/' . ucfirst($this->option('context')) . '/Values/Factories');
        $filename = $directory . '/' . $className . '.php';

        if (file_exists($filename) && !$this->option('force')) {
            $this->error('The Value object Factory already exists.');

            return 0;
        }

        if (!file_exists($directory)) {
            if (!mkdir($directory) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        $file = new PhpFile();
        $file->setStrictTypes();
        $factoryNamespace = $file->addNamespace($namespace);

        $factoryNamespace->addUse(Factory::class);

        $class = $factoryNamespace->addClass($className);
        $class->setExtends(Factory::class);

        $method = $class->addMethod('definition')
            ->setReturnType('array')
            ->setPublic()
            ->setBody('return [];');

        $valueClassName = Str::before($className, 'Factory');
        $valueFilename = app_path('Domain/' . ucfirst($this->option('context')) . '/Values/' . $valueClassName . '.php');

        if (file_exists($valueFilename)) {
            $body = 'return [';

            $valueFile = PhpFile::fromCode(file_get_contents($valueFilename));

            $valueNamespace = Str::before($namespace, 'Factories');
            $class = new ReflectionClass($valueNamespace . $valueClassName);
            $parameters = $class->getMethod('__construct')->getParameters();
            foreach ($parameters as $parameter) {
                if (!$parameter->getType()->isBuiltin()) {
                    if ($parameter->getType()->getName() !== DataCollection::class) {
                        $factoryNamespace->addUse($parameter->getType()->getName());
                    }

                    $type = new ReflectionClass($parameter->getType()->getName());
                    if ($type->isSubclassOf(Value::class)) {
                        $faker = class_basename($parameter->getType()->getName()) . '::empty()';
                        if (is_callable([$parameter->getType()->getName(), 'factory'])) {
                            $faker = class_basename($parameter->getType()->getName()) . '::factory()->create()';
                        }
                    }

                    if ($parameter->getType()->getName() === Money::class) {
                        $faker = 'Money::ofMinor($this->faker->numberBetween(100, 10000), \'USD\')';
                    }

                    if ($parameter->getType()->getName() === DataCollection::class) {
                        /** @psalm-suppress InvalidAttribute */
                        $collectionOf = $parameter->getAttributes(DataCollectionOf::class)[0]->getArguments()[0];
                        $factoryNamespace->addUse($collectionOf);

                        $faker = class_basename($collectionOf) . '::collection([' . class_basename($collectionOf) . '::factory()->create()])';
                    }

                    if ($type->isEnum()) {
                        $faker = 'array_rand(' . class_basename($parameter->getType()->getName()) . '::cases())';
                    }

                    if ($parameter->getType()->getName() === CurrencyAlpha3::class) {
                        $faker = 'CurrencyAlpha3::US_Dollar';
                    }
                } else {
                    $faker = match (true) {
                        $parameter->name === 'id' || Str::endsWith($parameter->name, 'Id') => '$this->faker->uuid()',
                        $parameter->getType()?->getName() === 'string' => '$this->faker->word()',
                        $parameter->getType()?->getName() === 'int' => '$this->faker->randomNumber()',
                        $parameter->getType()?->getName() === 'float' => '$this->faker->randomFloat()',
                        $parameter->getType()?->getName() === 'bool' => '$this->faker->boolean()',
                        $parameter->getType()?->getName() === 'array' => '$this->faker->words()',
                        $parameter->getType()?->getName() === CarbonImmutable::class => 'Date::now()',
                        default => '\'\'',
                    };

                    if ($parameter->getType()?->getName() === CarbonImmutable::class) {
                        $factoryNamespace->addUse(Date::class);
                    }
                }

                $body .= "\n\t'" . $parameter->getName() . "' => " . $faker . ',';
            }

            $body .= "\n];";

            $method->setBody($body);
        }

        file_put_contents($filename, (new PsrPrinter)->printFile($file));

        $this->components->info(sprintf('Value Factory [%s] created successfully.', $filename));

        if ($this->option('update')) {
            if (!file_exists($valueFilename)) {
                $this->components->error('The Value object does not exist.');

                return 0;
            }

            try {
                $valueFile->getNamespaces()[Str::replaceEnd('\\', '', $valueNamespace)]->addUse(HasValueFactory::class);

                $valueFile->getClasses()[$valueNamespace . $valueClassName]->addTrait(HasValueFactory::class);

                file_put_contents($valueFilename, (new PsrPrinter)->printFile($valueFile));
            } catch (InvalidStateException) {
            }

            $this->components->info(sprintf('Value object [%s] updated successfully.', $valueFilename));
        }

        return 0;
    }
}
