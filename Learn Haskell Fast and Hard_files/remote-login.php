commentObj.divReply = $id('IDCommentReplyDiv');

if(readCookie('IDCShowHide')=='show' && $id('IDCinfoBarImg'))
{
	$id('idc-infobar-loading').style.display = 'block';
	$id('idc-infobar-expand-image').style.display = 'none';
	$id('idc-showhide-links').style.display = 'none';	
	var curLocation = window.location.hash;
	if(curLocation.indexOf("IDComment")>0)
		var selectedCommentID = curLocation.substr(curLocation.indexOf("IDComment")+9);	
	else
		var selectedCommentID = false;

	IDPageLoad(0, "next", selectedCommentID, false);
}

function showHideIDC(page)
{
	if($id('idc-cover').style.display == 'none')
	{
		$id('idc-infobar-loading').style.display = 'block';
		$id('idc-infobar-expand-image').style.display = 'none';
		$id('idc-showhide-links').style.display = 'none';	
		if(!page)
			var page=0;		
		IDPageLoad(0, "next", false, false);
		//rest handled in getInnerComments now

	}
	else
	{
		$id('IDCinfoBarImg').className = 'idc-infobar-expand';
		showCommentsStyle = $id('idc-showcomments-link').style.display;
		postCommentStyle = $id('idc-postcomment-link').style.display;
		IDReplaceHtml($id('idc-showcomments-link'), "Show Comments");
		$id('idc-showcomments-link').href = 'javascript:showHideIDC();';
		$id('idc-showcomments-link').style.display = showCommentsStyle;
		IDReplaceHtml($id('idc-postcomment-link'), "Show Comments");
		$id('idc-postcomment-link').href = 'javascript:showHideIDC();';
		$id('idc-postcomment-link').style.display = postCommentStyle;
		$id('idc-cover').style.display = 'none';
		if($id('idc-footer'))
			$id('idc-footer').style.display = 'block';		
		$id('IDCommentsNewThreadCover').style.display = 'none';		
		createCookie("IDCShowHide", "hide", 30);

	}
}

function showIDC(page)
{
	var date = new Date();
	date.setTime(date.getTime()+(30*24*60*60*1000));
	
	$id('IDCinfoBarImg').className = 'idc-infobar-expanded';
	$id('idc-cover').style.display = 'block';
	showCommentsStyle = $id('idc-showcomments-link').style.display;
	postCommentStyle = $id('idc-postcomment-link').style.display;
	IDReplaceHtml($id('idc-showcomments-link'), "Hide Comments");
	$id('idc-showcomments-link').href = 'javascript:showHideIDC();';
	$id('idc-showcomments-link').style.display = showCommentsStyle;
	IDReplaceHtml($id('idc-postcomment-link'), "Hide Comments");
	$id('idc-postcomment-link').href = 'javascript:showHideIDC();';
	$id('idc-postcomment-link').style.display = postCommentStyle;
	if($id('idc-footer'))
		$id('idc-footer').style.display = 'none';		
	
	if( !$id('reqUsersOn') || $id('reqUsersOn').value == 'no' || commentObj.curUser.isLoggedIn )
		$id('IDCommentsNewThreadCover').style.display = 'block';

	if(!$id('idc-req-on') || commentObj.curUser.isLoggedIn)
		$id("IDCommentNewThreadText").style.width = ($id("IDCommentsNewThread").offsetWidth - 8) +"px";
	if($id('IDCommentNewThreadText').style.display=='block')
		$id('IDCommentNewThreadText').focus();
	IDUpdateTimeStamps();
	createCookie("IDCShowHide", "show", 30);
	$id('idc-infobar-loading').style.display = 'none';
	$id('idc-infobar-expand-image').style.display = 'block';
	$id('idc-showhide-links').style.display = 'block';	

}

function showHideOpts()
{
	var div = document.getElementById('showHideAdminOpts');
	var link = document.getElementById('IDAdminOptsLink');
	if(div.style.display == '')
	{
		link.className = 'idc-collapselink_closed';
		div.style.display = 'none';
	}
	else
	{
		link.className = 'idc-collapselink';
		div.style.display = '';	
	}
}
function voteComment(commentid, vote) {
	listObj = $id("IDComment"+commentid);
	
	if(commentObj.comments[commentid] && commentObj.comments[commentid].hasVoted==true)
	{
		showMsgBox("Sorry", "<p>You've already voted on that comment.</p>", 0, listObj);
		return;
	}
	
	if(commentObj.comments[commentid] && (commentObj.comments[commentid].status==2 || commentObj.comments[commentid].status==6))
	{
		showMsgBox("Sorry", "<p>You can't vote on a deleted comment.</p>", 0, listObj);
		return;
	}
	
	listObj = $id("IDCommentVoteScore"+commentid);
	
	if(listObj.parentNode.className.indexOf("idc-disabled")>-1)
	{
		showMsgBox("Sorry", "<p>You've already voted on that comment.</p>", 0, $id("IDComment"+commentid));
		return;
	}
	
	var theComment = commentObj.comments[commentid];
	if(vote==1) theComment.votescore++; else theComment.votescore--; 
				
	if(theComment.votescore<=0)
		var voteOutput=theComment.votescore;
	else
		var voteOutput="+"+theComment.votescore;						
				
	var newListObj = $newEl('span');
	newListObj.className = "idc-v-total";
	newListObj.id = "IDCommentVoteScore"+commentid;
	newListObj.innerHTML = voteOutput;
	
	listObj.parentNode.className+=" idc-disabled"; 		
	listObj.parentNode.insertBefore(newListObj, listObj);
	listObj.parentNode.removeChild(listObj);
	
	if(commentObj.voteCommentCallback)
		var firstCall = "false";
	else
		var firstCall = "true";
	
        if( commentObj.curUser.userid )
            var userid = commentObj.curUser.userid;
        else
            var userid = 0;
            
        if( commentObj.curUser.token )
            var token = commentObj.curUser.token;
        else
            var token = '';
        
	var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "vote":'+vote+', "commentid":'+commentid+', "userid":'+userid+', "token":"'+token+'", "firstCall":'+firstCall+'}';
	var requestObj = new buildRequestObj(theStr, 1, null, connectionErr);
	xs.make_request(requestObj);		
};

function changeDisabledLink()
{	
	var checkbox = document.getElementById('chkDisableIDC');
	var link = document.getElementById('adminOptions');

	if(checkbox.checked)
	{
		//checkbox.checked='checked';
		link.href = link.href.replace(/'0'\)/, "'1'\)");
		
	}
		
	else
	{
		//checkbox.checked='';
		link.href = link.href.replace(/'1'\)/, "'0'\)");
	}
}
	
function disableComments(blogpostid, acctid, val)
{	
	IDReplaceHtml($id("adminOptions"), 'Loading...');
	$id("adminOptions").href = "javascript:void(0);";		
	
	if(!commentObj.curUser.isLoggedIn)
	{
		showMsgBox("Sorry", "<p>You must be logged in to change settings.</p>", 0, listObj);
		return;
	}
	//changeDisabledLink();	
	if(commentObj.DisableCommentsCallback)
		var firstCall = "false";
	else
		var firstCall = "true";
	
	var theStr = '"params":{"blogpostid":'+blogpostid+', "val":'+val+', "acctid":'+acctid+', "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "firstCall":'+firstCall+'}';
	var requestObj = new buildRequestObj(theStr, 13, null, connectionErr);
	xs.make_request(requestObj);		
};

function id_showFBC(src) {
	
	
	if(src == 0) {
		document.getElementById('fbIframeNT').src = "http://intensedebate.com/fb-connect/fbConnect.php?acctid=" + commentObj.acctid + "&token=" + commentObj.token;
		showFBLoginNewThread();
	} else {
		document.getElementById('fbIframeR').src = "http://intensedebate.com/fb-connect/fbConnect.php?acctid=" + commentObj.acctid + "&token=" + commentObj.token;
		showFBLoginReply();
	}
	
	if(browser == "Microsoft Internet Explorer") {
		var b_version = navigator.appVersion;
		b_version = b_version.substr(b_version.indexOf("MSIE")+ 5, 3);
		var version = parseFloat(b_version);
	
		if(version <= 6) {
			document.getElementById('fbIframeNT').style.width = "100%";
			document.getElementById('fbIframeR').style.width = "100%";
			document.getElementById('fbIframeNT').style.height = "400px";
			document.getElementById('fbIframeR').style.height = "400px";
		}
	}
	$id('IDCPostNav').style.display = "none";
	$id('IDCPostNavReply').style.display = "none";
	id_fbSartPoll();
	id_fbPoll();	
};

function id_fbPoll() {
	if( commentObj && !commentObj.fbConnect )
		setTimeout('id_fbPoll()', 1000);
	var d = new Date();
	
	var id_fbPollScript = document.createElement('SCRIPT');
	id_fbPollScript.src='http://intensedebate.com/fb-connect/getFB.php?acctid=' + commentObj.acctid + '&token=' + commentObj.token + '&time=' + d.getTime();
	document.getElementsByTagName('head')[0].appendChild(id_fbPollScript);
};

