<?php

namespace Stc\CheckTranslationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stc\CheckTranslationBundle\Model\Checker;

class CheckTranslationCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('stc:check-translation')
            ->setDescription('Check translations structure.');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $files = $container->getParameter( 'stc_check_translation.files');
        $locales = $container->getParameter( 'stc_check_translation.locales');

        $output->writeln('================================================');
        $hasErrors = false;
        foreach ($files as $file) {
            $checker = new Checker($locales, $container->get('translator'));
            $checker
                ->checkTranslationYaml($file)
                ->process()
            ;

            foreach($checker->getOutput() as $string) {
                $output->writeln($string);
            }

            if (!$hasErrors) {
                $hasErrors = $checker->getHasErrors();
            }
        }

        if ($hasErrors) {
            throw new \Exception("Translations has errors.");
        }
    }
}
