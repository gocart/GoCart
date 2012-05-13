// Copyright (c) 2012 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.

var req = new XMLHttpRequest();

req.open(
    "GET",
    "https://localhost/GoCart/index.php/admin/activity/feed",
    true);
req.onload = showFeeds;

req.send(null);

function showFeeds() {
	var feeds = JSON.parse(req.responseText);
	var content = "";
  	for(var i = 0; i < feeds.length; i++)
  	{
		if(i%2)
		{
			content += "<div class='odd'>";			
		}
		else
		{
			content += "<div class='even'>";			
		}
		content += feeds[i].activity;
		content += "<br/><br/>";
		content += "</div>";
  	}
	document.body.innerHTML = content;
  
}