function id_fbStopPoll() {
	commentObj.fbConnect = true;
};

function id_fbSartPoll() {
	commentObj.fbConnect = false;
};

function id_show_nav() {
	$id('IDCPostNav').style.display = "block";
	$id('IDCPostNavReply').style.display = "block";
	IDCNav('IDCNavGuest');
	IDCNavReply('IDCNavGuestReply');
};

function postComment(src)
{
	if(commentObj.postCommentCallback)
		var firstCall = "false";
	else
		var firstCall = "true";
		
	if(typeof(mbl_current_visitor) == "undefined")
		var mblID = "";	
	else		
		var mblID = mbl_current_visitor;
		
	if(src==0) //new thread	
	{
		if( commentObj.postUsingTwitter ) {
			var subscribeThis = $id('IDSubscribeToThis').value;
			if($id('IDNewThreadTweetThis-tw').checked)
				var tweetThis = 'T';
			else
				var tweetThis = 'F';
			
			var idCommentText = id_apply_filter('pre_comment_text', $id('IDCommentNewThreadText').value);	
			var idCommentEmail = id_apply_filter('pre_comment_email', $id('txtEmailNewThreadTW').value);
				
			var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":0, "depth":0, "type":200, "token":"'+IDaddslashes(commentObj.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes('@' + IDC.twitter.api_response.screen_name))+'", "anonEmail":"'+IDaddslashes(idCommentEmail)+'", "anonURL":"'+IDaddslashes(IDC.twitter.link_url)+'", "exPicURL":"'+IDaddslashes(IDC.twitter.api_response.profile_image_url)+'", "userid":0, "mblid":"'+mblID+'", "tweetThis":"'+tweetThis+'", "subscribeThis":"'+subscribeThis+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';		
			
			if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
			{
				showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
				return;
			}
				
			IDReplaceHtml($id("IDNewThreadSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');
			var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
			xs.make_request(requestObj);
			
			return;
		}
		
		if( commentObj.postUsingFBC ) {
			var subscribeThis = $id('IDSubscribeToThis').value;
			
			var idCommentText = id_apply_filter('pre_comment_text', $id('IDCommentNewThreadText').value);	
			var idCommentEmail = id_apply_filter('pre_comment_email', $id('txtEmailNewThreadFB').value);
				
			var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":0, "depth":0, "type":100, "token":"'+IDaddslashes(commentObj.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes(commentObj.fbName))+'", "anonEmail":"'+IDaddslashes(idCommentEmail)+'", "anonURL":"'+IDaddslashes(commentObj.fbUrl)+'", "exPicURL":"'+IDaddslashes(commentObj.fbPic)+'", "userid":0, "mblid":"'+mblID+'", "tweetThis":"F", "subscribeThis":"'+subscribeThis+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';		
			
			if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
			{
				showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
				return;
			}
				
			IDReplaceHtml($id("IDNewThreadSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');		
			var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
			requestObj.session_hash = IDC.fbc.session_hash;
			requestObj.session_key = IDC.fbc.session_key;
			requestObj.userid = IDC.fbc.userid;
			xs.make_request(requestObj);
			
			return;
		}
                
		if ( commentObj.postUsingExauth ) {
			var subscribeThis = $id('IDSubscribeToThis').value;
			
			var idCommentText = id_apply_filter('pre_comment_text', IDC.$('IDCommentNewThreadText').value);	
			var idCommentEmail = id_apply_filter('pre_comment_email', IDC.exauth.user_email );
			var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":0, "depth":0, "type":500, "token":"'+IDaddslashes(commentObj.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes(IDC.exauth.user_name))+'", "anonEmail":"'+encodeURIComponent(IDaddslashes(idCommentEmail))+'", "anonURL":"'+encodeURIComponent(IDaddslashes(IDC.exauth.user_url))+'", "exPicURL":"'+encodeURIComponent(IDaddslashes(IDC.exauth.avatar_url))+'", "userid":0, "mblid":"'+mblID+'", "tweetThis":"F", "subscribeThis":"'+subscribeThis+'", "exauth_obj": "'+encodeURIComponent( JSON.stringify(IDC$EXAUTH) )+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';
			
			if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
			{
				showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
				return;
			}
				
			IDReplaceHtml($id("IDNewThreadSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');		
			var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
			xs.make_request(requestObj);
			
			return;
		}
		
		if(commentObj.newthreadType == 4 || commentObj.newthreadType == 5)	//OpenID
		{
			if($id('IDCommentNewThreadText').value=='Enter text right here!' || $id('IDCommentNewThreadText').value.length==0)
			{
				showMsgBox("Sorry", "<p>We're gonna need you to write a comment before you can post it.</p>", 0);
				return;
			}
		
			if(commentObj.newthreadType == 4) //signup
			{
				if($id('txtOpenIDSignupNewThreadURL').value.length == 0 || $id('txtOpenIDSignupNewThreadURL').value=="http://")				
				{
					showMsgBox("Sorry", "<p>We're gonna need you to specify an OpenID URl.</p>", 0);
					return;
				}
				
				$id('IDCommentsOpenIDSignupNewThreadComment').value = $id('IDCommentNewThreadText').value;
				$id('IDCommentsOpenIDSignupNewThreadParentid').value = 0;
				$id('IDCommentsOpenIDSignupNewThread').submit();
				return;
			}
			else //login
			{
				if($id('txtOpenIDSignupNewThreadURL').value.length == 0 || $id('txtOpenIDSignupNewThreadURL').value=="http://")				
				{
					showMsgBox("Sorry", "<p>We're gonna need you to specify an openid url.</p>", 0);
					return;
				}
				
				$id('IDCommentsOpenIDSignupNewThreadComment').value = $id('IDCommentNewThreadText').value;
				$id('IDCommentsOpenIDSignupNewThreadParentid').value = 0;
				$id('IDCommentsOpenIDSignupNewThread').submit();
				return;
			}					
		}
	
		//Check for defaults
		if($id('IDCommentNewThreadText').value=='Enter text right here!')
		{
			showMsgBox("Sorry", "<p>We're gonna need you to write a comment before you can post it.</p>", 0);
			return;
		}		
		
		if($id('txtEmailNewThread').value == '' &&  commentObj.newthreadType == 3)
		{
			showMsgBox("Sorry", "<p>In order to create an account, you need to supply a valid email address</p>", 0);
			return;
		}
		
		if($id('txtNameNewThread').value == '' && commentObj.newthreadType == 0)
		{
			showMsgBox("Sorry", "<p>Please tell us your name and then try to submit your comment again</p>", 0);
			return;
		}

		/*if($id('txtNameNewThread').value.indexOf('&')>0)
		{
			showMsgBox("Sorry", "<p>Your name can't contain a & character.</p>", 0);
			return;
		}*/			
		
		if($id('IDNewThreadTweetThis').checked)
			var tweetThis = 'T';
		else
			var tweetThis = 'F';
				
		var subscribeThis = $id('IDSubscribeToThis').value;
		
		var idCommentText = id_apply_filter('pre_comment_text', $id('IDCommentNewThreadText').value);	
		var idCommentName = id_apply_filter('pre_comment_name', $id('txtNameNewThread').value);
		var idCommentEmail = id_apply_filter('pre_comment_email', $id('txtEmailNewThread').value);
		var idCommentUrl = id_apply_filter('pre_comment_url', $id('txtURLNewThread').value);

		var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":0, "depth":0, "type":'+commentObj.newthreadType+', "token":"'+IDaddslashes(commentObj.curUser.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes(idCommentName))+'", "anonEmail":"'+IDaddslashes(idCommentEmail)+'", "anonURL":"'+IDaddslashes(idCommentUrl)+'", "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "mblid":"'+mblID+'", "tweetThis":"'+tweetThis+'", "subscribeThis":"'+subscribeThis+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';		
		
		if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
		{
			showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
			return;
		}
			
		IDReplaceHtml($id("IDNewThreadSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');		
		var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
		xs.make_request(requestObj);		
	}
	else
	{
		if( commentObj.postUsingTwitter ) {
			var subscribeThis = $id('IDSubscribeToThisReply').value;
			if($id('IDReplyTweetThis-tw').checked)
				var tweetThis = 'T';
			else
				var tweetThis = 'F';
				
			var idCommentText = id_apply_filter('pre_comment_text', $id('txtComment').value);	
			var idCommentEmail = id_apply_filter('pre_comment_email', $id('txtEmailReplyTW').value);
				
			var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":'+commentObj.parentid+', "depth":'+commentObj.depth+', "type":200, "token":"'+IDaddslashes(commentObj.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes('@' + IDC.twitter.api_response.screen_name))+'", "anonEmail":"'+IDaddslashes(idCommentEmail)+'", "anonURL":"'+IDaddslashes(IDC.twitter.link_url)+'", "exPicURL":"'+IDaddslashes(IDC.twitter.api_response.profile_image_url)+'", "userid":0, "mblid":"'+mblID+'", "tweetThis":"'+tweetThis+'", "subscribeThis":"'+subscribeThis+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';		
			
			if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
			{
				showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
				return;
			}
				
			IDReplaceHtml($id("IDReplyDivSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');		
			var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
			xs.make_request(requestObj);
			
			return;
		}
		
		if( commentObj.postUsingFBC ) {
			var subscribeThis = $id('IDSubscribeToThisReply').value;
			
			var idCommentText = id_apply_filter('pre_comment_text', $id('txtComment').value);	
			var idCommentEmail = id_apply_filter('pre_comment_email', $id('txtEmailReplyFB').value);
				
			var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":'+commentObj.parentid+', "depth":'+commentObj.depth+', "type":100, "token":"'+IDaddslashes(commentObj.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes(commentObj.fbName))+'", "anonEmail":"'+IDaddslashes(idCommentEmail)+'", "anonURL":"'+IDaddslashes(commentObj.fbUrl)+'", "exPicURL":"'+IDaddslashes(commentObj.fbPic)+'", "userid":0, "mblid":"'+mblID+'", "tweetThis":"F", "subscribeThis":"'+subscribeThis+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';		
			
			if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
			{
				showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
				return;
			}
				
			IDReplaceHtml($id("IDReplyDivSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');		
			var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
			requestObj.session_hash = IDC.fbc.session_hash;
			requestObj.session_key = IDC.fbc.session_key;
			requestObj.userid = IDC.fbc.userid;
			xs.make_request(requestObj);
			
			return;
		}
                
		if( commentObj.postUsingExauth ) {
			var subscribeThis = $id('IDSubscribeToThisReply').value;
			
			var idCommentText = id_apply_filter('pre_comment_text', IDC.$('txtComment').value);	
			var idCommentEmail = id_apply_filter('pre_comment_email', IDC.exauth.user_email );
			var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":'+commentObj.parentid+', "depth":'+commentObj.depth+', "type":500, "token":"'+IDaddslashes(commentObj.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes(IDC.exauth.user_name))+'", "anonEmail":"'+encodeURIComponent(IDaddslashes(idCommentEmail))+'", "anonURL":"'+encodeURIComponent(IDaddslashes(IDC.exauth.user_url))+'", "exPicURL":"'+encodeURIComponent(IDaddslashes(IDC.exauth.avatar_url))+'", "userid":0, "mblid":"'+mblID+'", "tweetThis":"F", "subscribeThis":"'+subscribeThis+'", "exauth_obj": "'+encodeURIComponent( JSON.stringify(IDC$EXAUTH) )+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';
			
			if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
			{
				showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
				return;
			}
				
			IDReplaceHtml($id("IDNewThreadSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');		
			var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
			xs.make_request(requestObj);
			
			return;
		}
		
		if(commentObj.replyType == 4 || commentObj.replyType == 5)	//OpenID
		{
			if($id('txtComment').value=='Enter text right here!' || $id('txtComment').value.length==0)
			{
				showMsgBox("Sorry", "<p>We're gonna need you to write a comment before you can post it.</p>", 0);
				return;
			}
		
			if(commentObj.replyType == 4) //signup
			{
				if($id('txtOpenIDSignupReplyURL').value.length == 0 || $id('txtOpenIDSignupReplyURL').value=="http://")				
				{
					showMsgBox("Sorry", "<p>We're gonna need you to specify an openid url.</p>", 0);
					return;
				}
				
				$id('IDCommentsOpenIDSignupReplyComment').value = $id('txtComment').value;
				$id('IDCommentsOpenIDSignupReplyParentid').value = commentObj.parentid;
				$id('IDCommentsOpenIDSignupReply').submit();
				return;
			}
			else //login
			{
				if($id('txtOpenIDSignupReplyURL').value.length == 0 || $id('txtOpenIDSignupReplyURL').value=="http://")				
				{
					showMsgBox("Sorry", "<p>We're gonna need you to specify an openid url.</p>", 0);
					return;
				}
				
				$id('IDCommentsOpenIDSignupReplyComment').value = $id('txtComment').value;
				$id('IDCommentsOpenIDSignupReplyParentid').value = commentObj.parentid;
				$id('IDCommentsOpenIDSignupReply').submit();
				return;
			}					
		}
		
		//Check for defaults
		if($id('txtComment').value=='Enter text right here!')
		{
			showMsgBox("Sorry", "<p>We're gonna need you to write a comment before you can post it.</p>", 0);
			return;
		}		
		
		if($id('txtEmailReply').value == '' &&  commentObj.replyType == 3)
		{
			showMsgBox("Sorry", "<p>In order to create an account, you need to supply a valid email address</p>", 0);
			return;
		}
		
		if($id('txtNameReply').value == '' && commentObj.replyType == 0)
		{
			showMsgBox("Sorry", "<p>Please tell us your name and then try to submit your comment again</p>", 0);
			return;
		}
		
		if($id('IDReplyTweetThis').checked)
			var tweetThis = 'T';
		else
			var tweetThis = 'F';
		
		var subscribeThis = $id('IDSubscribeToThisReply').value;

		var idCommentText = id_apply_filter('pre_comment_text', $id('txtComment').value);
		var idCommentName = id_apply_filter('pre_comment_name', $id('txtNameReply').value);
		var idCommentEmail = id_apply_filter('pre_comment_email', $id('txtEmailReply').value);
		var idCommentUrl = id_apply_filter('pre_comment_url', $id('txtURLReply').value);

		var theStr = '"params":{ "firstCall":'+firstCall+', "src":'+src+', "blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "parentid":'+commentObj.parentid+', "depth":'+commentObj.depth+', "type":'+commentObj.replyType+', "token":"'+IDaddslashes(commentObj.curUser.token)+'", "anonName":"'+encodeURIComponent(IDaddslashes(idCommentName))+'", "anonEmail":"'+IDaddslashes(idCommentEmail)+'", "anonURL":"'+IDaddslashes(idCommentUrl)+'", "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "mblid":"'+mblID+'", "tweetThis":"'+tweetThis+'", "subscribeThis":"'+subscribeThis+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes(idCommentText))).replace(/&/g, "%26")+'"}';
		
		if(theStr.length>7900 || (browser == "Microsoft Internet Explorer" && theStr.length>2050))
		{
			showMsgBox("Sorry", "<p>Your comment is a little too long.  Try splitting it into multiple comments.");
			return;
		}
		
		IDReplaceHtml($id("IDReplyDivSubmitLI"), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');
		
		var requestObj = new buildRequestObj(theStr, 0, null, connectionErr);
		xs.make_request(requestObj);		
		
		//d = new Date();
		//IDLastPostTime = d.getTime(); 
	}
};

function forgotPassword()
{
	if(commentObj.forgotPasswordCallback)
		var firstCall="false";
	else
		var firstCall="true";
		
	var theStr = '"params":{"email":"'+IDaddslashes($id('txtResolveEmail').value)+'", "firstCall":'+firstCall+'}';	
	var requestObj = new buildRequestObj(theStr, 7, null, connectionErr);
	xs.make_request(requestObj);	
};

function reportThisComment(commentid)
{
	if(commentObj.ReportCommentCallback)
		var firstCall="false";
	else
		var firstCall="true";
	
	var theStr = '"params":{"commentid":"'+commentid+'", "commentAdditional":"'+IDaddslashes($id('IDCCommentAdditional').value)+'", "firstCall":'+firstCall+'}';	
	//alert(theStr);
	var requestObj = new buildRequestObj(theStr, 14, null, connectionErr);
	xs.make_request(requestObj);	
};

function chkSignupReplyClick(obj)
{
	if(obj.value==3)
	{
		obj.value=0;
		commentObj.replyType = 0;
	}	
	else
	{
		obj.value=3;
		commentObj.replyType = 3;
	}		
};

function chkSignupOpenIDReplyClick(obj)
{
	if(obj.value==4)
	{
		obj.value=5;
		commentObj.replyType = 5;
	}	
	else
	{
		obj.value=4;
		commentObj.replyType = 4;
	}		
};

/*function chkSignupOpenIDNewThreadClick(obj)
{
	if(obj.value==4)
	{
		obj.value=5;
		commentObj.newthreadType = 5;
	}	
	else
	{
		obj.value=4;
		commentObj.newthreadType = 4;
	}		
};

function chkSignupNewThreadClick(obj)
{
	if(obj.value==3)
	{
		obj.value=0;
		commentObj.newthreadType = 0;
	}	
	else
	{
		obj.value=3;
		commentObj.newthreadType = 3;
	}		
};*/

function connectionErr(obj)
{
	IDReplaceHtml($id("IDReplyDivSubmitLI"), '<a class="idc-btn_l-secondary" href="javascript: hideReply();"><span></span><span class="idc-r">Cancel</span></a><a class="idc-btn_l" href="javascript: postComment(1);"><span></span><span class="idc-r"><strong>Submit Comment</strong></span></a>');
	IDReplaceHtml($id("IDNewThreadSubmitLI"), '<a class="idc-btn_l" href="javascript: postComment(0);"><span></span><span class="idc-r"><strong>Submit Comment</strong></span></a>');
	showMsgBox("Sorry","<p>There has been a connection error.  The connection has timed out.</p>", 2);	
};

function showReply(commentid) {
	if(commentObj.parentid == commentid) {
		hideReply();
		commentObj.parentid = 0;		
		return;
	}
	
	commentObj.depth = commentObj.comments[commentid].depth+1;
	commentObj.parentid = commentid;
	
	if( commentObj.comments[commentid] && commentObj.comments[commentid].threadparentid>0 && $id("IDCommentCollapseLink"+commentObj.comments[commentid].threadparentid).className.indexOf("collapselink_closed") > 0 )
		collapseThread(commentObj.comments[commentid].threadparentid);
	else if( $id("IDCommentCollapseLink"+commentid) && $id("IDCommentCollapseLink"+commentid).className.indexOf("collapselink_closed") > 0 )
		collapseThread(commentid);
	
	if( $id("IDCommentPostReplyLink"+commentObj.parentid) )
		$id("IDCommentPostReplyLink"+commentObj.parentid).style.display="block";
	
	if( commentObj.parentid )
		IDReplaceHtml($id("IDCommentPostReplyLink" + commentObj.parentid), "<a class='idc-btn_s' href='javascript: showReply(" + commentObj.parentid + ")'><span><input type='hidden' id='reqUsersOn' value='no' /></span><span class='idc-r'>Reply</span></a>");
		
	IDReplaceHtml($id("IDCommentPostReplyLink"+commentid), "<span class='idc-btn_s'><span></span><span class='idc-r'>Posting...</span></span>");

		
	createCookie("IDReplyCommentId", commentid, 1);
	
	if($id("IDCommentReplyName") && commentObj.comments[commentid].displayName)
		IDReplaceHtml($id("IDCommentReplyName"), "Replying to #user# ".replace(/#user#/, commentObj.comments[commentid].displayName));
	
	$id("txtComment").style.width = 50 +"px";	
	if( $id("IDCommentSubThread"+commentid).childNodes.length > 0 )
		$id("IDCommentSubThread"+commentid).insertBefore(commentObj.divReply, $id("IDComment"+commentid).nextSibling.firstChild);
	else
		$id("IDCommentSubThread"+commentid).appendChild(commentObj.divReply);
		
	commentObj.divReply.style.display = "block";
	
	//$id('IDPostReplyUseOpenID').style.display = "none";
	$id('IDCommentReplyInnerDiv').style.width = (IDgetWidth(commentObj.divReply) - 18) +"px";
	$id('IDCommentsOpenIDReplyInnerDiv').style.width = (IDgetWidth(commentObj.divReply) - 18) +"px";
	
	$id("txtComment").style.width = (IDgetWidth(commentObj.divReply) - 26) +"px";
	$id("txtComment").focus();
	
	var args = new Array();
	args['commentid'] = commentid;
	id_fire_action('show_reply', args);
};

function hideReply() {
	IDReplaceHtml($id("IDCommentPostReplyLink" + commentObj.parentid), "<a class='idc-btn_s' href='javascript: showReply(" + commentObj.parentid + ")'><span><input type='hidden' id='reqUsersOn' value='no' /></span><span class='idc-r'>Reply</span></a>");
	commentObj.divReply.style.display = "none";
	commentObj.parentid = 0;
};

function IDgetWindowHeight() 
{
	 var viewportheight;
	 
	 // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
	 
	 if (typeof window.innerWidth != 'undefined')
	 {
	      viewportheight = window.innerHeight
	 }
	 
	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
	
	 else if (typeof document.documentElement != 'undefined'
	     && typeof document.documentElement.clientWidth !=
	     'undefined' && document.documentElement.clientWidth != 0)
	 {
	       viewportheight = document.documentElement.clientHeight;
	 }
	 else
	 {
	       viewportheight = document.getElementsByTagName('body')[0].clientHeight;
	 }

	return viewportheight;
};

function IDgetScrollY() 
{
	return f_filterResults (
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0
	);
};

function showMsgBox(header, text, type, objTop, otherButton)
{	
	IDReplaceHtml($id('IDCommentPopupInner'), '');
	var link1 = $newEl("a");
	link1.href="javascript: hideMsgBox();";
	link1.className = "idc-close";
		var innerSpan = $newEl("span");
		IDReplaceHtml(innerSpan,'Close');
		link1.appendChild(innerSpan);
	var h6 = $newEl("h6");
	IDReplaceHtml(h6, header);
	var pText = $newEl('div');
	
	IDReplaceHtml(pText, text);
	var p = $newEl('p');
	p.className="idc-bottom";
		var link2 = $newEl('a');
		link2.href="javascript: hideMsgBox();";
		IDReplaceHtml(link2, "<span></span><span class='idc-r'>Close Message</span>");
		link2.className="idc-btn_s";
		p.appendChild(link2);
		if( otherButton )
			p.appendChild(otherButton);
			
	$id('IDCommentPopupInner').appendChild(link1);	
	$id('IDCommentPopupInner').appendChild(h6);
	$id('IDCommentPopupInner').appendChild(pText);
	$id('IDCommentPopupInner').appendChild(p);
	
	if(browser == "Microsoft Internet Explorer")
	{			
		var b_version=navigator.appVersion;
		b_version = b_version.substr(b_version.indexOf("MSIE")+ 5, 3);
		var version=parseFloat(b_version);

		if(version<=6)
		{			
				$id('IDCommentPopup').style.top = (IDgetWindowHeight()/4 + IDgetScrollY()+"px");
		}
	}
		
	if(type==0)
		$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "").replace(/idc-success/g, "");
	else if(type==1)
		$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "").replace(/idc-success/g, "");/*$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "") + " idc-success";*/
	else
		$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "").replace(/idc-success/g, "");/*$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-success/g, "") + " idc-error";*/
		
	$id('IDCommentPopup').style.display = "block";
};

function showReportBox(commentId)
{	
	IDReplaceHtml($id('IDCommentPopupInner'), '');
	var link1 = $newEl("a");
	link1.href="javascript: hideMsgBox();";
	link1.className = "idc-close";
		var innerSpan = $newEl("span");
		IDReplaceHtml(innerSpan,'Close');
		link1.appendChild(innerSpan);
	var h6 = $newEl("h6");
	var type = 0;
	var header = 'Report this comment for a violation';
	IDReplaceHtml(h6, header);
	var pText = $newEl('div');
	var text = '<p>'+$id('IDCustomReportTxt').innerHTML+'</p><p>Reason for reporting:</p><textarea id="IDCCommentAdditional" class="idc-text_noresize" value="" style="width:90%;"></textarea><p><a href="javascript: reportThisComment('+commentId+');" class="idc-btn_s"><span></span><span class="idc-r">Report this!</span></a></p>';
	
	IDReplaceHtml(pText, text);
	var p = $newEl('p');
	p.className="idc-bottom";
		var link2 = $newEl('a');
		link2.href="javascript: hideMsgBox();";
		IDReplaceHtml(link2, "<span></span><span class='idc-r'>Close Message</span>");
		link2.className="idc-btn_s";
		p.appendChild(link2);
	$id('IDCommentPopupInner').appendChild(link1);	
	$id('IDCommentPopupInner').appendChild(h6);
	$id('IDCommentPopupInner').appendChild(pText);
	$id('IDCommentPopupInner').appendChild(p);
	
	if(browser == "Microsoft Internet Explorer")
	{			
		var b_version=navigator.appVersion;
		b_version = b_version.substr(b_version.indexOf("MSIE")+ 5, 3);
		var version=parseFloat(b_version);

		if(version<=6)
		{			
			/*if(null!=objTop)
				$id('IDCommentPopup').style.top = (IDgetTop(objTop))+"px";
			else*/
				$id('IDCommentPopup').style.top = (IDgetWindowHeight()/4 + IDgetScrollY()+"px");	
		}
	}
		
	if(type==0)
		$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "").replace(/idc-success/g, "");
	else if(type==1)
		$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "").replace(/idc-success/g, "");/*$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "") + " idc-success";*/
	else
		$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-error/g, "").replace(/idc-success/g, "");/*$id('IDCommentPopup').className = $id('IDCommentPopup').className.replace(/idc-success/g, "") + " idc-error";*/
		
	$id('IDCommentPopup').style.display = "block";
};


function IDCNav(id)
{
	$id('IDCNavGuest').style.display = "inline";
	$id('IDCNavGuest2').style.display = "none";
	$id('IDCNavOpenID').style.display = "block";
	$id('IDCNavList').style.display = "none";
	//$id('IDCNavTwitter').style.display = "block";
	//$id('IDCNavTwitter2').style.display = "block";
	//$id('IDCNavOpenID2').style.display = "none";
	if (id=="IDCNavGuest" || id=="IDCNavGuest2") {
		$id(id).style.display = "none";
		$id('IDCNavList').style.display = "block";
		$id(id+"2").style.display = "inline";
		}
};

function IDCNavReply(id)
{
	$id('IDCNavGuestReply').style.display = "inline";
	$id('IDCNavGuestReply2').style.display = "none";
	$id('IDCNavOpenIDReply').style.display = "block";
	$id('IDCNavListReply').style.display = "none";
	//$id('IDCNavTwitterReply').style.display = "block";
	//$id('IDCNavTwitterReply2').style.display = "block";
	//$id('IDCNavOpenIDReply2').style.display = "none";
	if (id=="IDCNavGuestReply" || id=="IDCNavGuestReply2") {
		$id(id).style.display = "none";
		$id('IDCNavListReply').style.display = "block";
		$id(id+"2").style.display = "inline";
		}
};

function hideMsgBox()
{
	$id('IDCommentPopup').style.display = "none";
};

function showReputationWhy() {
	showMsgBox("IntenseDebate Reputation Meter", "<p>The reputation meter is a measure of strength of all previous comments made on our system by a certain commenter as judged by his or her peers. It is one way to tell whether the comment you are reading is written by someone well-regarded.</p> <img src='"+IDHost+"/images/id-rep_scale.png' alt='Reputation Scale' />", 0 );
};

function showForgotPassword()
{	
	showMsgBox("Forgot your password?", "<form><label for=\"txtResolveEmail\">Please put in your email:</label><input type=\"text\" class=\"idc-text\" id=\"txtResolveEmail\" style=\"color:black\" onfocus=\"txtOnFocus(this, '');\" onblur=\"txtOnBlur(this, '');\" /><a href=\"javascript: forgotPassword();\" class=\"idc-btn_s\"><span></span><span class='idc-r'>Send me my password!</span></a></form>", 0);
	setTimeout('$id("txtResolveEmail").value=$id("IDtxtLoginEmail").value;',50);
	setTimeout('$id("txtResolveEmail").focus();',60);
};

function showGiveFeedback()
{	
	showMsgBox("Got feedback? Great!", "<form><label for='txtFeedbackEmail'>Email:</label><input type='text' id='txtFeedbackEmail' name='txtEmail' class='idc-text' onfocus=\"txtOnFocus(this, '');\" onblur=\"txtOnBlur(this, '');\" /><textarea name='txtFeedback' class='idc-text' id='txtFeedback' value='Feedback' onfocus=\"txtOnFocus(this, 'Feedback');\" onblur=\"txtOnBlur(this, 'Feedback');\"></textarea><a href='javascript: sendFeedback()' class='idc-btn_s'><span></span><span class='idc-r'>Send feedback</span></a></form>", 0);
	//$id("txtFeedback").focus();
	$id("txtFeedback").style.color="black";
	setTimeout('$id("txtFeedback").focus();',100);
};

function sendFeedback()
{	
	var theStr = '"params":{"feedback":"'+IDaddslashes($id("txtFeedback").value)+'", "email":"'+IDaddslashes($id("txtFeedbackEmail").value)+'", "blogpostid":'+commentObj.blogpostid+'}';
	var requestObj = new buildRequestObj(theStr, 8, null, connectionErr);
	xs.make_request(requestObj);	
};

function showSignupWhy()
{
	showMsgBox("Why signup?", "<p><ul><li>Link up with people who make comments on your favorite blogs</li><li>Market yourself by providing links to your facebook, twitter, and other accounts</li><li>Gain readership to your blog or website</li><li>Allow friends to track your comments across all blogs using ID Comments</li></ul></p>", 0);
};

function showLogin()
{
	$id("IDCommentsHead").className = $id("IDCommentsHead").className.replace(/idc-signup/g, "").replace(/idc-openid/g, "").replace(/idc-openid_signup/g, "").replace(/idc-wp-login/g, "") + " idc-login";
	$id("IDCommentsHeadLogin").className += " idc-sel";
	//$id("IDCommentsHeadSignup").className = $id("IDCommentsHeadSignup").className.replace(/idc-sel/g, "");
	//window.location.hash = "#idc-container";
		if(browser == "Microsoft Internet Explorer")
	{			
		var b_version=navigator.appVersion;
		b_version = b_version.substr(b_version.indexOf("MSIE")+ 5, 3);
		var version=parseFloat(b_version);

		if(version<=6)
		{			
				$id('IDLoginPopup').style.top = (IDgetWindowHeight()/4 + IDgetScrollY()+"px");
		}
	}
	$id('IDtxtLoginEmail').focus();
};

function showWPLogin() {
	IDC.$("IDCommentsHead").className = IDC.$("IDCommentsHead").className.replace(/idc-signup/g, "").replace(/idc-openid/g, "").replace(/idc-openid_signup/g, "").replace(/idc-login/g, "") + " idc-wp-login";
	IDC.$('IDtxtWPLoginEmail').focus();
};

function showLoginOpenID()
{
	$id("IDCommentsHead").className = $id("IDCommentsHead").className.replace(/idc-login/g, "") + " idc-openid";
	$id("IDCommentsHeadLogin").className += " idc-sel";
	//window.location.hash = "#idc-container";
	//$id("IDCommentsHeadSignup").className = $id("IDCommentsHeadSignup").className.replace(/idc-sel/g, "");
		if(browser == "Microsoft Internet Explorer")
	{			
		var b_version=navigator.appVersion;
		b_version = b_version.substr(b_version.indexOf("MSIE")+ 5, 3);
		var version=parseFloat(b_version);

		if(version<=6)
		{			
				$id('IDLoginOpenIDPopup').style.top = (IDgetWindowHeight()/4 + IDgetScrollY()+"px");
		}
	}
};

function hideLoginSignup() {
	$id("IDCommentsHead").className = $id("IDCommentsHead").className.replace(/idc-login/g, "").replace(/idc-signup/g,"").replace(/idc-openid/g, "").replace(/idc-openid_signup/g, "").replace(/idc-loggingin/g, "").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"").replace(/idc-wp-login/g,"");
	$id("IDCommentsHeadLogin").className = $id("IDCommentsHeadLogin").className.replace(/idc-sel/g, "");	
};

function showSignupNewThread()
{
	$id("IDCommentsNewThread").className = $id("IDCommentsNewThread").className.replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"") + " idc-signup";
    //$id("IDCommentsNewThreadListItem2").innerHTML='<h6><a href="javascript: showLoginNewThread();">Login instead</a></h6>';
	//$id("IDCommentsNewThreadListItem2").className=$id("IDCommentsNewThreadListItem2").className.replace(/idc-sel/gg, "");
	//$id("IDCommentsNewThreadListItem3").className+=" idc-sel";	
	//$id("IDPostNewThreadUseOpenID").style.display="block";
	//$id("IDPostNewThreadGoBack").style.display="none"
	/*if($id("chkSignUpNewThread").value==0)
		commentObj.newthreadType = 0;
	else*/
		commentObj.newthreadType = 0;
};

function showFBLoginReply() {
	$id("IDCommentReplyDiv").className = $id("IDCommentReplyDiv").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-twitter_loggedin/g,"") + " idc-facebook_login";	
	//$id("IDPostReplyUseOpenID").style.display = "none";
};

function showFBLoggedInReply() {
	$id("IDCommentReplyDiv").className = $id("IDCommentReplyDiv").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-facebook_login/g,"").replace(/idc-twitter_loggedin/g,"") + " idc-facebook_loggedin";	
	//$id("IDPostReplyUseOpenID").style.display = "none";
};

function showFBLoginNewThread() {
	$id("IDCommentsNewThread").className = $id("IDCommentsNewThread").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-twitter_loggedin/g,"") + " idc-facebook_login";	
	//$id("IDPostNewThreadUseOpenID").style.display = "none";
};

function showFBLoggedInNewThread() {
	$id("IDCommentsNewThread").className = $id("IDCommentsNewThread").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-facebook_login/g,"").replace(/idc-twitter_loggedin/g,"") + " idc-facebook_loggedin";	
	//$id("IDPostNewThreadUseOpenID").style.display = "none";
};

function showTWLoggedInNewThread() {
	$id("IDCommentsNewThread").className = $id("IDCommentsNewThread").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"") + " idc-twitter_loggedin";	
};

function showTWLoggedInReply() {
	$id("IDCommentReplyDiv").className = $id("IDCommentReplyDiv").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-openid_signup/g,"").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"") + " idc-twitter_loggedin";	
};
	
