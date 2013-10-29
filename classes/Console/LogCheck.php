<?php

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class LogCheck extends Command
{
    protected function configure()
    {
        $this
            ->setName('primo:logcheck')
            ->setDescription('Check Primo App Logs')
            ->addArgument('logdir', InputArgument::REQUIRED, 'Log Directory to Check')
            ->addArgument('tab', InputArgument::OPTIONAL, 'Which scope do you want to analyze?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logdir = $input->getArgument('logdir');
        if ($logdir) {
            $data = $this->getLogs($logdir);
        } else { //get all
            $data = "Please specifiy the log directory";
        }

        if ($tab = $input->getArgument('tab')) {
            //$tab = $input->getArgument('tab');
            $data = $this->getLogs($logdir, $tab);
        }

        $output->writeln($data);
    }
    
    private function getLogs($logdir, $tab = null) {
      $logdata_overview = array();
      $queries = array();
      $exports = array();
      $record_lookups = array();
      $errors = array();
      $finder = new Finder();
      $finder->files()->in($logdir)->name("*.log");
      foreach($finder as $log) {
        $log = $logdir."/".$log->getFilename();
        $logfile = fopen($log, 'r');
        while (!feof($logfile)) {
          $log_entry = fgets($logfile);
          //$data .= $log_entry;
          if(preg_match('/QUERY:(.+)REDIRECT/', $log_entry)) {
            echo $log_entry;
          }
        }
      }
      
      return $logdata_overview;
    }
    
    private function countQuerues() {
      
    }
    
    private function countExports() {
      
    }
    
    private function countRecordLookups() {
      
    }
 
}

?>