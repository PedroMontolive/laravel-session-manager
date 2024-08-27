<?php

namespace Pemto\SessionManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'session-manager:install', description: 'Install all of the session-manager resources')]
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session-manager:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the session-manager resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->components->info('Publishing SessionManager Service Provider...');

        collect([
            'Migration' => fn () => $this->callSilent('vendor:publish', ['--tag' => 'session-manager-migrations']) == 0,
            'Configuration' => fn () => $this->callSilent('vendor:publish', ['--tag' => 'session-manager-config']) == 0,
            'Environment' => fn () => $this->configureSession(),
        ])->each(fn ($task, $description) => $this->components->task($description, $task));

        $this->components->info('SessionManager installed successfully.');
    }

    /**
     * Configure the session driver for Jetstream.
     *
     * @return void
     */
    protected function configureSession()
    {
        $this->replaceInFile('SESSION_DRIVER=cookie', 'SESSION_DRIVER=database', base_path('.env'));
        $this->replaceInFile('SESSION_DRIVER=redis', 'SESSION_DRIVER=database', base_path('.env'));
        $this->replaceInFile('SESSION_DRIVER=cookie', 'SESSION_DRIVER=database', base_path('.env.example'));
        $this->replaceInFile('SESSION_DRIVER=redis', 'SESSION_DRIVER=database', base_path('.env.example'));
    }

    /**
     * Replace a given string in a file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        if (!file_exists($path)) {
            $this->components->error("File does not exist: $path");
            return;
        }

        $contents = file_get_contents($path);
        if (str_contains($contents, $search)) {
            $contents = str_replace($search, $replace, $contents);
            file_put_contents($path, $contents);
            $this->components->info("Updated '$search' to '$replace' in $path.");
        }
    }
}