function showSignupOpenIDNewThread()
{
	$id("IDCommentsNewThread").className = $id("IDCommentsNewThread").className.replace(/idc-signup/g,"").replace(/idc-login/g,"").replace(/idc-openid/g,"").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"") + " idc-openid_signup";	
	//$id("IDPostNewThreadUseOpenID").style.display="none";
	//$id("IDPostNewThreadGoBack").style.display="block";
	/*if($id("chkSignupOpenIDNewThread").value==4)
		commentObj.newthreadType = 4;
	else*/
		commentObj.newthreadType = 5;
	$id("txtOpenIDSignupNewThreadURL").focus();
};

function showSignupReply()
{
	commentObj.divReply.className = commentObj.divReply.className.replace(/idc-login/g,"").replace(/idc-openid/g, "").replace(/idc-openid_signup/g, "").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"") + " idc-signup";
	//$id("IDCommentReplyLoginLink").innerHTML = '<h6><a href="javascript: showLoginReply();">Login</a></h6>';	
	//$id("IDCommentReplyLoginLink").className=$id("IDCommentReplyLoginLink").className.replace(/idc-sel/g, "");
	//$id("IDCommentReplySignupLink").className+=" idc-sel";	
	//$id("IDPostReplyUseOpenID").style.display="block";
	//$id("IDPostReplyGoBack").style.display="none";
	/*if($id("chkSignup").value==0)
		commentObj.replyType = 0;
	else*/
		commentObj.replyType = 0;
	$id('IDCPostNav').style.display = "block";
	$id('IDCPostNavReply').style.display = "block";
};

