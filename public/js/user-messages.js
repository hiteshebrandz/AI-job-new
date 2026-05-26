(function () {
    const container = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    if (!container || !form) return;

    const listUrl = container.getAttribute('data-list-url');
    const sendUrl = container.getAttribute('data-send-url');
    const readUrl = container.getAttribute('data-read-url');
    let lastId = parseInt(container.getAttribute('data-last-id'), 10) || 0;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function appendMessage(msg) {
        if (document.querySelector(`[data-id="${msg.id}"]`)) return;
        const div = document.createElement('div');
        div.className = 'chat-bubble ' + (msg.is_mine ? 'chat-bubble-mine' : 'chat-bubble-theirs');
        div.setAttribute('data-id', msg.id);
        const date = msg.created_at ? new Date(msg.created_at).toLocaleString() : '';
        div.innerHTML = '<p class="text-sm whitespace-pre-wrap"></p><p class="text-[10px] mt-1 opacity-70"></p>';
        div.querySelector('p').textContent = msg.body;
        div.querySelectorAll('p')[1].textContent = date;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        lastId = Math.max(lastId, msg.id);
    }

    function poll() {
        fetch(listUrl + '?after_id=' + lastId, {
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf || '' },
        })
            .then((r) => r.json())
            .then((data) => {
                (data.messages || []).forEach(appendMessage);
            })
            .catch(() => {});
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const body = (input?.value || '').trim();
        if (!body) return;

        fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf || '',
            },
            body: JSON.stringify({ body }),
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.message) appendMessage(data.message);
                if (input) input.value = '';
            });
    });

    if (readUrl) {
        fetch(readUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf || '' } });
    }

    setInterval(poll, 5000);
    container.scrollTop = container.scrollHeight;
})();
