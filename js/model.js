//@TODO check for window size modification
//@TODO add close button
//@TODO ensure padding of surrounding element

/**
 * clears the mask for the modal window and removes style for the body element
 *
 */
 
 function removeMask(mask){
    jQuery('body').removeAttr('style');
    mask.hide('200',function(){jQuery(this).remove();});
}

/**
 * add mask for the modal window
 *
 * @TODO fix redirect if on the same page - for later usage
 */
function addMask(element){
    var body = jQuery('body');
    var mask =  jQuery(document.createElement('div'));
    var modalWindow =  jQuery(document.createElement('div'));
    mask.addClass('modal-window-background').css({'position' : 'absolute', 'overflow' : 'auto','top' : window.pageYOffset,'width' : '100%', 'height' : '100%', 'background-color' : 'rgba(255,255,255,0.9)', 'z-index' : '100'});
    body.css({'height' : mask.height(), 'overflow' : 'hidden'});
    body.prepend(mask);
    modalWindow.addClass('modal-window-container').css({'width' : '745px', 'overflow' : 'auto','padding' : '15px', 'background-color' : 'white', 'margin' : '60px auto'});
    modalWindow.append(element);
    mask.append(modalWindow);
    jQuery(document).delegate('.modal-window-background','click',function(event){
        if(this === event.target){
            removeMask(mask);
        }
    });
    return mask;
}
