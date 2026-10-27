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
    },
    apiBase: apiBase,
    apiUrl: apiUrl
  };

  var nativeFetch = window.fetch ? window.fetch.bind(window) : null;

  if (nativeFetch) {
    window.fetch = function (input, init) {
      if (typeof input === 'string' && input.charAt(0) === '/' && input.indexOf('/demo/') !== 0) {
        input = apiUrl(input);
        if (mode === 'swoole') {
          init = init || {};
          if (typeof init.credentials === 'undefined') {
            init.credentials = 'include';
          }
        }
      }

      return nativeFetch(input, init);
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

      return new NativeEventSource(url, options);
    };
    window.EventSource.prototype = NativeEventSource.prototype;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyApiLinks);
  } else {
    applyApiLinks();
  }
})();
