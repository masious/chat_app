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
			.main-content-holder{
				overflow-y: scroll;
				height: 80vh;
				margin-bottom: 10px;
			}
			.main-content{
				height: 76vh;
			}
			.mine{
				float: right;
			}
			.pm{
				padding: 3px;
				clear: both;
				background:#ccc;
				margin: 3px;
				width: 50%;
				position: relative;
				border-radius: 3px;
			}
			.pm .date{
				position: absolute;
				top: 0;
				right: 0;
				color:#555;
				font-size:9px;
				display: none;
			}
			.pm:hover .date{
				display: block;
				background: rgba(255,255,255,0.4);
			}
		</style>
		<script>
			var users = <?php echo json_encode(User::all()); ?>;
			users.find = function(id){
				for(var i in users){
					if(typeof users[i].id != 'undefined')
						if(users[i].id == id)
							return users[i];
				}
			};
			
			var user = <?php echo json_encode(Auth::user()); ?>;
			var timestamp = 0;
			function renderPM(message){
				var active_id = $('.user.active').attr('data-id');
				if(message.sender_id != active_id && message.receiver_id != active_id)
					return ;
				$('.main-content').append(
					$('<div/>',
						{
							"class":	"pm " + (message.sender_id==user.id?"mine":""),
							"html":		"<span class='users-name'>" + users.find(message.sender_id).username + "</span>: " + message.body + 
										"<div class=\"date\">" + message.date + "</div>"
						}
					)
				);
				var mydiv = $('.main-content-holder');
				mydiv.scrollTop(mydiv.prop('scrollHeight'));
			}
			
			var userMessages = [];
			
			function storeMessage(message){
				var index = message.sender_id==user.id ? message.receiver_id : message.sender_id;
				if(typeof userMessages[index] === 'undefined')
					userMessages[index] = [];
					
				userMessages[index].push(message);
			}
			
			function getMessages(){
				$.post('/dashboard/get_messages',{from: timestamp},function(o){
					var messages = JSON.parse(o);
					if(messages.length > 0)
						timestamp = messages[messages.length - 1].date;
					
					for(var m in messages){
						renderPM(messages[m]);
						storeMessage(messages[m]);
					}
				});
			}
			
			function renderMessagesOf(userId){
				$('.main-content').html('');
				for(var m in userMessages[userId]){
					renderPM(userMessages[userId][m]);
				}
			}
		
			$(function(){
			
				for(var u in users){
					if(typeof users[u] == 'object' && users[u].id != user.id)
						$('.users-list').append($('<li/>',
							{
								"class":	"user",
								"data-id":	users[u].id,
								"html":		$('<a/>',
									{
										"href":		"#",
										"html":		users[u].username
									}),
							}
						));
				}
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
			
		</ul>
		<div class="col-sm-10 chat-area pull-right">
			<div class="main-content-holder">
				<div class="main-content">
				</div>
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