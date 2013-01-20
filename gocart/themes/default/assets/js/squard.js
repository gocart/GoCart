//Squar'd is a little script written for GoCart to help get the product images squar'd up.

$.fn.squard = function(dim, container){

	//dim is the square dimensions you want to match
	img	= $(this);

	var newImg=document.createElement("img");

	// Added ability to pull through alt text for thumbnails
	var newAlt=document.createElement("h3");
	var newAltText=document.createTextNode(img.attr('data-alt'));
	newAlt.appendChild(newAltText);

	// Added ability to pull through caption text for thumbnails
	var newCap=document.createElement("p");
	var newCapText=document.createTextNode(img.attr('data-caption'));
	newCap.appendChild(newCapText);

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
	container.append(newAlt);
	container.append(newCap);

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