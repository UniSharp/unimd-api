<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;

class Serve extends Command {
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'serve';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Serve the application on the PHP development server';

  /**
   * Execute the console command.
   *
   * @return void
   *
   * @throws \Exception
   */
  public function fire()
  {
    $host = $this->input->getOption('host');
    $port = $this->input->getOption('port');
    $base = ProcessUtils::escapeArgument(__DIR__);
    $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
    $docroot = $this->laravel->basePath() . '/public';
    $this->info("Lumen development server started on http://{$host}:{$port}/");
    passthru("{$binary} -S {$host}:{$port} -t '{$docroot}'");
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'],
      ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8000],
    ];
  }
}