function showSignupOpenIDReply()
{
	commentObj.divReply.className = commentObj.divReply.className.replace(/idc-signup/g, "").replace(/idc-login/g, "").replace(/idc-openid/g, "").replace(/idc-facebook_login/g,"").replace(/idc-facebook_loggedin/g,"") + " idc-openid_signup";
	//$id("IDCommentReplyLoginLink").innerHTML = '<h6><a href="javascript: showSignupReply();">Back</a></h6>';
	//$id("IDPostReplyUseOpenID").style.display="none";
	//$id("IDPostReplyGoBack").style.display="block";
	/*if($id("chkSignupOpenIDReply").value==4)
		commentObj.replyType = 4;
	else*/
		commentObj.replyType = 5;
	$id("txtOpenIDSignupReplyURL").focus();
};

function collapseThread(commentid) {
	if($id("IDCommentCollapseLink" + commentid) && $id("IDCommentCollapseLink" + commentid).className == "idc-collapselink") {
		if( $id("IDCommentCollapseLink" + commentid) ) $id("IDCommentCollapseLink" + commentid).className = "idc-collapselink_closed";
		$id("IDCommentSubThread" + commentid).style.display = 'none';
		if( $id("IDThread" + commentid) ) $id("IDThread" + commentid).className += " idc-collapse";
	} else {
		if( $id("IDCommentCollapseLink" + commentid) ) $id("IDCommentCollapseLink" + commentid).className = "idc-collapselink";
		if( $id("IDThread" + commentid) ) $id("IDThread" + commentid).className = $id("IDThread" + commentid).className.replace(/idc-collapse/g, "");
		if( $id("IDCommentSubThread" + commentid).childNodes.length == 0 ) { //content not loaded yet
			if( commentObj.parentid == commentid) {
				hideReply();
				document.body.appendChild(commentObj.replyDiv); 
				commentObj.replyOnLoad = commentid;
			} else 
				commentObj.replyOnLoad = 0;
				
			IDReplaceHtml($id("IDCommentSubThread" + commentid), "<div class='idc-thread_loading'><img src='http://s.intensedebate.com/images/ajax-loader.gif' alt='Loading...' /> Loading...</div>");
			setTimeout('IDloadGetInnerCommentsChildren('+commentid+', 0);', 0);			
		} else
			$id("IDCommentSubThread" + commentid).style.display = 'block';
	}
};

