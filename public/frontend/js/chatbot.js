/**
 * AI Chatbot - JavaScript Implementation
 * Converted from React + TypeScript to Vanilla JavaScript for Laravel Blade
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        API_ENDPOINT: '/chat/send'
    };

    // State management
    const ChatState = {
        chatId: localStorage.getItem('chatbot_chat_id') || null,
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
        if (ChatState.chatId) {
            localStorage.setItem('chatbot_chat_id', ChatState.chatId);
        }
        localStorage.setItem('chatbot_hist', JSON.stringify(ChatState.history));
    }

    function scrollToBottom() {
        setTimeout(() => {
            if (elements.messages) {
                elements.messages.scrollTop = elements.messages.scrollHeight;
            }
        }, 100);
    }

    // Create product card element
    function createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'chatbot-product-card';
        
        const title = document.createElement('div');
        title.className = 'chatbot-product-title';
        title.textContent = product.name || product.title || 'Sản phẩm';
        card.appendChild(title);
        
        const details = document.createElement('div');
        details.className = 'chatbot-product-details';
        
        // Brand and Category
        if (product.brand || product.category) {
            const brandCat = document.createElement('div');
            brandCat.className = 'chatbot-product-brand';
            brandCat.textContent = [product.brand, product.category].filter(Boolean).join(' • ');
            details.appendChild(brandCat);
        }
        
        // Price
        const priceDiv = document.createElement('div');
        priceDiv.className = 'chatbot-product-price';
        const finalPrice = product.final_price || product.price || 0;
        const priceText = new Intl.NumberFormat('vi-VN', { 
            style: 'currency', 
            currency: 'VND' 
        }).format(finalPrice);
        priceDiv.textContent = priceText;
        if (product.discount) {
            const discountBadge = document.createElement('span');
            discountBadge.className = 'chatbot-product-discount';
            discountBadge.textContent = ` -${product.discount}%`;
            priceDiv.appendChild(discountBadge);
        }
        details.appendChild(priceDiv);
        
        // Size and Stock
        const metaDiv = document.createElement('div');
        metaDiv.className = 'chatbot-product-meta';
        const metaItems = [];
        if (product.size) {
            metaItems.push(`Size: ${product.size}`);
        }
        if (product.stock !== undefined) {
            metaItems.push(product.stock > 0 ? `Còn hàng: ${product.stock}` : 'Hết hàng');
        }
        if (metaItems.length > 0) {
            metaDiv.textContent = metaItems.join(' • ');
            details.appendChild(metaDiv);
        }
        
        card.appendChild(details);
        
        // View button
        if (product.url) {
            const viewBtn = document.createElement('a');
            viewBtn.href = product.url;
            viewBtn.className = 'chatbot-product-btn';
            viewBtn.textContent = 'Xem sản phẩm';
            viewBtn.target = '_blank';
            card.appendChild(viewBtn);
        }
        
        return card;
    }

    // Parse products from text (fallback)
    function parseProductsFromText(text) {
        const products = [];
        const lines = text.split('\n');
        
        lines.forEach(line => {
            // Pattern: "- Product Name (Brand, Category) — Price — Link: url"
            const match = line.match(/^-\s*(.+?)\s*\(([^)]+)\)\s*—\s*([^—]+)\s*—\s*Link:\s*(https?:\/\/[^\s]+)/i);
            if (match) {
                const [, name, brandCat, priceInfo, url] = match;
                const [brand, category] = brandCat.split(',').map(s => s.trim());
                const priceMatch = priceInfo.match(/([\d.,]+)/);
                const price = priceMatch ? parseFloat(priceMatch[1].replace(/[,.]/g, '')) : 0;
                
                products.push({
                    name: name.trim(),
                    title: name.trim(),
                    brand: brand,
                    category: category,
                    price: price,
                    final_price: price,
                    url: url.trim()
                });
            }
        });
        
        return products;
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
            
            if (isUser) {
                bubble.textContent = clean || msg.content || '';
            } else {
                // For assistant messages, parse and render with products
                const textContent = clean || msg.content || '';
                const products = msg.meta?.products || [];
                
                // Remove product links from text (they'll be shown as cards)
                const textWithoutLinks = textContent.replace(/Link:\s*https?:\/\/[^\s]+/gi, '').trim();
                
                if (textWithoutLinks) {
                    const textDiv = document.createElement('div');
                    textDiv.className = 'chatbot-message-text';
                    textDiv.textContent = textWithoutLinks;
                    bubble.appendChild(textDiv);
                }
                
                // Add product cards if available
                if (products && products.length > 0) {
                    const productsContainer = document.createElement('div');
                    productsContainer.className = 'chatbot-products-container';
                    
                    products.forEach(product => {
                        const productCard = createProductCard(product);
                        productsContainer.appendChild(productCard);
                    });
                    
                    bubble.appendChild(productsContainer);
                } else {
                    // Fallback: if no products in meta, try to parse from text
                    const parsedProducts = parseProductsFromText(textContent);
                    if (parsedProducts.length > 0) {
                        const productsContainer = document.createElement('div');
                        productsContainer.className = 'chatbot-products-container';
                        
                        parsedProducts.forEach(product => {
                            const productCard = createProductCard(product);
                            productsContainer.appendChild(productCard);
                        });
                        
                        bubble.appendChild(productsContainer);
                    }
                }
            }

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

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            
            // Add CSRF token if available
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const payload = {
                text: inputText
            };
            
            if (ChatState.chatId) {
                payload.chat_id = ChatState.chatId;
            }

            const response = await fetch(CONFIG.API_ENDPOINT, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload),
                signal: ChatState.abortController.signal
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                const errorMsg = errorData.message || errorData.reply || `Server error (${response.status}). Please try again.`;
                throw new Error(errorMsg);
            }

            const json = await response.json();
            
            // Update chat ID if provided
            if (json.chat_id) {
                ChatState.chatId = json.chat_id;
                saveToLocalStorage();
            }

            // Add assistant reply to history with products in meta
            const reply = json.reply || 'No response received';
            ChatState.history.push({ 
                role: 'assistant', 
                content: reply,
                meta: {
                    products: json.matched_products || []
                }
            });
            
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
                content: `❌ Lỗi: ${errorMsg}\n\nVui lòng thử lại hoặc bắt đầu cuộc trò chuyện mới.`
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
        ChatState.chatId = null;
        ChatState.isLoading = false;
        localStorage.removeItem('chatbot_hist');
        localStorage.removeItem('chatbot_chat_id');
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
                chatId: ChatState.chatId,
                historyLength: ChatState.history.length,
                isLoading: ChatState.isLoading
            };
        }
    };

})();

