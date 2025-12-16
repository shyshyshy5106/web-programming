// Small utilities for table accessibility and interactions
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    // Make truncate cells focusable for keyboard users
    document.querySelectorAll('.truncate').forEach(function(el){
      if(!el.hasAttribute('tabindex')) el.setAttribute('tabindex','0');
      // Add key handler to copy the text into title for mobile tooltip fallback on Enter
      el.addEventListener('keydown', function(e){
        if(e.key === 'Enter' || e.key === ' '){
          // Toggle an aria-expanded attribute to let CSS handle focus state if needed
          var expanded = el.getAttribute('aria-expanded') === 'true';
          el.setAttribute('aria-expanded', (!expanded).toString());
        }
      });
    });

    // Small helper to ensure delete modal works for dynamically added delete buttons
    document.querySelectorAll('button[data-delete-url]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        var url = btn.getAttribute('data-delete-url');
        var msg = btn.getAttribute('data-delete-msg') || 'Are you sure?';
        var modal = document.getElementById('confirm-delete-modal');
        if(modal){
          document.getElementById('confirm-delete-msg').textContent = msg;
          var go = document.getElementById('confirm-delete-go');
          go.setAttribute('href', url);
          modal.style.display = 'flex';
        }
      });
    });

  });
})();