/* Admin AI Chatbot JS */
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.ai-chatbot-container');
    const toggle = container.querySelector('.ai-chatbot-toggle');
    const windowEl = container.querySelector('.ai-chatbot-window');
    const closeBtn = container.querySelector('.ai-chatbot-close');
    const sendBtn = container.querySelector('.ai-chatbot-send');
    const input = container.querySelector('.ai-chatbot-input input');
    const messagesContainer = container.querySelector('.ai-chatbot-messages');
    const typingIndicator = container.querySelector('.typing-indicator');

    if (!toggle) return;

    toggle.addEventListener('click', () => {
        windowEl.classList.toggle('active');
        if (windowEl.classList.contains('active')) input.focus();
    });

    closeBtn.addEventListener('click', () => {
        windowEl.classList.remove('active');
    });

    const addMessage = (text, sender) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', sender);
        msgDiv.textContent = text;
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const sendMessage = async () => {
        const text = input.value.trim();
        if (!text) return;

        addMessage(text, 'user');
        input.value = '';
        typingIndicator.style.display = 'block';

        try {
            const response = await fetch('../api/ai_admin.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
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
            addMessage('Lỗi kết nối AI.', 'bot');
        }
    };

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});
