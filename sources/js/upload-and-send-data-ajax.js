(function()
{
    function uploadAndSendData(url, data, successFunction, oValidator, thisForm, spinner)
    { 
        spinner.removeClass('spinner-border-hide');
        spinner.addClass('spinner-border-show');
        
        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        
        xhr.onload = $.proxy(successFunction, { xhr: xhr, oValidator: oValidator, thisForm: thisForm, spinner: spinner });
        
        xhr.open('POST', url, true);
        xhr.send(data);
    }
    
    window.uploadAndSendData = uploadAndSendData;
})();