<script type="text/javascript">
/* <![CDATA[ */
    function grin(tag) {
    	var myField;
    	tag = ' ' + tag + ' ';
        if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
    		myField = document.getElementById('comment');
    	} else {
    		return false;
    	}
    	if (document.selection) {
    		myField.focus();
    		sel = document.selection.createRange();
    		sel.text = tag;
    		myField.focus();
    	}
    	else if (myField.selectionStart || myField.selectionStart == '0') {
    		var startPos = myField.selectionStart;
    		var endPos = myField.selectionEnd;
    		var cursorPos = endPos;
    		myField.value = myField.value.substring(0, startPos)
    					  + tag
    					  + myField.value.substring(endPos, myField.value.length);
    		cursorPos += tag.length;
    		myField.focus();
    		myField.selectionStart = cursorPos;
    		myField.selectionEnd = cursorPos;
    	}
    	else {
    		myField.value += tag;
    		myField.focus();
    	}
    }
/* ]]> */
</script>
<a href="javascript:grin(':?:')" title="?"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_question.gif" alt="?" /></a>
<a href="javascript:grin(':razz:')" title="razz"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_razz.gif" alt="razz" /></a>
<a href="javascript:grin(':sad:')" title="sad"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_sad.gif" alt="sad" /></a>
<a href="javascript:grin(':evil:')" title="evil"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_evil.gif" alt="evil" /></a>
<a href="javascript:grin(':!:')" title="!"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_exclaim.gif" alt="!" /></a>
<a href="javascript:grin(':smile:')" title="smile"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_smile.gif" alt="smile" /></a>
<a href="javascript:grin(':oops:')" title="oops"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_redface.gif" alt="oops" /></a>
<a href="javascript:grin(':grin:')" title="grin"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_biggrin.gif" alt="grin" /></a>
<a href="javascript:grin(':eek:')" title="eek"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_surprised.gif" alt="eek" /></a>
<a href="javascript:grin(':shock:')" title="shock"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_eek.gif" alt="shock" /></a>
<a href="javascript:grin(':???:')" title="???"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_confused.gif" alt="???" /></a>
<a href="javascript:grin(':cool:')" title="cool"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_cool.gif" alt="cool" /></a>
<a href="javascript:grin(':lol:')" title="lol"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_lol.gif" alt="lol" /></a>
<a href="javascript:grin(':mad:')" title="mad"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_mad.gif" alt="mad" /></a>
<a href="javascript:grin(':twisted:')" title="twisted"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_twisted.gif" alt="twisted" /></a>
<a href="javascript:grin(':roll:')" title="roll"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_rolleyes.gif" alt="roll" /></a>
<a href="javascript:grin(':wink:')" title="wink"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_wink.gif" alt="wink" /></a>
<a href="javascript:grin(':idea:')" title="idea"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_idea.gif" alt="idea" /></a>
<a href="javascript:grin(':arrow:')" title="arrow"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_arrow.gif" alt="arrow" /></a>
<a href="javascript:grin(':neutral:')" title="neutral"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_neutral.gif" alt="neutral" /></a>
<a href="javascript:grin(':cry:')" title="cry"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_cry.gif" alt="cry" /></a>
<a href="javascript:grin(':mrgreen:')" title="mrgreen"><img src="<?php bloginfo('template_directory'); ?>/images/smilies/icon_mrgreen.gif" alt="mrgreen" /></a>
<br />