<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">

	<?php
	session_start ();
	$access = '1';
	$user = 'guest';
	$uid = '';

	require 'core/bin/php/setting.php';

	  // the access is regulated in the api get and edit
	if (isset ($_SESSION["token"])) {
		// check if the access is true and correct
		$token = (explode("-", $_SESSION["token"]));
		condb('open');
		$sql = mysql_query("SELECT user, userID FROM user WHERE userID = " . $token[1] . " AND token = '" . $token[0] . "';");
		condb('close');
		$num_results = mysql_num_rows($sql);
		if ($num_results > 0) {
			while ($row = mysql_fetch_object($sql)) {
				$user = $row->user;
				$access = '0';
				$uid = $row->userID;
			}
		}
	}


	?>
	<!--
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
-->
	<title>Notizblogg</title>
	<link rel="shortcut icon" href="<?php echo __SITE_URL__; ?>/core/style/img/favicon.ico">

	<meta name="author" content="André Kilchenmann"/>
	<meta name="description" content="Notizblogg ist der digitale Zettelkasten von André Kilchenmann. Nebst textuellem Inhalt kann der digitale MeMex, auch Bilder, Video- oder Ton-Dokumente aufnehmen."/>
	<meta name="keywords" content="literatur,verwaltung,zettelkasten,luhmann,upcycling,redesign,kilchenmann,andré,milchkannen,eiskunstlauf,fotografie,notizblogg"/>

	<meta name="Resource-type" content="Document"/>

	<script type="text/javascript" src="<?php echo __SITE_URL__; ?>/core/lib/jquery-1.10.2.min.js"></script>

	<script type="text/javascript" src="<?php echo __SITE_URL__; ?>/core/bin/js/md5.js"></script>
	<script type="text/javascript" src="<?php echo __SITE_URL__; ?>/core/bin/js/jquery.center.js"></script>
	<script type="text/javascript" src="<?php echo __SITE_URL__; ?>/core/bin/js/jquery.warning.js"></script>

	<script type="text/javascript" src="<?php echo __SITE_URL__; ?>/core/bin/js/jquery.shownote.js"></script>
	<script type="text/javascript" src="<?php echo __SITE_URL__; ?>/core/bin/js/jquery.expand.js"></script>

	<link rel="stylesheet" type="text/css" href="<?php echo __SITE_URL__; ?>/core/style/css/nb.css">

</head>
<body>

<header>
	<h1>
		<a href='http://notizblogg.ch'>Notizblogg</a> |
		<a href="https://plus.google.com/u/0/102518416171514295136/posts?rel=author">der digitale Zettelkasten von André Kilchenmann</a>
	</h1>
	<div class="left">
		<span class="project"></span>
	</div>
	<div class="center">
		<span class="search"></span>
	</div>
	<div class="right">
		<span class="add"></span>
		<span class="user"></span>
	</div>
	<!-- <span class="drawer"></span> -->
	<!-- <span class="menu"></span> -->
</header>
<div class="float_obj medium warning"></div>
<div class="float_obj large pamphlet"></div>
<footer>
	<p class="small">
		<a href="http://notizblogg.ch">Notizblogg</a> | Idea, Concept and Design &copy;
		<a href="https://plus.google.com/u/0/102518416171514295136/posts?rel=author">André Kilchenmann</a> |
		<span class='year'></span>
		<a href="http://milchkannen.ch">
		<img src="<?php echo __SITE_URL__; ?>/core/style/img/akM-logo-small.png" alt="milchkannen | kilchenmann" title="milchkannen | andré kilchenmann"/>
		</a>
	</p>
</footer>

<div id="fullpage">
		<div class="viewer">


			<?php
			// default parameters

			$type = '';
			$query = 'all';
			$viewer = 'wall';

			if($_SERVER['QUERY_STRING']){
				// default values; in case of wrong queries; these variables would be overwritten in the right case
				if(isset($_GET['source'])){
					$type = 'source';
					$query = $_GET['source'];
					$viewer = 'desk';
				}
				if(isset($_GET['note'])){
					$type = 'note';
					$query = $_GET['note'];
				}
				if(isset($_GET['label'])){
					$type = 'label';
					$query = $_GET['label'];
				}
				if(isset($_GET['author'])){
					$type = 'author';
					$query = $_GET['author'];
				}
				if(isset($_GET['collection'])){
					$type = 'collection';
					$query = $_GET['collection'];
					$viewer = 'desk';
				}
				if(isset($_GET['q'])){
					$type = 'search';
					$query = $_GET['q'];
				}
			}
			/*
			else {
				?>
				<script type="text/javascript">
					$('#fullpage').css({'background-image': 'url(core/style/img/bg-notizblogg.jpg)'});
				</script>
			<?php
			}

			show($type, $query, $access, $viewer);
*/
			?>

		</div>

</div>


<script type="text/javascript">
// Read a page's GET URL variables and return them as an associative array.
function getUrlVars()
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
}


var NB = {};
NB.access = '<?php echo $access; ?>';
NB.user = {
	id:  '<?php echo $uid; ?>',
	name: '<?php echo $user; ?>'
};
NB.url = '<?php echo __SITE_URL__; ?>';
NB.uri = NB.url + '/' +(location.search).substr(1);
NB.media = '<?php echo __MEDIA_URL__; ?>';

NB.query = {
	type: '<?php echo $type; ?>',
	id: '<?php echo $query; ?>'
};

//NB.query = getUrlVars();


//console.log(NB);
//console.log('uri: ' + NB.uri);
//console.log('user: ' + NB.user);
//console.log('uid: ' + NB.uid);
//console.log('access: ' + NB.access);
//console.log(NB.query);

//console.log((location.search).substr(1));

//	$(document).ready(function () {

