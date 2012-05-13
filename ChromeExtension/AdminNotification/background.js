// Copyright (c) 2011 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.

/*
  Displays a notification with the current time. Requires "notifications"
  permission in the manifest file (or calling
  "webkitNotifications.requestPermission" beforehand).
*/
  var req = new XMLHttpRequest();
  
function show() {
	req.open(
		"GET",
		"https://localhost/GoCart/index.php/admin/activity/notify/"+localStorage.lastReadID ,
    true);
	req.onload = showFeedNotification;

	req.send(null);
}

function   showFeedNotification()
{
	var time = /(..)(:..)/.exec(new Date());     // The prettyprinted time.
	var hour = time[1] % 12 || 12;               // The prettyprinted hour.
	var period = time[1] < 12 ? 'a.m.' : 'p.m.'; // The period of the day.
	var feeds = JSON.parse(req.responseText);
	var notification = new Array();
	for (var i= 0 ; i < feeds.length; i++)
	{
	
		if(1 == feeds[i].type)
		{
			var dimage = 'NewOrder.png';
		}
		else if (2 == feeds[i].type)
		{
			var dimage = 'NewCustomer.png';			
		}
		
		localStorage.lastReadID = feeds[0].id;
		notification[i] = window.webkitNotifications.createNotification(
		dimage,                      // The image.
		hour + time[2] + ' ' + period, // The title.
		feeds[i].activity      // The body.
		);
		notification[i].show();
		
	}
		
}


// Conditionally initialize the options.
if (!localStorage.isInitialized) {
  localStorage.isActivated = true;   // The display activation.
  localStorage.frequency = 1;        // The display frequency, in minutes.
  localStorage.isInitialized = true; // The option initialization.
  localStorage.lastReadID = 0;
}

// Test for notification support.
if (window.webkitNotifications) {
  // While activated, show notifications at the display frequency.
  if (JSON.parse(localStorage.isActivated)) { show(); }

  var interval = 0; // The display interval, in minutes.

  setInterval(function() {
    interval++;

    if (
      JSON.parse(localStorage.isActivated) &&
        localStorage.frequency <= interval
    ) {
      show();
      interval = 0;
    }
  }, 60000);
}
