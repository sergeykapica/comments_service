<?
namespace ControllerUrls;

function getUrlHandlers()
{
	$urlHandler = array(
		'index',
        'ajax_comment_add_handler',
        'ajax_comment_assorted_list',
        'admin_authorizate',
        'admin_authorizate_handler',
        'admin_panel',
        'admin_comment_detail',
        'admin_comment_edit_handler',
        'admin_comments_delete_handler',
        'admin_logout'
	);
	
	return $urlHandler;
}
?>