function scrollToComment(commentid)
{
	var winH = IDgetWindowHeight();
	window.scrollTo(0, IDgetTop($id('IDComment'+commentid))- (winH / 2 - 50));
	highlightIt("IDComment"+commentid);

};

function resetFormColors()
{
	$id('txtComment').style.color="#CCC";
	$id('txtNameReply').style.color="#CCC";
	$id('txtEmailReply').style.color="#CCC"
	//$id('txtEmailReply2').style.color="#CCC";
	//$id('txtPasswordReply').style.color="#CCC";
	$id('txtURLReply').style.color="#CCC";
	
	$id('IDCommentNewThreadText').style.color="#CCC";
	$id('txtNameNewThread').style.color="#CCC";
	$id('txtEmailNewThread').style.color="#CCC"
	//$id('txtEmailNewThread2').style.color="#CCC";
	//$id('txtPasswordNewThread').style.color="#CCC";
	$id('txtURLNewThread').style.color="#CCC";
};

function logUserIn()
{
	
	if(navigator.userAgent.toLowerCase().indexOf("safari")==-1 && browser != "Microsoft Internet Explorer")
	{	
		if(commentObj.logUserInCallback)
			var firstCall = "false";
		else
			var firstCall = "true";
					
		$id("IDCommentsHead").className = $id("IDCommentsHead").className.replace(/idc-login/g, "").replace(/idc-signup/g,"").replace(/idc-openid/g, "").replace(/idc-openid_signup/g, "")+ " idc-loggingin";		
			
		var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "acctid":'+commentObj.acctid+', "email":"'+IDaddslashes($id('IDtxtLoginEmail').value)+'", "pass":"'+IDaddslashes($id('IDtxtLoginPass').value)+'", "token":"'+commentObj.token+'", "firstCall":'+firstCall+'}';
		var requestObj = new buildRequestObj(theStr, 2, null, connectionErr);
		xs.make_request(requestObj);		
	}
	else
	{
		document.getElementById('IDfrmHeaderLogin').submit();
	}
	
};

