/* AI Chatbot JS */
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.ai-chatbot-toggle');
    const window = document.querySelector('.ai-chatbot-window');
    const closeBtn = document.querySelector('.ai-chatbot-close');
    const sendBtn = document.querySelector('.ai-chatbot-send');
    const input = document.querySelector('.ai-chatbot-input input');
    const messages = document.querySelector('.ai-chatbot-messages');
    const typingIndicator = document.querySelector('.typing-indicator');

    if (!toggle) return;

    toggle.addEventListener('click', () => {
        window.classList.toggle('active');
        if (window.classList.contains('active')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        window.classList.remove('active');
    });

    const addMessage = (text, sender) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', sender);
        msgDiv.textContent = text;
        messages.appendChild(msgDiv);
        messages.scrollTop = messages.scrollHeight;
    };

    const sendMessage = async () => {
        const text = input.value.trim();
        if (!text) return;

        addMessage(text, 'user');
        input.value = '';
        typingIndicator.style.display = 'block';
        messages.scrollTop = messages.scrollHeight;

        try {
            const response = await fetch('/api/ai_chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await response.json();
            typingIndicator.style.display = 'none';

            if (data.response) {
                addMessage(data.response, 'bot');
            } else if (data.error) {
                addMessage('Lỗi: ' + data.error, 'bot');
            }
        } catch (error) {
            typingIndicator.style.display = 'none';
            addMessage('Không thể kết nối với AI. Vui lòng thử lại sau.', 'bot');
            console.error('AI Chat Error:', error);
        }
    };

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});
