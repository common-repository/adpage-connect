jQuery(document).ready(function($) {
   
    $('.connect-campaign').click(function(e) {
        
        e.preventDefault();
       
        var hash = $(this).attr('href');
        
        $('.adpgc-modals').fadeIn(250, function() {
            $('.adpgc-modal[data-campaign="' + hash + '"]').fadeIn(250);
        });
        
    });
    
    $('.disconnect-campaign').click(function(e) {
        
        e.preventDefault();
       
        $.ajax({
            method: 'POST',
            url: '/adpgc/disconnect',
            data: {
                hash: $(this).attr('href')
            },
            dataType: 'json',
            success: function(response) {
                
                if (response.ok == true) {
                    
                    alert('Campaign disconnected successfully!');
                    
                    location.reload();
                    
                }
                else {
                    
                    alert('Whoops! An error occurred: ' + response.data.error)
                    
                }
                
            }
        });
        
    });
    
    $('.adpgc-modal input[name="slug"]').on('input', function() {
        
        $(this).parent().find('.slug-preview span').text(
            $(this).val()
        );
        
    });
    
    $('.adpgc-modal form').keypress(function(e) {
        
        if (e.which == 13) {

            return false;
            
        }
        
    });
    
    $('.adpgc-modal form').on('submit', function(e) {
        
        e.preventDefault();
        
        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                
                if (response.ok == true) {
                    
                    alert('Campaign connected successfully!');
                    
                    $('.adpgc-modals').fadeOut(250);
                    $('.adpgc-modals .adpgc-modal').hide();
                    
                    location.reload();
                    
                }
                else {
                    
                    alert('Whoops! An error occurred: ' + response.data.error)
                    
                }
                
            }
        });
        
    });
    
    $('.adpgc-modal-close').click(function() {
        
        $('.adpgc-modals').fadeOut(250);
        $('.adpgc-modals .adpgc-modal').hide();
        
    });
    
});