function save_options() {
  var counter = document.getElementById('counter').value;

  chrome.storage.local.set({
    counter: counter,
  }, function() {
    
    var status = document.getElementById('status');
    status.textContent = 'Options saved.';

    setTimeout(function() {
      status.textContent = '';
    }, 750);
  });
}

function restore_options() {
  chrome.storage.local.get({
    counter: 1,
  }, function(items) {
    document.getElementById('counter').value = items.counter;
  });
}

document.addEventListener('DOMContentLoaded', restore_options);

document.getElementById('save').addEventListener('click', save_options);