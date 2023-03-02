<?php

namespace Ikechukwukalu\Requirepin\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SampleRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sample:routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the package sample routes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/stubs/web.stub'),
            FILE_APPEND
        );

        file_put_contents(
            base_path('routes/api.php'),
            file_get_contents(__DIR__.'/stubs/api.stub'),
            FILE_APPEND
        );

        $this->components->info('Sample Routes scaffolding generated successfully.');
    }
}
