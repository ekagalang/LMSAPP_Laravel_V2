document.addEventListener("DOMContentLoaded", () => {
    const sendBtn = document.getElementById("send-button");
    const messageInput = document.getElementById("message-input");
    const chatContainer = document.getElementById("chat-container");
    
    if (!sendBtn || !messageInput || !chatContainer) {
        console.warn("⚠️ ChatComponent: element tidak ditemukan");
        return;
    }

    const chatId = chatContainer.dataset.chatId;

    sendBtn.addEventListener("click", async () => {
        const content = messageInput.value.trim();
        if (!content) return;

        try {
            const response = await fetch(`/chats/${chatId}/messages`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            });

            if (response.ok) {
                messageInput.value = "";
            } else {
                const err = await response.json();
                console.error("Gagal kirim pesan", err);
            }
        } catch (error) {
            console.error("Error kirim pesan", error);
        }
    });
});
