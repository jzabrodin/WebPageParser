<?php

require_once __DIR__ . '/../Runner.php';
require_once __DIR__ . '/Report.php';

class Parser
{

    const supported_commands = [
        'exit' => 'exit from program',
        'parse' => 'parse site,' .
            ' site is first parameter ,' . PHP_EOL .
            ' example : parse www.netpeak.ua',
        'report' => 'generating report for domain' . PHP_EOL .
            'example report www.netpeak.ua',
        'help' => 'show list of available commands'
    ];

    public $argv;

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    public function executeCommand()
    {
        echo "\n Hello! \n";
        $res = $this->argv[1];

        switch ($res) {
            case 'parse':
                $this->executeParse();
                break;
            case 'report':
                $this->executeReport();
                break;
            case 'help':
                $this->printListOfCommands();
                break;
            default:
                $this->executeNotSupported($res);
                break;

        }
        echo "\n Good bye! \n";
    }

    private function executeParse(): void
    {
        $site = $this->argv[2];

        $exploded = explode('.', $site);

        if (count($exploded) < 2) {
            echo "\n Bad url. Sorry :( \n";
        } else {
            $runner = new Runner($site);
            $runner->run();
        }

    }

    private function executeReport()
    {
        $domain = (string)$this->argv[2];
        $report = new Report($domain);
        $report->printReport();
    }

    private function printListOfCommands()
    {

        foreach (self::supported_commands as $command => $description) {
            echo "\n $command \n $description \n \n" .
                "-----------";
        }

    }

    /**
     * @param $res
     */
    private function executeNotSupported($res): void
    {
        echo "\n $res not supported \n type \'help\' for list of supported commands \n";
        exit;
    }

}


$parser = new Parser($argv);
$parser->executeCommand();
