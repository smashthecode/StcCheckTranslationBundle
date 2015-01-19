StcCheckTranslationBundle
==================================================================

1. `composer.json`:

        "smashthecode/check-translation-bundle": "dev-master"

    and

        "repositories": [
            {
                "type": "vcs",
                "url":  "https://github.com/overthecode/StcCheckTranslationBundle.git"
            }
        ],

    and run:

        php composer.phar install
2. kernel:

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Stc\CheckTranslationBundle\StcCheckTranslationBundle(),
            );
        }
3. `config.yml`:

        stc_check_translation:
            files:
                - 'App/DemoBundle/Resources/translations/messages'
            locales: [pl, en]


4. run from console:

        app/console stc:check-translation
5. usage in test:

        use Symfony\Bundle\FrameworkBundle\Console\Application as App;
        use Symfony\Component\Console\Tester\CommandTester;
        use Stc\CheckTranslationBundle\Command\CheckTranslationCommand;

        public function testDefaultCommand()
        {
            $kernel = $this->createKernel();
            $kernel->boot();

            $application = new App($kernel);
            $application->add(new CheckTranslationCommand());

            $command = $application->find('stc:check-translation');
            $commandTester = new CommandTester($command);
            $commandTester->execute(array('command' => $command->getName()));
        }