document.getElementById('IDfrmHeaderLogin').onsubmit = function () {
	if(navigator.userAgent.toLowerCase().indexOf("opera") > -1){		
		return false;
	}
	 	
};

function sortComments(sortMethod)
{
	$id("IDCommentReplyDiv").style.display = "none";	
	$id("idc-cover").appendChild($id("IDCommentReplyDiv"));
	if($id("idc-sortLinks"))	
		$id("idc-sortLinks").style.display = "none";
	if($id("idc-sortLinksLoading"))	
		$id("idc-sortLinksLoading").style.display = "block";
	
	switch(sortMethod)
	{
		case 0: //Score
			$id('IDSortLink0').className = "idc-sel";
			$id('IDSortLink1').className = "";
			$id('IDSortLink2').className = "";
			commentObj.sortType = "rating";
			IDPageLoad(0, "next");
			break;
			
		case 1: //Time
			$id('IDSortLink0').className = "";
			$id('IDSortLink1').className = "idc-sel";
			$id('IDSortLink2').className = "";
			commentObj.sortType = "date";
			IDPageLoad(0, "next");
			break;
			
		case 2: //Last Activity
			$id('IDSortLink0').className = "";
			$id('IDSortLink1').className = "";
			$id('IDSortLink2').className = "idc-sel";
			commentObj.sortType = "lastactivity";
			IDPageLoad(0, "next");
			break;
	}
};

function IDShowFollowBlog()
{
	$id('divIdcShareBlog').style.display="block";
	$id('divIdcSharePost').style.display="none";
	$id('IDShareMenuPost').className = "";
	$id('IDShareMenuBlog').className = "idc-sel";
};

function IDShowFollowPost()
{
	$id('divIdcSharePost').style.display="block";
	$id('divIdcShareBlog').style.display="none";
	$id('IDShareMenuBlog').className = "";
	$id('IDShareMenuPost').className = "idc-sel";
};

function showFollowThisDiscussion()
{
	$id('IDCommentsHeadFollowMenu').style.display="block";
	if($id('IDCommentsShowNetvibes').src=='')
	{
		$id('IDCommentsShowNetvibes').src = 'http://eco.netvibes.com/img/add2netvibes.png';
		$id('IDCommentsShowYahoo').src = 'http://us.i1.yimg.com/us.yimg.com/i/us/my/addtomyyahoo4.gif';
		$id('IDCommentsShowGoogle').src = 'http://buttons.googlesyndication.com/fusion/add.gif';
		$id('IDCommentsShowMsn').src = 'http://tkfiles.storage.msn.com/x1pHd9OYNP16fmmfqJHji7qY0yYomKrFzGROBps3O6qHF0JRlVV8xH6X4cfsptw0fftk5oJYFpTKP6I-i91-se8TaoO7R9oiPVoxDEG_LEZW_XhegHxASvHJYsSxNjf526t';
		$id('IDCommentsShowRss').src = 'http://intensedebate.com/themes/chameleon/images/feed-icon-12x12.png';
		$id('IDCommentsShowNetvibes2').src = 'http://eco.netvibes.com/img/add2netvibes.png';
		$id('IDCommentsShowYahoo2').src = 'http://us.i1.yimg.com/us.yimg.com/i/us/my/addtomyyahoo4.gif';
		$id('IDCommentsShowGoogle2').src = 'http://buttons.googlesyndication.com/fusion/add.gif';
		$id('IDCommentsShowMsn2').src = 'http://tkfiles.storage.msn.com/x1pHd9OYNP16fmmfqJHji7qY0yYomKrFzGROBps3O6qHF0JRlVV8xH6X4cfsptw0fftk5oJYFpTKP6I-i91-se8TaoO7R9oiPVoxDEG_LEZW_XhegHxASvHJYsSxNjf526t';
		$id('IDCommentsShowRss2').src = 'http://intensedebate.com/themes/chameleon/images/feed-icon-12x12.png';
	}
};

