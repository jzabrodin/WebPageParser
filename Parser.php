<?php

require_once __DIR__.'/src/Runner.php';
require_once __DIR__.'/src/Report.php';

class Parser
{

    const supported_commands = [
            'exit' => 'exit from program',
            'parse' => 'parse site,'.
                    ' site is first parameter ,'.PHP_EOL.
                    ' example : parse www.netpeak.ua',
            'report' => 'generating report for domain'.PHP_EOL.
                    'example report www.netpeak.ua',
            'help' => 'show list of available commands',
    ];

    public $argv;

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    public function executeCommand()
    {
        $this->printMessage("Hello");
        $res = $this->argv[1] ?? 'empty command';

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
        $this->printMessage("Good bye!");
    }

    /**
     * @param string $message
     */
    private function printMessage(string $message): void
    {
        echo "\n {$message} \n";
    }

    private function executeParse(): void
    {
        $site = $this->argv[2] ?? '';

        if ($site === '') {
            $message = "Bad url: $site Sorry :(";
            $this->printMessage($message);

            return;
        }

        $exploded = explode('.', $site);

        if (count($exploded) < 2) {
            $message = "Bad url. Sorry :(";
            $this->printMessage($message);
        } else {
            $runner = new Runner($site);
            $runner->run();
        }

    }

    private function executeReport()
    {
        $domain = $this->argv[2] ?? '';

        if (!$domain) {
            echo "\n domain is empty \n";

            return;
        }

        $report = new Report($domain);
        $report->printReport();
    }

    private function printListOfCommands()
    {
        $this->printMessage("=== Supported commands: ===");
        foreach (self::supported_commands as $command => $description) {
            $message = "$command: \n $description ";
            $this->printMessage($message);
            $this->printMessage("-----------");
        }

    }

    /**
     * @param $res
     */
    private function executeNotSupported($res): void
    {
        $message = "$res not supported";
        $this->printMessage($message);
        $this->printListOfCommands();
        exit;
    }

}


$parser = new Parser($argv);
$parser->executeCommand();
