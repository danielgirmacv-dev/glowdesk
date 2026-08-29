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

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['reply' => 'Error: API key not configured.'], 500);
        }

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
            'parts' => [['text' => $request->input('message')]]
        ];

        // Dynamically build the product list from the database
        $products = Product::where('is_active', true)->get(['name']);
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

Skin concern → product mapping (use as guide):
  oily skin      → Cleanser or Face Wash
  dry skin       → Moisturizer
  dark spots     → Serum
  sun protection → Sunscreen
  dull skin      → Toner or Serum
  acne           → Cleanser or Face Wash
  hydration      → Moisturizer or Toner

RULE 4 — General conversation
If the message is a greeting, casual talk, or unrelated topic,
respond in a friendly, helpful way as GlowBot.
You may mention GlowDesk products naturally when relevant.

─── STRICT LIMITS ────────────────────────────────────
✗ Never confirm or create actual orders
✗ Never generate or mention specific prices
✗ Never invent products outside the available list
✗ Never add extra text when returning INTENT format
✗ Product names must EXACTLY match the available list

─── EXAMPLES ─────────────────────────────────────────
User: I want to buy cleanser
GlowBot:
INTENT: ORDER
PRODUCT: Cleanser

User: Do you have sunscreen?
GlowBot:
INTENT: INQUIRY
PRODUCT: Sunscreen

User: I need something for oily skin
GlowBot:
INTENT: INQUIRY
PRODUCT: Cleanser

User: hello
GlowBot: Hello! 👋 Welcome to GlowDesk! How can I help you glow today?

User: I want serum
GlowBot:
INTENT: ORDER
PRODUCT: Serum";

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
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "I'm having trouble thinking right now. Could you repeat that?";
                return response()->json(['reply' => $reply]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json(['reply' => 'Sorry, I am currently experiencing technical difficulties. (API Error)'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json(['reply' => 'An error occurred. Please try again later.'], 500);
        }
    }
}
