$(function()
{
	window.brewing = false;

	var elem = {
		main: '.js-main',
		bottom: '.js-bottom',
		top: '.js-top',
		brewNow: '.js-brew-now',
		timer: '.js-timer',
		timeSince: '.js-time-since'
	};

	initPage();

	function initPage()
	{
		$.ajax({
			url:'/latest'
		}).done(function(data)
		{
			displayLatestCoffee(data);
		}).fail(function()
		{
			$(elem.main).html('An Error Occurred');
		});

		$('body').on('click', elem.brewNow, function()
		{
			startBrew();
			return false;
		});

		var mainTick = window.setInterval(function()
		{
			if (! window.brewing) {
				$.ajax({
					url:'/latest'
				}).done(function(data)
				{
					updateTimeSince(data);
				}).fail(function()
				{
					$(elem.bottom).html('Connection Interruption');
				});
			}
		}, 10000);
	}

	function updateTimeSince(data)
	{
		if(data) {
			data = JSON.parse(data);
			var time = moment(moment.utc(data.created_at), "YYYY-MM-DD HH:mm:ss").fromNow();
			$(elem.timeSince).html(time);
		}
	}

	function displayLatestCoffee(data)
	{
		if(data) {
			clearDisplay();
			data = JSON.parse(data);
			var time = moment(moment.utc(data.created_at), "YYYY-MM-DD HH:mm:ss").fromNow();

			$(elem.main).hide().html(brewButton()).fadeIn();
			$(elem.bottom).html("<span class='last-brewed'>Last Brewed</span><span class='time-since js-time-since'>" + time + "</span>").fadeIn();
		}
	}

	function brewButton()
	{
		return "<a class='button js-brew-now' href='#'>Start Brew</a>";
	}

	function renderCoffee(data)
	{
		data = JSON.parse(data);
		return moment(moment.utc(data.created_at), "YYYY-MM-DD HH:mm:ss").fromNow();
	}

	function startBrew()
	{
		clearDisplay();

		window.newBrewData = null;
		window.brewing = true;

		$.ajax({
			url: '/new'
		}).done(function(data)
		{
			window.newBrewData = data;
		}).fail(function()
		{
			clearDisplay();
			$(elem.main).html('An Error Occurred').fadeIn();
			return;
		});

		var totalMinutes = 10;
		var totalSeconds = totalMinutes * 60;

		var timerText = getTimerText(totalSeconds);

		$(elem.top).hide().html('<div class="brewing">BREWING</div>').fadeIn();
		$(elem.timer).hide().html(timerText).fadeIn();

		var interval = window.setInterval(function()
		{
			totalSeconds = totalSeconds - 1;

			if (parseInt(totalSeconds) < 0) {
				window.clearInterval(interval);
				displayLatestCoffee(window.newBrewData);
				window.brewing = false;
				return;
			}

			var timerText = getTimerText(totalSeconds);
			$(elem.timer).html(timerText);
		}, 1000);
	}

	function getTimerText(totalSeconds)
	{
		var minutes = Math.floor(totalSeconds / 60);
		var seconds = totalSeconds % 60;

		seconds = (seconds < 10) ? "0" + seconds : "" + seconds;
		return minutes + ":" + seconds;
	}

	function clearDisplay()
	{
		$(elem.main).fadeOut();
		$(elem.top).fadeOut();
		$(elem.bottom).fadeOut();
		$(elem.timer).fadeOut();
	}
});