function hideFollowThisDiscussion()
{$id('IDCommentsHeadFollowMenu').style.display="none";};

function createCookie(name,value,days) 
{
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) 
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) { createCookie(name,"",-1);};

$id('IDCommentNewThreadText').onchange = function () {createCookie("IDNewThreadComment", this.value, 1);};
$id('txtComment').onchange = function () {createCookie("IDReplyComment", this.value, 1);};

if(readCookie("IDNewThreadComment") && readCookie("IDNewThreadComment").length>0 && window.location.hash.indexOf("IDComment")<=0)
{
	$id('IDCommentNewThreadText').value = readCookie("IDNewThreadComment");
	$id('IDCommentNewThreadText').style.color="black";
}
else
	eraseCookie("IDNewThreadComment");

if(readCookie("IDReplyComment") && readCookie("IDReplyComment").length>0 && window.location.hash.indexOf("IDComment")<=0)
{
	$id('txtComment').value = readCookie("IDReplyComment");
	$id('txtComment').style.color="black";
	
	if(readCookie("IDReplyCommentId"))
	{
		if(commentObj.comments[readCookie("IDReplyCommentId")])
			showReply(readCookie("IDReplyCommentId"));
	}
}
else
{
	eraseCookie("IDReplyComment");
	eraseCookie("IDReplyCommentId");
}

function IDeditComment(commentid)
{
	if(!commentObj.curUser.userid || commentObj.curUser.userid <= 0)
	{
		showMsgBox("Sorry", "<p>You must be logged in to edit a comment.</p>", 0, null);		
		return;
	}
	
	if(!commentObj.comments[commentid] || !commentObj.comments[commentid].commentid)
	{
		showMsgBox("Sorry", "<p>That comment doesn't exist...</p>", 0, null);		
		return;
	}
	
	$id("IDComment-CommentText"+commentid).style.display="none";
	if(!$id("IDEditCommentTextArea"+commentid))
	{
		var newTextArea = $newEl("textarea");
		newTextArea.id = "IDEditCommentTextArea"+commentid;
		$id("IDComment-CommentText"+commentid).parentNode.appendChild(newTextArea);	
	}
	else
		$id("IDComment-CommentText"+commentid).parentNode.appendChild($id("IDEditCommentTextArea"+commentid));	
		
	$id("IDEditCommentTextArea"+commentid).style.display="block";
	$id("IDEditCommentTextArea"+commentid).className = "idc-text";
	if(commentObj.comments[commentid].depth==0)
		$id("IDEditCommentTextArea"+commentid).style.width = (IDgetWidth($id('IDComment'+commentid))-8)+"px";
	else
		$id("IDEditCommentTextArea"+commentid).style.width = (IDgetWidth($id('IDComment'+commentid))-26) +"px";
		
	$id("IDEditCommentTextArea"+commentid).value = $id("IDComment-CommentText"+commentid).innerHTML.replace(/<\/div>/gi,'').replace(/<div style="overflow: auto;">/gi, '').replace(/<span class="idc-clear"> <\/span>/gi, '').replace(/<a href="(.*?)"><\/a>/gi, '').replace(/&amp;/gi, '&').replace(/<span class="idc-smiley"><span style="background-position: (.*?);"><span>(.*?)<\/span><\/span><\/span>/, '$2');
	$id("IDEditCommentTextArea"+commentid).value = $id("IDEditCommentTextArea"+commentid).value.replace(/<br>/gi,'\n').replace(/^\s+|\s+$/g,"");
	//$id("IDCommentPostReplyLink"+commentid).style.display="none";
	
	if(!$id("IDCommentCancelSave"+commentid))
	{
		var newLinkDiv = $newEl("div");
		newLinkDiv.id = "IDCommentCancelSave" + commentid;
		$id("IDCommentLinksRight"+commentid).parentNode.insertBefore(newLinkDiv, $id("IDCommentLinksRight"+commentid));
	}
	else
		$id("IDCommentLinksRight"+commentid).parentNode.insertBefore($id("IDCommentCancelSave"+commentid), $id("IDCommentLinksRight"+commentid));
		
	IDReplaceHtml($id("IDCommentCancelSave"+commentid), '<a href="javascript: IDcancelEditComment('+commentid+');" class="idc-btn_l-secondary"><span></span><span class="idc-r">Cancel</span></a><a href="javascript: IDsaveComment('+commentid+');" class="idc-btn_l"><span></span><span class="idc-r"><strong>Save</strong></span></a>');
	$id("IDCommentCancelSave"+commentid).className = "idc-right";
	$id("IDCommentCancelSave"+commentid).style.display="block";
	$id("IDCommentPostReplyLink"+commentid).style.display="none";
	$id("IDCommentLinksRight"+commentid).style.display="none";
	
	if(!$id('divIDEditExpandingText'+commentid))
	{
		var editMeasureDiv = $newEl("div");
		editMeasureDiv.className="idc-measure";
		editMeasureDiv.id = 'divIDEditExpandingText'+commentid;
		$id('idc-container').appendChild(editMeasureDiv);
	}
	if(commentObj.expanding=='T')
	{
	$id("IDEditCommentTextArea"+commentid).onchange = function(e){var keycode = IDgetKeycode(e);IDReplaceHtml($id('divIDEditExpandingText'+commentid), this.value.replace(/\n/g,'<br />')+'&nbsp;');$id('divIDEditExpandingText'+commentid).style.width = ($id("IDEditCommentTextArea"+commentid).offsetWidth - 12)+'px';	this.style.height = (parseInt($id('divIDEditExpandingText'+commentid).offsetHeight) + 10)+'px';};	
	$id("IDEditCommentTextArea"+commentid).onkeyup = function(e){var keycode = IDgetKeycode(e);IDReplaceHtml($id('divIDEditExpandingText'+commentid), this.value.replace(/\n/g,'<br />')+'&nbsp;');$id('divIDEditExpandingText'+commentid).style.width = ($id("IDEditCommentTextArea"+commentid).offsetWidth - 12)+'px';	this.style.height = (parseInt($id('divIDEditExpandingText'+commentid).offsetHeight) + 10)+'px';};	
		IDReplaceHtml($id('divIDEditExpandingText'+commentid), $id("IDEditCommentTextArea"+commentid).value.replace(/\n/g,'<br />')+'&nbsp;');$id('divIDEditExpandingText'+commentid).style.width = ($id("IDEditCommentTextArea"+commentid).offsetWidth - 12)+'px';	$id("IDEditCommentTextArea"+commentid).style.height = (parseInt($id('divIDEditExpandingText'+commentid).offsetHeight) + 10)+'px';
	}
	else
	{
		$id("IDEditCommentTextArea"+commentid).className = 'idc-text_noresize';
	}
}

