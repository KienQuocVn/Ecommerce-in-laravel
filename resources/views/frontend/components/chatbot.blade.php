<!-- AI Chatbot Widget -->
<div id="chatbot-container" style="position: fixed; right: 20px; bottom: 20px; z-index: 9999;">
    <!-- Chat Panel -->
    <div id="chatbot-panel" style="display: none; width: 380px; height: 550px; max-height: 80vh; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); background: #ffffff; margin-bottom: 12px;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #ffffff;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.25rem;">🤖</span>
                <div>
                    <div style="font-weight: 600; font-size: 0.95rem;">AI Assistant</div>
                    <div style="font-size: 0.75rem; opacity: 0.9;">Online</div>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button id="chatbot-new-btn" title="New Chat" style="padding: 6px 10px; border-radius: 8px; border: none; background: rgba(255,255,255,0.2); color: #ffffff; cursor: pointer; font-size: 0.85rem; font-weight: 500; transition: background 0.2s;">
                    New
                </button>
                <button id="chatbot-close-btn" title="Close" style="width: 32px; height: 32px; border-radius: 8px; border: none; background: rgba(255,255,255,0.2); color: #ffffff; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
                    ✕
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" style="flex: 1; min-height: 0; background: #fafafa; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; scroll-behavior: smooth;">
            <div id="chatbot-empty-state" style="opacity: 0.5; text-align: center; padding: 2rem 1rem; font-size: 0.9rem; display: block;">
                👋 Bắt đầu trò chuyện với AI
            </div>
        </div>

        <!-- Input Area -->
        <div style="padding: 0.75rem; border-top: 1px solid #e5e7eb; background: #ffffff;">
            <div id="chatbot-loading" style="display: none; font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #f3f4f6; border-radius: 8px;">
                <span style="animation: chatbot-spin 1s linear infinite; display: inline-block;">⏳</span>
                <span>AI đang trả lời... (vui lòng chờ)</span>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="text" id="chatbot-input" placeholder="Nhập tin nhắn..." style="flex: 1; padding: 0.65rem 0.9rem; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 0.9rem; outline: none; transition: all 0.2s; background: #ffffff;">
                <button id="chatbot-send-btn" style="padding: 0.65rem 1.2rem; border-radius: 12px; border: none; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #ffffff; cursor: pointer; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3); min-width: 70px;">
                    ➤
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Button -->
    <button id="chatbot-toggle-btn" aria-label="Toggle Chat" style="width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4); transition: all 0.3s ease;">
        💬
    </button>
</div>

<!-- Chatbot Styles -->
<style>
    @keyframes chatbot-slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes chatbot-spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    #chatbot-panel.chatbot-open {
        display: flex !important;
        animation: chatbot-slideUp 0.3s ease-out;
    }

    #chatbot-messages .chatbot-message {
        display: flex;
        justify-content: flex-start;
        width: 100%;
    }

    #chatbot-messages .chatbot-message.user-message {
        justify-content: flex-end;
    }

    #chatbot-messages .chatbot-message-bubble {
        max-width: 80%;
        padding: 0.65rem 0.9rem;
        border-radius: 12px;
        font-size: 0.9rem;
        line-height: 1.5;
        word-wrap: break-word;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        white-space: pre-wrap;
    }

    #chatbot-messages .chatbot-message.user-message .chatbot-message-bubble {
        border-radius: 12px 12px 2px 12px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #ffffff;
    }

    #chatbot-messages .chatbot-message.assistant-message .chatbot-message-bubble {
        border-radius: 12px 12px 12px 2px;
        background: #f3f4f6;
        color: #1f2937;
    }

    #chatbot-messages .chatbot-message-think {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        opacity: 0.8;
        font-size: 0.85rem;
        font-style: italic;
    }

    #chatbot-input:focus {
        border-color: #3b82f6 !important;
    }

    #chatbot-send-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4) !important;
    }

    #chatbot-send-btn:disabled {
        background: #e5e7eb !important;
        color: #9ca3af !important;
        cursor: not-allowed !important;
        box-shadow: none !important;
    }

    #chatbot-toggle-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 32px rgba(37, 99, 235, 0.5) !important;
    }

    #chatbot-toggle-btn.chatbot-open {
        transform: rotate(180deg);
    }

    #chatbot-new-btn:hover,
    #chatbot-close-btn:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }
</style>

<!-- Chatbot Script -->
<script src="{{ asset('frontend/js/chatbot.js') }}"></script>