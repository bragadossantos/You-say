document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  // ---- Like / Dislike ----
  document.querySelectorAll('[data-reaction]').forEach(btn => {
    btn.addEventListener('click', async function () {
      if (!token) { window.location.href = '/entrar'; return; }
      const url = this.dataset.url;
      const type = this.dataset.reaction;
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ type })
      });
      if (!res.ok) return;
      const data = await res.json();
      document.getElementById('likes-count').textContent = data.likes;
      document.getElementById('dislikes-count').textContent = data.dislikes;
      document.querySelectorAll('[data-reaction]').forEach(b => b.classList.remove('active-like', 'active-dislike'));
      this.classList.add(type === 'like' ? 'active-like' : 'active-dislike');
    });
  });

  // ---- Star rating ----
  const stars = document.querySelectorAll('.star-rating .bi');
  stars.forEach(star => {
    star.addEventListener('click', async function () {
      if (!token) { window.location.href = '/entrar'; return; }
      const container = this.closest('.star-rating');
      const url = container.dataset.url;
      const value = this.dataset.value;
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ stars: value })
      });
      if (!res.ok) return;
      const data = await res.json();
      container.querySelectorAll('.bi').forEach((s, i) => {
        s.classList.toggle('bi-star-fill', i < value);
        s.classList.toggle('bi-star', i >= value);
        s.classList.toggle('active', i < value);
      });
      document.getElementById('rating-average').textContent = data.average;
      document.getElementById('rating-total').textContent = data.total;
    });
  });

  // ---- Share ----
  document.querySelectorAll('[data-share]').forEach(btn => {
    btn.addEventListener('click', async function () {
      const platform = this.dataset.share;
      const pageUrl = window.location.href;
      const title = document.title;
      let shareUrl = null;

      if (platform === 'whatsapp') shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' - ' + pageUrl)}`;
      if (platform === 'facebook') shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(pageUrl)}`;
      if (platform === 'x') shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(pageUrl)}`;

      if (platform === 'link') {
        navigator.clipboard.writeText(pageUrl);
        this.innerHTML = '<i class="bi bi-check2"></i> Link copiado';
      } else if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=500');
      }

      const registerUrl = this.dataset.registerUrl;
      if (registerUrl && token) {
        fetch(registerUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ platform })
        });
      }
    });
  });

  // ---- Search toggle (mobile) ----
  const searchToggle = document.getElementById('searchToggle');
  const searchBox = document.getElementById('searchBox');
  if (searchToggle && searchBox) {
    searchToggle.addEventListener('click', () => searchBox.classList.toggle('d-none'));
  }
});
