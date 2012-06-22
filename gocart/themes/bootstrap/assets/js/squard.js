//Squar'd is a little script written for GoCart to help get the product images squar'd up.

$.fn.squard = function(dim, container){
	
	//dim is the square dimensions you want to match
	img	= $(this);
	
	var newImg=document.createElement("img");
	
	newImg.setAttribute('src', img.attr('src'));
	
	if(img.innerHeight() == img.innerWidth())
	{
		newImg.style.width	= dim+'px';
		newImg.style.height	= dim+'px';
	}
	else if(img.innerHeight() > img.innerWidth())
	{
		newImg.style.height	= dim+'px';
	}
	else
	{
		newImg.style.width	= dim+'px';
		
		//find top margin
		//newImg.style.marginTop = (dim - newImg.height)/2+'px';
		
	}
	
	newImg.setAttribute('src', img.attr('src'));
	
	if(img.innerHeight() == img.innerWidth())
	{
		newImg.style.width	= dim+'px';
		newImg.style.height	= dim+'px';
	}
	else if(img.innerHeight() > img.innerWidth())
	{
		newImg.style.height	= dim+'px';
	}
	else
	{
		newImg.style.width	= dim+'px';
		
		//find top margin
		//newImg.style.marginTop = (dim - newImg.height)/2+'px';	
	}
	
	container.html(newImg);
	
	newImg.onload = function()
	{
		img2	= container.children().eq(0);
		if(img2.innerHeight() < dim)
		{
			margin = (dim-img2.innerHeight())/2;

			img2.css('margin-top', margin+'px');
		}
	}

};