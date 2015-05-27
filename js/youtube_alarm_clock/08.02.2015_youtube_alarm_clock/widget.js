document.addEventListener('DOMContentLoaded', function() {

    chrome.runtime.sendMessage({message: 'update'}, function() {});

    var body = document.getElementsByTagName('body')[0];

    body.addEventListener('mouseover', function() {
        this.style.opacity = 1;
        this.style.cursor = 'pointer';
    });
    body.addEventListener('mouseout', function() {
        this.style.opacity = 0.6;
        this.style.cursor = 'default';
    });
    
    var content = document.getElementById('start_w');
    
    content.addEventListener('click', function(){
        var url = chrome.extension.getURL("start_widget.html");
        window.open(url);
    });
});


