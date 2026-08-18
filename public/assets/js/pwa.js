(() => {
  if (!('serviceWorker' in navigator)) {
    return;
  }

  const registerServiceWorker = () => {
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {});
  };

  if (document.readyState === 'complete') {
    registerServiceWorker();
  } else {
    window.addEventListener('load', registerServiceWorker, { once: true });
  }
})();