// set the panel icons if you're logged in
		$.getScript(NB.url + '/core/bin/js/jquery.login.js', function() {
			if(NB.user.name !== 'guest' && NB.access !== '1' && NB.user.id !== '') {
				$('.user').login({
					type: 'logout',
					user: NB.user.id,
					submit: 'Abmelden',
					action: NB.url + '/core/bin/php/check.out.php'
				});
				$.getScript(NB.url + '/core/bin/js/jquery.finder.js', function() {
					/* integrate the search bar in the header panel */
					$('.project').append($('<a>').attr({href: NB.url}).append($('<h2>').text('Notizblogg')).addClass('logo'));



					$('.search').finder({
						search: 'Suche',
						filter: 'Erweiterte Suche',
						database: ''
					});
				});
					/* integrate the add button */
					$('.add')
						.append($('<button>').addClass('btn grp_none toggle_add'))
						.expand({
							type: 'source',		// source || note
							sourceID: 'new',
							noteID: 'new',
							edit: true,		// true || false
							data: undefined,
							show: 'form'		// booklet || form
						});
				$.getScript(NB.url + '/core/bin/js/jquery.drawer.js', function() {

					$('.drawer').append(
						//		$('<button>').addClass('btn grp_none toggle_drawer')
					);
				});

			} else {
				$('.user').login({
					type: 'login',
					user: 'Benutzername',
					key: 'Passwort',
					submit: 'Anmelden',
					action: NB.url + '/core/bin/php/check.in.php'
				});
			}
		});

		var height = $(window).height() - $('header').height() - $('footer').height();
		$('div.viewer').css({'height': height});
		$('.float_obj').center();


		//$.getScript('core/bin/js/jquery.fullpage.min.js', function() {
			/*
			$('#fullpage').fullpage({
				//	anchors: ['info', 'demo', 'tools', 'about', 'login' ],
				anchors: ['start'],
				//	slidesColor: ['#1A1A1A', '#1A1A1A', '#7E8F7C', '#333333'],
				slidesColor: ['#1A1A1A'],
				css3: true
			});
			*/
		//});


/*
		if(getUrlVars()["access"] !== undefined) {
			$('#fullpage').warning({
				type: 'access'
			});
			$('body').on('click', function(){
				window.location.href = window.location.href.split('?')[0];
			})
		}
		*/

//	});

	$(document).keyup(function(e) {
		if(e.keyCode == 27) {
				if($('.float_obj').is(':visible')) {
					$(this).hide();
					$('.viewer').css({'opacity': '1'});

					if($('button.toggle_add').hasClass('toggle_delete')) {
						$(this).toggleClass('toggle_delete');
					}

				}
		}
	});

	$(window).load(function() {
		$('.viewer').shownote(NB);

		/*
		if($('.desk').length !== 0) {
			var panel_left = $('.left_side');
			var panel_right = $('.right_side');
			var margin_left = panel_left.position().left + panel_left.width();
			if((margin_left + panel_right.width()) >= win_width ) {
				panel_right.css({left: '44px'});
			} else {
				panel_right.css({left: margin_left});
			}
		}
		*/
	});
	$(window).resize(function() {
		var height = $(window).height() - $('header').height() - $('footer').height();
		$('div.viewer').css({'height': height});
		$('.float_obj').center();
		// set the numbers of wall columns
		if($('.wall').length !== 0) {
			var width = $(window).width();
			var note_width = $(this).find('.note').width() + 60;
			var num_col = Math.floor(width / note_width);
			$(this).css({
				'-webkit-column-count': num_col,
				'-moz-column-count': num_col,
				'column-count': num_col,
				'width': num_col * note_width
			});
		}

	});

	//$.getScript(NB.url + '/core/bin/js/jquery.mousewheel.min.js', function() {
		/*
		$('div.viewer').on('mousewheel', function (e, d) {
			var viewer = $(this);
			if ((this.scrollTop === (viewer[0].scrollHeight - viewer.height()) && d < 0) || (this.scrollTop === 0 && d > 0)) {
				e.preventDefault();
			}
		})
		*/
	//});



	$('div.desk')
		.mouseenter(function() {
//			$('.note').toggleClass('active');
//			$('.note').children('div').css({'background-color': 'rgba(251, 251, 251, 0.3)'});
		})
		.hover(function() {
//			$('.note').children('div').css({'background-color': 'rgba(251, 251, 251, 0.3)'});
		})
		.mouseleave(function() {
//			$('.note').toggleClass('active');
//			$('.note').children('div').css({'background-color': ''});
	});

/*
	$('div.tools button').hover(function(){
			// first function is for the mouseover/mouseenter events
			console.log($(this).attr('id'));
		},
		function(){
			// second function is for mouseleave/mouseout events
			$(this).find('button').show();
		});
*/





	if($('.desk').length !== 0) {
		$.getScript(NB.url + '/core/bin/js/jquery.masonry.min.js', function() {
			$(function(){
				function Arrow_Points()
				{
					var s = $('.right_side').find('.note');
					$.each(s, function(i, obj) {
						var posLeft = $(obj).position().left;		//css("left");
					//	$(obj).addClass('borderclass');
						if(posLeft === 0)
						{
//							html = "<span class='rightCorner'></span>";
							$(obj).prepend($('<span>').addClass('rightCorner'));
						}
						else
						{
//							html = "<span class='leftCorner'></span>";
							$(obj).css({'text-align': 'right'}).prepend($('<span>').addClass('leftCorner'));
						}
					});
				}


	// Divs
				$('.right_side').masonry({itemSelector : '.note'});
				Arrow_Points();




			});


	});
	}


	/* copyright date */
	var curDate = new Date(),
		curYear = curDate.getFullYear();
	$('span.year').text('2006-' + curYear);


</script>



</body>
</html>
