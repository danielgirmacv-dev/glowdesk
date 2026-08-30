<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendOrderNotification($order)
    {
        if (!$this->botToken || !$this->chatId) {
            Log::warning('Telegram bot token or chat ID is not configured.');
            return;
        }

        $items = $order->items->map(function ($item) {
            return "{$item->product->name} (x{$item->quantity})";
        })->join("\n- ");

        $message = "🛒 *New Internal Order*\n\n"
            . "👤 *Customer:* {$order->customer_name}\n"
            . "🏢 *Department:* {$order->department}\n"
            . "📱 *Phone:* {$order->phone}\n"
            . "🕒 *Time:* {$order->created_at->format('Y-m-d H:i:s')}\n\n"
            . "🛍 *Items:*\n- {$items}\n\n"
            . "💰 *Total:* $" . number_format($order->total_amount, 2);

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API error details: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }

    public function sendClientConfirmation($order)
    {
        if (!$this->botToken || empty($order->telegram_username)) {
            return;
        }

        $username = trim(trim($order->telegram_username, '@'));
        $targetChatId = null;

        try {
            // First, dynamically resolve the @username to a numeric chat_id using getUpdates
            $updatesResponse = Http::get("https://api.telegram.org/bot{$this->botToken}/getUpdates");
            
            if ($updatesResponse->successful()) {
                $updates = $updatesResponse->json('result') ?? [];
                
                // Scan backwards (recent first) to find the user
                foreach (array_reverse($updates) as $update) {
                    if (isset($update['message']['from']['username'])) {
                        if (strtolower($update['message']['from']['username']) === strtolower($username)) {
                            $targetChatId = $update['message']['from']['id'];
                            break;
                        }
                    }
                }
            }

            if (!$targetChatId) {
                Log::error("Telegram Client Notification: Could not resolve username @{$username} to a numeric chat_id. Ensure they have messaged the bot recently.");
                return;
            }

            $items = $order->items->map(function ($item) {
                return "{$item->quantity}x {$item->product->name}";
            })->join("\n- ");

            $message = "✅ *Order Confirmed!*\n\n"
                . "Hi {$order->customer_name}, your order from GlowDesk is now being processed 🎉\n\n"
                . "🛍 *Items:*\n- {$items}\n\n"
                . "💰 *Total:* Br " . number_format($order->total_amount, 2) . "\n\n"
                . "We'll be in touch shortly. Thank you!";

            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error('Telegram Client Notification error details: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram client confirmation: ' . $e->getMessage());
        }
    }

    public function sendCustomRequestNotification(string $name, string $phone, string $requestMessage, ?string $telegramUsername = null)
    {
        if (!$this->botToken || !$this->chatId) {
            return;
        }

        $message = "📝 *New Custom Item Request*\n\n"
            . "👤 *Customer:* {$name}\n"
            . "📱 *Phone:* {$phone}\n"
            . ($telegramUsername ? "✈️ *Telegram:* @{$telegramUsername}\n\n" : "\n")
            . "💭 *Looking for:*\n_{$requestMessage}_";

        try {
            Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram custom request notification: ' . $e->getMessage());
        }
    }

    /**
     * Verifies the initData string sent from Telegram WebApp SDK using HMAC-SHA256.
     */
    public function verifyWebAppData(string $initData): bool
    {
        if (empty($this->botToken) || empty($initData)) {
            return false;
        }

        parse_str($initData, $data);
        if (!isset($data['hash'])) {
            return false;
        }

        $hash = $data['hash'];
        unset($data['hash']);

        $dataCheckArr = [];
        foreach ($data as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        sort($dataCheckArr);

        $dataCheckString = implode("\n", $dataCheckArr);
        $secretKey = hash_hmac('sha256', $this->botToken, 'WebAppData', true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals(bin2hex($calculatedHash), $hash);
    }

    /**
     * Configures Telegram bot's menu button to open GlowDesk WebApp.
     */
    public function setupChatMenuButton(string $webAppUrl, string $buttonText = 'Open GlowDesk'): array
    {
        if (!$this->botToken) {
            return ['success' => false, 'message' => 'Bot token is missing'];
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/setChatMenuButton";
        
        $response = Http::post($url, [
            'menu_button' => [
                'type' => 'web_app',
                'text' => $buttonText,
                'web_app' => [
                    'url' => $webAppUrl
                ]
            ]
        ]);

        return [
            'success' => $response->successful(),
            'body' => $response->json()
        ];
    }
}
