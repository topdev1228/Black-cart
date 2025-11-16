<?php
declare(strict_types=1);

namespace App\Console\GrumPHP;

use function count;
use GrumPHP\Formatter\ProcessFormatterInterface;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Config\ConfigOptionsResolver;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractExternalTask<ProcessFormatterInterface>
 */
class Npx extends AbstractExternalTask
{
    public static function getConfigurableOptions(): ConfigOptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'script' => null,
            'triggered_by' => ['js', 'jsx', 'coffee', 'ts', 'tsx', 'less', 'sass', 'scss'],
            'working_directory' => './',
            'args' => [],
        ]);

        $resolver->addAllowedTypes('script', ['string']);
        $resolver->addAllowedTypes('triggered_by', ['array']);
        $resolver->addAllowedTypes('working_directory', ['string']);
        $resolver->addAllowedTypes('args', ['array']);

        return ConfigOptionsResolver::fromOptionsResolver($resolver);
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();
        $files = $context->getFiles()->extensions($config['triggered_by']);
        if (0 === count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = $this->processBuilder->createArgumentsForCommand('npx');
        $arguments->addRequiredArgument('%s', $config['script']);
        $arguments->addArgumentArray('%s', $config['args']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->setWorkingDirectory(realpath($config['working_directory']));
        $process->run();

        if (!$process->isSuccessful()) {
            return TaskResult::createFailed($this, $context, $this->formatter->format($process));
        }

        return TaskResult::createPassed($this, $context);
    }
}
