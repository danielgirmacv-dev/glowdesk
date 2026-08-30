<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class ChatbotController extends Controller
{
    public function invoke(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array'
        ]);

        $userMsg = $request->input('message');
        $products = Product::where('is_active', true)->get(['name']);
        
        $apiKey = env('GEMINI_API_KEY');

        // Build product list for system instruction
        $productList = "";
        foreach ($products as $p) {
            $productList .= "- {$p->name}\n";
        }

        $systemInstruction = "You are GlowBot, the official AI beauty assistant for GlowDesk — a premium beauty and skincare e-commerce store.
Your job is to chat naturally with customers AND detect their shopping intent.

─── STORE IDENTITY ───────────────────────────────────
Store name  : GlowDesk
Bot name    : GlowBot
Tone        : Friendly, warm, beauty-savvy

─── AVAILABLE PRODUCTS ───────────────────────────────
{$productList}
─── INTENT RULES ─────────────────────────────────────
RULE 1 — ORDER intent
If the user wants to BUY, ORDER, or PURCHASE a product,
respond ONLY in this exact format (no extra text):

INTENT: ORDER
PRODUCT: <exact_product_name>

RULE 2 — INQUIRY intent
If the user asks about price, availability, recommendation,
or details about a product, respond ONLY in this format:

INTENT: INQUIRY
PRODUCT: <exact_product_name>

RULE 3 — Indirect mentions
If the user mentions a skin concern without naming a product
(e.g. \"something for dry skin\", \"help with dark spots\"),
pick the most relevant product and use INQUIRY format.

RULE 4 — General conversation
If the message is a greeting, casual talk, or unrelated topic,
respond in a friendly, helpful way as GlowBot.

─── STRICT LIMITS ────────────────────────────────────
✗ Never confirm or create actual orders
✗ Never generate or mention specific prices
✗ Never invent products outside the available list
✗ Product names must EXACTLY match the available list";

        if ($apiKey) {
            $contents = [];
            $history = $request->input('history', []);
            
            foreach ($history as $msg) {
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['text']]]
                ];
            }
            
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMsg]]
            ];

            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemInstruction]
                    ]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.5,
                    'maxOutputTokens' => 1024,
                ]
            ];

            try {
                // Using gemini-2.0-flash endpoint
                $response = Http::timeout(8)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return response()->json(['reply' => $reply]);
                    }
                } else {
                    Log::warning('Gemini API Warning: ' . $response->status() . ' ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::warning('Chatbot Exception: ' . $e->getMessage());
            }
        }

        // Intelligent local fallback if API key missing or external request fails
        $fallbackReply = $this->getLocalFallbackReply($userMsg, $products);
        return response()->json(['reply' => $fallbackReply]);
    }

    private function getLocalFallbackReply(string $userMsg, $products): string
    {
        $msg = strtolower($userMsg);

        // Check if user specifically named a product
        foreach ($products as $p) {
            $pName = strtolower($p->name);
            if (str_contains($msg, $pName)) {
                if (str_contains($msg, 'buy') || str_contains($msg, 'order') || str_contains($msg, 'purchase') || str_contains($msg, 'get')) {
                    return "INTENT: ORDER\nPRODUCT: " . $p->name;
                }
                return "INTENT: INQUIRY\nPRODUCT: " . $p->name;
            }
        }

        // Keywords for skin concerns
        if (str_contains($msg, 'hi') || str_contains($msg, 'hello') || str_contains($msg, 'hey')) {
            return "Hello! 👋 Welcome to GlowDesk! I am GlowBot, your beauty assistant. How can I help you glow today?";
        }

        if (str_contains($msg, 'oily') || str_contains($msg, 'acne') || str_contains($msg, 'cleanse')) {
            return "For oily or acne-prone skin, deep cleansing is key! Would you like to check out our Cleanser?";
        }

        if (str_contains($msg, 'dry') || str_contains($msg, 'moisturiz') || str_contains($msg, 'hydrat')) {
            return "For dry or dehydrated skin, a rich moisturizer is essential! Would you like details on our Moisturizer?";
        }

        if (str_contains($msg, 'sun') || str_contains($msg, 'spf') || str_contains($msg, 'uv')) {
            return "Daily sun protection keeps your skin young and healthy! Check out our Sunscreen options.";
        }

        if (str_contains($msg, 'spot') || str_contains($msg, 'bright') || str_contains($msg, 'serum')) {
            return "For dark spots and glowing skin, our Serum works wonders! Would you like to view it?";
        }

        $names = $products->pluck('name')->implode(', ');
        return "I'm GlowBot, your beauty assistant! ✨ We have great skincare products available including: " . ($names ?: 'Cleanser, Moisturizer, Sunscreen, Serum') . ". Feel free to ask about any item!";
    }
}
