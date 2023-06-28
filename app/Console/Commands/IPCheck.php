<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;

class IPCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ip-check {ip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'IP Check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->check();
    }

    public function check()
    {
        $client = new Client();
        $statusCode = null;

        try {
            $table = new Table($this->output);
            $table->setHeaders([
                '<fg=black>Name</>', '<fg=black>Value</>'
            ]);

            $result = $client->get('http://ip-api.com/php/'.$this->argument('ip'))
                ->getBody()->getContents();

            $data = [];
            $result = collect(unserialize($result))->except('status', 'query');
            foreach ($result as $key=>$value){
                $data[] = [ucwords($key), $value];
            }

            $table->setRows($data);
            $table->render();
        } catch (GuzzleException $e) {
            $this->info($e->getMessage());
        }
    }
}