function IDsaveComment(commentid)
{
	if(!commentObj.curUser.userid || commentObj.curUser.userid <= 0)
	{
		showMsgBox("Sorry", "<p>You must be logged in to delete a comment.</p>", 0, null);		
		return;
	}
	
	if(!commentObj.comments[commentid] || !commentObj.comments[commentid].commentid)
	{
		showMsgBox("Sorry", "<p>That comment doesn't exist...</p>", 0, null);		
		return;
	}
	
	IDReplaceHtml($id("IDCommentCancelSave"+commentid), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');
	var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "accountid":'+commentObj.acctid+', "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "commentid":"'+commentid+'", "comment":"'+encodeURIComponent(IDaddslashes(IDaddslashes($id("IDEditCommentTextArea"+commentid).value))).replace(/&/g, "%26")+'"}';
	var requestObj = new buildRequestObj(theStr, 12, null, connectionErr);
	xs.make_request(requestObj);
}

function IDcancelEditComment(commentid)
{
	$id("IDEditCommentTextArea"+commentid).style.display="none";
	$id("IDComment-CommentText"+commentid).style.display="block";
	
	$id("IDCommentCancelSave"+commentid).style.display="none";
	$id("IDCommentPostReplyLink"+commentid).style.display="block";
	$id("IDCommentLinksRight"+commentid).style.display="block";
}

function deleteComment(commentid, type)
{
	var answer = confirm("Are you sure you want to delete this comment?")
	if (answer)
	{
		if(!commentObj.curUser.userid || commentObj.curUser.userid <= 0)
		{
			showMsgBox("Sorry", "<p>You must be logged in to delete a comment.</p>", 0, null);		
			return;
		}
		
		if(!commentObj.comments[commentid] || !commentObj.comments[commentid].commentid)
		{
			showMsgBox("Sorry", "<p>That comment doesn't exist...</p>", 0, null);		
			return;
		}
		
		if(type<0 || type>1)
		{
			showMsgBox("Sorry", "<p>That type is invalid.</p>", 0, null);		
			return;
		}
		
		var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "accountid":'+commentObj.acctid+', "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "commentid":"'+commentid+'"}';
		
		if(type==0)
			objType = 11;
		else
			objType = 10;
			
		IDReplaceHtml($id("IDCommentPostReplyLinkDelete"+commentid), 'Deleting...');
		$id("IDCommentPostReplyLinkDelete"+commentid).className = "idc-loadtext";
		var requestObj = new buildRequestObj(theStr, objType, null, connectionErr);
		xs.make_request(requestObj);
	}
}

function deleteTrackback(trackbackid)
{
	var answer = confirm("Are you sure you want to delete this trackback?")
	if (answer)
	{
		if(!commentObj.curUser.userid || commentObj.curUser.userid <= 0)
		{
			showMsgBox("Sorry", "<p>You must be logged in to delete a trackback.</p>", 0, null);		
			return;
		}
		
		var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "accountid":'+commentObj.acctid+', "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "trackbackid":"'+trackbackid+'"}';
		
		objType = 15;
		if($id("IDTrackBackDeleteLink"+trackbackid))
		{
			IDReplaceHtml($id("IDTrackBackDeleteLink"+trackbackid), 'Deleting...');
			$id("IDTrackBackDeleteLink"+trackbackid).className = "idc-loadtext";
			var requestObj = new buildRequestObj(theStr, objType, null, connectionErr);
			xs.make_request(requestObj);
		}
		else
		{
			showMsgBox("Sorry", "<p>That trackback doesn't exist...</p>", 0, null);
			return;		
		}
	}	
}

function banIP(commentid)
{
	var answer = confirm("Are you sure you want to ban this IP address?")
	if (answer)
	{
		if(!commentObj.curUser.userid || commentObj.curUser.userid <= 0)
		{
			showMsgBox("Sorry", "<p>You must be logged in to ban an IP.</p>", 0, null);		
			return;
		}
		
		if(!commentObj.comments[commentid] || !commentObj.comments[commentid].commentid)
		{
			showMsgBox("Sorry", "<p>That comment doesn't exist...</p>", 0, null);		
			return;
		}	
		
		var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "accountid":'+commentObj.acctid+', "userid":'+commentObj.curUser.userid+', "token":"'+commentObj.curUser.token+'", "commentid":"'+commentid+'"}';
				
		IDReplaceHtml($id("IDCommentPostReplyLinkBan"+commentid), 'Blocking...');
		$id("IDCommentPostReplyLinkBan"+commentid).className = "idc-loadtext";
		var requestObj = new buildRequestObj(theStr, 16, null, connectionErr);
		xs.make_request(requestObj);
	}
}

function IDCSubscribeByEmail(type)
{
	if($id('IDCSubscribeEmail'+type))
		var IDCEmail = $id('IDCSubscribeEmail'+type).value;
	else
		var IDCEmail = commentObj.curUser.userid;
	
	var theStr = '"params":{"blogpostid":'+commentObj.blogpostid+', "accountid":'+commentObj.acctid+', "email":"'+IDCEmail+'", "type":"'+type+'"}';
			
	IDReplaceHtml($id('IDCSubscribeSubmit'+type), '<img src="http://s.intensedebate.com/images/ajax-loader.gif" alt="Submitting..." />');

	var requestObj = new buildRequestObj(theStr, 17, null, connectionErr);
	xs.make_request(requestObj);	
}

IDC.show_more_trackbacks = function () {
    var child_nodes = IDC.$('IDTBWrapper').childNodes;
    for( objElem in child_nodes ) {
        if (child_nodes[objElem] && child_nodes[objElem].style )
            IDC.show( child_nodes[objElem] );
    };
    IDC.hide( IDC.$('IDShowMoreTBLink') );
};

IDC.user_menu = {
    "show": function ( parent_div_id, userid ) {
	if ( IDC.popupMenuTimeout )
		clearTimeout( IDC.popupMenuTimeout );	
		
	if ( IDC.userMenu[userid] ) {
                var div = IDC.$('idc-usermenu');
                document.body.appendChild(div);
                div.innerHTML = IDC.userMenu[userid];
                var parent_div = IDC.$( parent_div_id );
		while( parent_div.hasChildNodes() )
			parent_div.removeChild( parent_div.firstChild );
                parent_div.appendChild(div);
                IDC.show( new Array( div, parent_div ) );
		return;
	}
	
	if ( IDC.showUserMenuCallback )
		var firstCall = "false";
	else
		var firstCall = "true";

	var requestObj = new buildRequestObj( '"params":{"blogpostid":'+commentObj.blogpostid+', "parent_div_id":"'+parent_div_id+'", "commentuserid":"'+userid+'", "userid":"'+commentObj.curUser.userid+'", "token":"'+commentObj.token+'", "firstCall":'+firstCall+'}', 2, null, connectionErr);
	requestObj.service_url = 'http://intensedebate.com/js/getUserMenu.php';
	xs.make_request(requestObj);
    },
    
    "hide": function ( comment_id ) {
        IDC.hide('IDUserMenu' + comment_id);
    },
    
    "clear_cache": function () {
        IDC.userMenu = new Array();
    },
    
    "add_friend": function ( uid ) {
            if ( commentObj && commentObj.curUser && !commentObj.curUser.isLoggedIn ) {
                    showMsgBox("Sorry", "<p>You must be logged in to add friends.</p>", 0, null);
                    return;
            }
            
            if ( IDC.addFriendCallback )
                    var firstCall = "false";
            else
                    var firstCall = "true";
            
            if ( commentObj && commentObj.curUser && commentObj.curUser.userid && commentObj.curUser.userid > 0 )
                var login_str = '"blogpostid":' + commentObj.blogpostid + ', "userid":' + commentObj.curUser.userid + ', "token":"' + commentObj.curUser.token + '",';
                
            var theStr = '"params":{' + login_str + ' "friendid":' + uid + ', "firstCall":'+firstCall+'}';
            var requestObj = new buildRequestObj(theStr, 6, null, connectionErr);
            xs.make_request(requestObj);
    },
    
    "add_menu_to_img": function ( img, user ) {
        if ( !img )
            return;
        
        IDC.user_menu.unique_img_counter++;
        var counter=IDC.user_menu.unique_img_counter;
        var new_div = IDC.c_object( 'div', {'id': 'IDUserMenu' + counter, 'class': 'idc-m', 'onmouseover': function () { clearTimeout( IDC.popupMenuTimeout ); }, 'onmouseout': function () { IDC.popupMenuTimeout = setTimeout( 'IDC.user_menu.hide(IDC.user_menu.unique_img_counter);', 250 ); } } );
        new_div.onmouseout = function() { IDC.popupMenuTimeout = setTimeout( function() { IDC.user_menu.hide( counter); } , 250 ) };
        IDC.e_style( new_div, {'display': 'none'} );
        img.parentNode.appendChild( new_div );
        img.onmouseover = function () { IDC.user_menu.show('IDUserMenu' + counter, user) };
    },
    
    "unique_img_counter" : 0,
    "loading_div" : IDC.c_object( 'div', {'id':'idc-usermenu-loading', 'innerHTML': '<div class="idc-m-avatar idc-m-avatar-loading"><img class="idc-m-loadingimg" src="http://s.intensedebate.com/themes/universal/images/idc-m-loading.gif" alt="Loading..."/></div>' })
};

IDC.user_menu.clear_cache();
		var comment_array = null;
		for( objElem in comment_array ) {
			if ( !commentObj.comments[comment_array[objElem]] )
				continue;
		
			if ( comment_array[objElem] || commentObj.comments[comment_array[objElem]].status == 2 || commentObj.comments[comment_array[objElem]].status == 6 ) {
				if ( IDC.$("IDCommentVoteScore"+comment_array[objElem]) && IDC.$("IDCommentVoteScore"+comment_array[objElem]).parentNode )
					IDC.$("IDCommentVoteScore"+comment_array[objElem]).parentNode.className += " idc-disabled";
			}
		}var IDAdminIsLoggedIn=false;if( typeof(IDCounted) == "undefined" ) {
	var IDCounted;
	_qoptions = [{ labels:"languages.en_US,idc", qacct:"p-94D6e1NDscLvI"},{qacct:"p-18-mFEk4J448M", labels:"type.intensedebate.embed"}];
	IDcountIT();
}
	
function IDcountIT() {
	var newScript  = document.createElement('script');
	newScript.type = 'text/javascript';
	newScript.src  = '//edge.quantserve.com/quant.js';
	document.getElementsByTagName('head')[0].appendChild(newScript);
}
var argList = new Array();
argList['userid'] = '';
argList['is_admin'] = false;

id_fire_action('idcomments_func_load', argList);
