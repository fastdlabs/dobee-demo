(function () {
  'use strict';

  var config = window.__DEMO_RUNTIME__ || {};
  var storageKey = 'fastd.demo.runtime';
  var query = new URLSearchParams(location.search);
  var mode = query.get('api');

  try {
    mode = mode || localStorage.getItem(storageKey);
  } catch (error) {
    // localStorage may be unavailable; fall back to current runtime.
  }

  if (mode !== 'swoole') {
    mode = 'cgi';
  }

  function apiBase() {
    if (mode === 'swoole') {
      return String(config.swoole_base || location.origin).replace(/\/$/, '');
    }

    return location.origin.replace(/\/$/, '');
  }

  function apiUrl(path) {
    if (/^https?:\/\//i.test(path)) {
      return path;
    }

    return apiBase() + (path.charAt(0) === '/' ? path : '/' + path);
  }

  function currentRuntimeLabel() {
    var runtime = String(config.current || '').toLowerCase();
    if (runtime === 'swoole') {
      return 'Swoole';
    }
    if (runtime === 'fastcgi') {
      return 'CGI / FastCGI';
    }
    return runtime || 'unknown';
  }

  function createPanel() {
    if (document.getElementById('demo-runtime-info')) {
      return;
    }

    var panel = document.createElement('div');
    panel.id = 'demo-runtime-info';
    panel.style.cssText = [
      'position:fixed',
      'right:14px',
      'bottom:14px',
      'z-index:2147483647',
      'max-width:min(560px,calc(100vw - 28px))',
      'background:rgba(2,6,23,.94)',
      'border:1px solid #334155',
      'border-radius:8px',
      'padding:9px 12px',
      'font:12px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
      'color:#cbd5e1',
      'box-shadow:0 8px 24px rgba(0,0,0,.35)',
      'pointer-events:none',
      'white-space:nowrap',
      'overflow:hidden',
      'text-overflow:ellipsis'
    ].join(';');

    panel.innerHTML =
      '<div>页面：<b id="demo-runtime-page"></b></div>' +
      '<div>API：<b id="demo-runtime-api"></b></div>' +
      '<div>最近请求：<span id="demo-runtime-last">-</span></div>';

    document.body.appendChild(panel);
  }

  var lastRequestText = '-';

  function updatePanel(lastRequest, status) {
    var page = document.getElementById('demo-runtime-page');
    var api = document.getElementById('demo-runtime-api');
    var last = document.getElementById('demo-runtime-last');

    if (!page || !api || !last) {
      return;
    }

    page.textContent = currentRuntimeLabel() + ' ' + location.origin;
    api.textContent = (mode === 'swoole' ? 'Swoole' : 'CGI') + ' ' + apiBase();

    if (typeof lastRequest !== 'undefined') {
      lastRequestText = lastRequest + (status ? ' → HTTP ' + status : '');
    }

    last.textContent = lastRequestText;
  }

  function applyApiLinks() {
    if (mode !== 'swoole') {
      return;
    }

    document.querySelectorAll('a[href^="/"]').forEach(function (link) {
      var url = new URL(link.getAttribute('href'), location.origin);
      if (url.pathname.indexOf('/demo/') === 0) {
        return;
      }
      link.href = apiBase() + url.pathname + url.search + url.hash;
    });
  }

  window.demoRuntime = {
    get mode() {
      return mode;
    },
    setMode: function (next) {
      mode = next === 'swoole' ? 'swoole' : 'cgi';
      try {
        localStorage.setItem(storageKey, mode);
      } catch (error) {
        // ignore storage errors
      }
      applyApiLinks();
      updatePanel();
    },
    apiBase: apiBase,
    apiUrl: apiUrl
  };

  var nativeFetch = window.fetch ? window.fetch.bind(window) : null;

  if (nativeFetch) {
    window.fetch = function (input, init) {
      var actualInput = input;
      var method = (init && init.method) || (window.Request && input instanceof Request ? input.method : 'GET');

      if (typeof actualInput === 'string' && actualInput.charAt(0) === '/' && actualInput.indexOf('/demo/') !== 0) {
        actualInput = apiUrl(actualInput);
        if (mode === 'swoole') {
          init = init || {};
          if (typeof init.credentials === 'undefined') {
            init.credentials = 'include';
          }
        }
      }

      updatePanel(method + ' ' + (typeof actualInput === 'string' ? actualInput : actualInput.url));

      return nativeFetch(actualInput, init).then(function (response) {
        updatePanel(method + ' ' + (typeof actualInput === 'string' ? actualInput : actualInput.url), response.status);
        return response;
      });
    };
  }

  var NativeEventSource = window.EventSource;

  if (NativeEventSource) {
    window.EventSource = function (url, options) {
      if (typeof url === 'string' && url.charAt(0) === '/' && mode === 'swoole') {
        url = apiUrl(url);
        options = options || {};
        options.withCredentials = true;
      }

      updatePanel('GET ' + url);

      return new NativeEventSource(url, options);
    };
    window.EventSource.prototype = NativeEventSource.prototype;
  }

  function boot() {
    createPanel();
    updatePanel();
    applyApiLinks();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
