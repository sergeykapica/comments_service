(function()
{
    window.moveToElement = function(moveRoadElement, element, duration)
    {
        moveRoadElement.animate({
            scrollTop: element.offset().top + 'px'
        },
        {
            duration: duration
        });
    }
})();