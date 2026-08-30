<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramBotSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup-webapp {--url= : The HTTPS web application URL} {--title=Open GlowDesk : The text on the bot menu button}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register or update Telegram Bot chat menu button with Telegram Mini App WebApp URL';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService)
    {
        $url = $this->option('url') ?: config('app.url');

        if (empty($url) || $url === 'http://localhost') {
            $this->error('Please provide a valid HTTPS URL using --url=https://your-domain.com');
            return Command::FAILURE;
        }

        if (!str_starts_with($url, 'https://')) {
            $this->warn('Warning: Telegram WebApps require an HTTPS URL. Make sure your URL starts with https://');
        }

        $this->info("Setting Telegram Chat Menu Button URL to: {$url}");

        $result = $telegramService->setupChatMenuButton($url, $this->option('title'));

        if ($result['success']) {
            $this->info('✅ Telegram WebApp menu button configured successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Failed to configure Telegram menu button: ' . json_encode($result['body']));
            return Command::FAILURE;
        }
    }
}
