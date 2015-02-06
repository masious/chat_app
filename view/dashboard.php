<!Doctype html>
<html>
	<head>
	<link rel="stylesheet" href="/css/bootstrap.css" />
	<script src="/js/jquery.js"></script>
		<style>
			body{
				overflow: hidden;
			}
			.online{
				width: 	8px;
				height: 8px;
				background: green;
				border-radius: 8px;
				display: inline-block;
			}
			.users-list{
				height: 100vh;
				box-shadow: 5px 2px 2px rgba(0,0,0,0.2);
				display: block;
				padding-right: 0;
			}
			.users-list > li.active{
				box-shadow: 7px 2px 2px #fff;
			}
			.users-list > li > a{
				border-radius: 4px 0px 0px 4px;
			}
			.users-name{
				color: blue;
			}
			.main-content{
				height: 76vh;
			}
		</style>
		<script>
			var timestamp = 0;
			function renderPM(username,message){
				$('.main-content').append(
					$('<div/>',
						{
							"class":	"pm",
							"html":		"<span class='users-name'>" + username + "</span>: " + message.body
						}
					)
				);
			}
			
			var userMessages = [];
			
			function storeMessage(message){
				if(typeof userMessages[message.sender_id] === 'undefined')
					userMessages[message.sender_id] = [];
					
				userMessages[message.sender_id].push(message);
			}
			
			function getMessages(){
				$.post('/dashboard/get_messages',{from: timestamp},function(o){
					var messages = JSON.parse(o);
					if(messages.length > 0)
						timestamp = messages[messages.length - 1].date;
					
					for(var m in messages){
						renderPM($('.user.active').html(),messages[m]);
						storeMessage(messages[m]);
					}
				});
			}
			
			function renderMessagesOf(userId){
				$('.main-content').html('');
				for(var m in userMessages[userId]){
					renderPM($(".user[data-id='"+userId+"'] a").html(),userMessages[userId][m]);
				}
			}
		
			$(function(){
				$($('.user')[0]).addClass('active');
				
				$('#send').click(function(e){
					var receiver_id = $('.user.active').attr('data-id');
					var body = $('#body').val();
					$.post('/dashboard/send/', {receiver_id: receiver_id, body: body}, function(o){
						var response = JSON.parse(o);
						if(response.status == 'success')
							$('#body').val('');
					});
				});
				
				$('.user').click(function(){
					$('.user.active').removeClass('active');
					$(this).addClass('active');
					renderMessagesOf($(this).attr('data-id'));
				});
				
				getMessages();
				var t = setInterval(getMessages,3000);
			});
		</script>
	</head>
	<body>
		<?php echo $html->element('header'); ?>
		<ul class="nav nav-pills nav-stacked col-sm-2 users-list push-left">
			<?php foreach($users as $user) { ?>
				<li role="presentation" class="user" data-id="<?php echo $user->id; ?>">
					<a href="#"><?php echo $user->username; ?></a>
				</li>
			<?php } ?>
		</ul>
		<div class="col-sm-10 chat-area pull-right">
			<div class="main-content">
			</div>
			<div id="sender-area">
				<div class="input-group">
					<textarea rows="1" name="body" placeholder="Type a message..." id="body" class="form-control" ></textarea>
					<span class="input-group-btn">
						<button id="send" class="btn btn-primary">Send</button>
					</span>
				</div>
			</div>
			<?php echo Auth::user()->username; ?>
		</div>
	</body>
</html>