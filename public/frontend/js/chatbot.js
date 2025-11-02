/**
 * AI Chatbot - JavaScript Implementation
 * Converted from React + TypeScript to Vanilla JavaScript for Laravel Blade
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        DEFAULT_MODEL: 'meta-llama/llama-4-scout-17b-16e-instruct',
        DEFAULT_TEMPERATURE: 0.5,
        MAX_CONTEXT_MESSAGES: 8,
        SYSTEM_MESSAGE: 'You are a helpful AI assistant. Be concise and clear.',
        API_ENDPOINT: '/api/chat'
    };

    // State management
    const ChatState = {
        model: localStorage.getItem('chatbot_model') || CONFIG.DEFAULT_MODEL,
        temperature: parseFloat(localStorage.getItem('chatbot_temp') || CONFIG.DEFAULT_TEMPERATURE),
        history: (function() {
            try {
                return JSON.parse(localStorage.getItem('chatbot_hist') || '[]');
            } catch {
                return [];
            }
        })(),
        isLoading: false,
        isOpen: false,
        abortController: null
    };

    // DOM Elements
    const elements = {
        container: document.getElementById('chatbot-container'),
        panel: document.getElementById('chatbot-panel'),
        messages: document.getElementById('chatbot-messages'),
        emptyState: document.getElementById('chatbot-empty-state'),
        input: document.getElementById('chatbot-input'),
        sendBtn: document.getElementById('chatbot-send-btn'),
        toggleBtn: document.getElementById('chatbot-toggle-btn'),
        closeBtn: document.getElementById('chatbot-close-btn'),
        newBtn: document.getElementById('chatbot-new-btn'),
        loading: document.getElementById('chatbot-loading')
    };

    // Utility Functions
    function parseThink(content) {
        const thinkRegex = /<think[^>]*>([\s\S]*?)<\/think>/i;
        const match = content.match(thinkRegex);
        const think = match ? (match[1] || '').trim() : '';
        const clean = content.replace(/<think[^>]*>[\s\S]*?<\/think>/gi, '').trim();
        return { think, clean };
    }

    function saveToLocalStorage() {
        localStorage.setItem('chatbot_model', ChatState.model);
        localStorage.setItem('chatbot_temp', String(ChatState.temperature));
        localStorage.setItem('chatbot_hist', JSON.stringify(ChatState.history));
    }

    function scrollToBottom() {
        setTimeout(() => {
            if (elements.messages) {
                elements.messages.scrollTop = elements.messages.scrollHeight;
            }
        }, 100);
    }

    // Message Rendering
    function renderMessages() {
        if (!elements.messages) return;

        // Clear messages (but keep empty state for now)
        const emptyState = elements.messages.querySelector('#chatbot-empty-state');
        const existingMessages = elements.messages.querySelectorAll('.chatbot-message');
        existingMessages.forEach(msg => msg.remove());

        if (ChatState.history.length === 0) {
            if (elements.emptyState && !emptyState) {
                elements.messages.appendChild(elements.emptyState);
            } else if (elements.emptyState) {
                elements.emptyState.style.display = 'block';
            }
            return;
        }

        // Hide empty state
        if (elements.emptyState) {
            elements.emptyState.style.display = 'none';
        }

        ChatState.history.forEach((msg, index) => {
            if (msg.role === 'system') return;

            const isUser = msg.role === 'user';
            const { think, clean } = parseThink(msg.content || '');

            // Message container
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${isUser ? 'user-message' : 'assistant-message'}`;

            // Message bubble
            const bubble = document.createElement('div');
            bubble.className = 'chatbot-message-bubble';
            bubble.textContent = clean || msg.content || '';

            messageDiv.appendChild(bubble);

            // Add thinking if exists
            if (think) {
                const thinkDiv = document.createElement('div');
                thinkDiv.className = 'chatbot-message-think';
                thinkDiv.textContent = `💭 ${think}`;
                bubble.appendChild(thinkDiv);
            }

            elements.messages.appendChild(messageDiv);
        });

        scrollToBottom();
    }

    // API Communication
    async function sendMessage() {
        const inputText = elements.input.value.trim();
        if (!inputText || ChatState.isLoading) return;

        // Cancel previous request if still running
        if (ChatState.abortController) {
            ChatState.abortController.abort();
        }

        ChatState.isLoading = true;
        ChatState.abortController = new AbortController();
        updateUI();

        // Add user message to history
        const userMsg = { role: 'user', content: inputText };
        ChatState.history.push(userMsg);
        saveToLocalStorage();
        renderMessages();

        // Clear input
        elements.input.value = '';

        // Get recent history only
        const recentHistory = ChatState.history.slice(-CONFIG.MAX_CONTEXT_MESSAGES);
        const messages = [
            { role: 'system', content: CONFIG.SYSTEM_MESSAGE },
            ...recentHistory
        ];

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            
            // Add CSRF token if available (for web routes, API routes don't need it)
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await fetch(CONFIG.API_ENDPOINT, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    model: ChatState.model,
                    temperature: ChatState.temperature,
                    messages: messages
                }),
                signal: ChatState.abortController.signal
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                const errorMsg = errorData.content || `Server error (${response.status}). Please try again.`;
                throw new Error(errorMsg);
            }

            const json = await response.json();
            const { think, clean } = parseThink(json.content || '');
            const final = clean || json.content || 'No response received';
            const withThink = think
                ? `${final}\n\n<think>\n${think}\n</think>`
                : final;

            ChatState.history.push({ role: 'assistant', content: withThink });
            saveToLocalStorage();
            renderMessages();

        } catch (error) {
            if (error.name === 'AbortError') {
                console.log('Request cancelled');
                return;
            }

            console.error('Chat error:', error);
            const errorMsg = error instanceof Error
                ? error.message
                : 'Connection error. Please check your internet.';

            ChatState.history.push({
                role: 'assistant',
                content: `❌ Error: ${errorMsg}\n\nPlease try again or start a new chat.`
            });
            saveToLocalStorage();
            renderMessages();
        } finally {
            ChatState.isLoading = false;
            ChatState.abortController = null;
            updateUI();
        }
    }

    // UI Updates
    function updateUI() {
        // Update send button
        if (elements.sendBtn) {
            const canSend = elements.input.value.trim() && !ChatState.isLoading;
            elements.sendBtn.disabled = !canSend;
            elements.sendBtn.textContent = ChatState.isLoading ? '...' : '➤';
        }

        // Update input
        if (elements.input) {
            elements.input.disabled = ChatState.isLoading;
            elements.input.style.opacity = ChatState.isLoading ? '0.7' : '1';
            elements.input.style.backgroundColor = ChatState.isLoading ? '#f9fafb' : '#ffffff';
        }

        // Update loading indicator
        if (elements.loading) {
            elements.loading.style.display = ChatState.isLoading ? 'flex' : 'none';
        }
    }

    // Event Handlers
    function handleSend() {
        sendMessage();
    }

    function handleInputKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const canSend = elements.input.value.trim() && !ChatState.isLoading;
            if (canSend) {
                sendMessage();
            }
        }
    }

    function handleToggle() {
        ChatState.isOpen = !ChatState.isOpen;
        if (elements.panel) {
            if (ChatState.isOpen) {
                elements.panel.classList.add('chatbot-open');
                elements.toggleBtn.textContent = '✕';
                elements.toggleBtn.classList.add('chatbot-open');
                // Focus input when opened
                setTimeout(() => {
                    if (elements.input) {
                        elements.input.focus();
                    }
                }, 100);
            } else {
                elements.panel.classList.remove('chatbot-open');
                elements.toggleBtn.textContent = '💬';
                elements.toggleBtn.classList.remove('chatbot-open');
            }
        }
    }

    function handleClose() {
        ChatState.isOpen = false;
        if (elements.panel) {
            elements.panel.classList.remove('chatbot-open');
            elements.toggleBtn.textContent = '💬';
            elements.toggleBtn.classList.remove('chatbot-open');
        }
    }

    function handleClear() {
        if (ChatState.abortController) {
            ChatState.abortController.abort();
        }
        ChatState.history = [];
        ChatState.isLoading = false;
        localStorage.removeItem('chatbot_hist');
        renderMessages();
        updateUI();
    }

    // Initialize
    function init() {
        // Check if elements exist
        if (!elements.container || !elements.panel || !elements.messages) {
            console.error('Chatbot: Required DOM elements not found');
            return;
        }

        // Render initial messages
        renderMessages();

        // Attach event listeners
        if (elements.sendBtn) {
            elements.sendBtn.addEventListener('click', handleSend);
        }

        if (elements.input) {
            elements.input.addEventListener('keydown', handleInputKeyDown);
            elements.input.addEventListener('input', updateUI);
        }

        if (elements.toggleBtn) {
            elements.toggleBtn.addEventListener('click', handleToggle);
        }

        if (elements.closeBtn) {
            elements.closeBtn.addEventListener('click', handleClose);
        }

        if (elements.newBtn) {
            elements.newBtn.addEventListener('click', handleClear);
        }

        // Initial UI update
        updateUI();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose to global scope if needed
    window.Chatbot = {
        open: function() {
            if (!ChatState.isOpen) {
                handleToggle();
            }
        },
        close: function() {
            if (ChatState.isOpen) {
                handleToggle();
            }
        },
        clear: handleClear,
        getState: function() {
            return {
                model: ChatState.model,
                temperature: ChatState.temperature,
                historyLength: ChatState.history.length,
                isLoading: ChatState.isLoading
            };
        }
    };

})();

