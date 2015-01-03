/* Ajax 评论翻页 */
$(document).on("click", "#commentnav a",//评论翻页标签名
    function() {
        var baseUrl = $(this).attr("href"),
        commentsHolder = $("#comments-body"),//评论内容容器名，要包住评论内容和分页菜单
        id = $(this).parent().data("post-id"),
        page = 1,
        concelLink = $("#cancel-comment-reply-link");
        /comment-page-/i.test(baseUrl) ? page = baseUrl.split(/comment-page-/i)[1].split(/(\/|#|&).*$/)[0] : /cpage=/i.test(baseUrl) && (page = baseUrl.split(/cpage=/)[1].split(/(\/|#|&).*$/)[0]);
        concelLink.click();
        var ajax_data = {
            action: "ajax_comment_page_nav",
            um_post: id,
            um_page: page
        };
        commentsHolder.html('<div id="loading-comments"></div>')
        $.post(bigfa_Ajax_Url.um_ajaxurl, ajax_data,
        function(data) {
            commentsHolder.html(data);
            $("body, html").animate({
                scrollTop: commentsHolder.offset().top - 50
            },
            1e3)
        });
        return false;
    });

jQuery(document).ready(
  function(){
    $(".mobile-nav-panel").click(function() {
      $(".nav").toggleClass("active")
    });
  }
);

//点击加载文章
jQuery(document).on("click", "#show-more",
function() {
    if (jQuery(this).hasClass('is-loading')) {
        return false;
    }
     else {
        var paged = jQuery(this).data("paged"),
        total = jQuery(this).data("total");
        var ajax_data = {
            action: "ajax_index_post",
            paged: paged,
            total: total
        };
        jQuery(this).html('loading...').addClass('is-loading')
         jQuery.post('/wp-admin/admin-ajax.php', ajax_data,
        function(data) {
            jQuery('#show-more').remove();
            jQuery("#main").append(data);//这里是包裹文章的容器名
        });
        return false;
    }
});