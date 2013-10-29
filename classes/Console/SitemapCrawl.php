<?php

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



class SitemapCrawl extends Command
{
    protected function configure()
    {
       $this
            ->setName('sitemap:crawl')
            ->setDescription('Crawl Sitemap for Caching Purposes' )
              ->addArgument('host', InputArgument::OPTIONAL, 'Name of host to crawl.')
              ->addOption(
                      'hourly', null, InputOption::VALUE_NONE, 'If set, the task will refresh all URLs marked hourly')
              ->addOption(
                      'daily', null, InputOption::VALUE_NONE, 'If set, the task will refresh all URLs marked hourly')
              ->addOption(
                      'weekly', null, InputOption::VALUE_NONE, 'If set, the task will refresh all URLs marked hourly')
               
        ; 
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');
        if ($host) {
            $text = 'Hello '.$host;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('hourly')) {
            $text = strtoupper($text);
        }
        
        elseif ($input->getOption('daily')) {
            $text = strtoupper($text);
        }
        
        elseif ($input->getOption('weekly')) {
            $text = strtoupper($text);
        }
        
       

        $output->writeln($text);
    }
    
    
}
