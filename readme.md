#GoCart 2.3

License

##User Guide Appendix

- [Overview](#overview)
- [Installation](#installation)
- [Configuration](#configuration)
	- [Localization](#localization)
	- [Site Settings](#sitesettings)
		- [Shipping Libraries](#shippinglibraries)
		- [Payment Gateways](#paymentgateways)
		- [Canned Messages](#cannedmessages)
		- [Administrators](#administrators)
	- [Content Management](#contentmanagement)
		- [Categories](#categories)
		- [Filters](#filters)
		- [Products](#products)
			- [Details](#details)
			- [Digital Content](#digitalconent)
			- [Categories](#productcategories)
			- [Filters](#filters)
			- [Options](#options)
			- [Related Items](#relateditems)
			- [Images](#images)
		- [Static Pages](#staticpages)
		- [Banners](#banners)
		- [Coupons](#coupons)
		- [Gift Cards](#giftcards)
	- [Customer Management](#customermanagement)
		- [Listing / Exporting](#listingexporting)
		- [Groups](#groups)
	- [Order Management](#ordermanagement)
		- [Receiving](#receiving)
		- [Reports](#reports)
- [Customization](#customization)
	- [Themes](#themes)
- [Getting Support](#gettingsupport)

# <a name="overview"></a> Overview

GoCart is a simple, lightweight shopping cart system built on the CodeIgniter framework. The goal of GoCart is not to add an enormous number of features, but to build an easy to use, easy to customize shopping cart. We have packed in a nice feature set and plan on refining and adding more. 

# <a name="installation"></a> Installation

Start by cloning our repo from github, or download the zip package from our [website](http://www.gocartdv.com/download) and unpack your files in your public web folder. You may install to your web root or any subfolder, such as /shop.

You must create your own database prior to installing the system. GoCart will not create a database for you.

When you first navigate to the web location of your cart, you will see the installer form. If you see any warning notices, fix the stated problems before continuing. The installer will need write access to some of the folders.

Fill in your database connection information.

If you wish to use URL shortening, click the box that says you wish to remove index.php from the site links. The installer will place an '.htaccess' file in your folder to enable rewriting.

Click continue, your database will be populated and you should be taken to the admin login screen.

For advanced configuration options, take a look under gocart/config/. We don't recommend making any changes in here unless you know what you're doing.

If you have trouble during this process, check into getting [support](#gettingsupport). 

# <a name="configuration"></a> Configuration

When you log in to the admin panel, you will see an empty dashboard. It's time to begin setting up the functional aspects of your store.

## <a name="localization"></a> Localization

Menu > 
You are free to choose what locations you will support. 

## <a name="sitesettings"></a> Site Settings

Administrative > Settings

Here you will find where to configure your payment gateways, shipping options, and message templates.

### <a name="shippinglibraries"></a> Shipping Libraries

At the top of this section you will see the list of available shipping rate packages to choose from.

We've included integrations with real-time rate request services for Fedex, UPS, and USPS local and international. You will need to sign up for an account with the respective shipper of your choice to use these services.

In addition, we've included two static shipping rate tabulators. Flatrate allows you to set a fixed shipping charge for all orders.

Table Rate gives you more flexibility to set your shipping charges based on weight or price scales, and by country. You'll see an example of a rate table under the settings form when you first load it.

The options on the left are table name (to give your price scale a title). The method allows you to set your rates based on a price or by the order weight. Country is a multiselect field that allows you to restrict your rate table for users in different locations. Select all for global availability.

Your rates are stacked as from (value) to (shipping cost). In the example, the first rate is from $0 to $4 order price will result in a $5 shipping charge. From $5 to $9 order price, the shipping charge will be $15. If you were to change the table method to 'weight,' then the shipping would be rated from 0 to 4 pounds, 5 to 9, etc.

Don't forget to change 'disabled' to 'enabled' in the settings to activate your shipping package!

### <a name="paymentgateways"></a> Payment Gateways

Below shipping, you will see a list of available payment gateways. You can choose to use more than one. Start by clicking 'install' next to the ones of your choice. This will initialize the settings for the package. Next, click on 'configure' to enter your api credentials or other pertinent information. The options will vary from package to package.

Don't see the payment gateway you want to use? Have a look at our available [commerical packages](http://gocartdv.com/payment-methods).

Don't forget to change "disabled" to "enabled" to activate the package when you've entered your configuration fields.

## <a name="cannedmessages"></a> Canned Messages

Canned messages are basic email templates for your email notifications. Change these to edit the text and layout of the emails the site sends out to customers.

## <a name="administrators"></a> Administrators

Here you can add administrator accounts. There are two types, 'admin' and 'order.'

Admins can edit anything on the site. Order admins can only see options related to filling orders and cannot change any important site functions.

## <a name="contentmanagement"></a> Content Management


### <a name="categories"></a> Categories
### <a name="filters"></a> Filters
### <a name="products"></a> Products
#### <a name="details"></a> Details
#### <a name="digitalconent"></a> Digital Content
#### <a name="productcategories"></a> Categories
#### <a name="filters"></a> Filters
#### <a name="options"></a> Options
#### <a name="relateditems"></a> Related Items
#### <a name="images"></a> Images
### <a name="staticpages"></a> Static Pages
### <a name="banners"></a> Banners
### <a name="coupons"></a> Coupons
### <a name="giftcards"></a> Gift Cards
## <a name="customermanagement"></a> Customer Management
### <a name="listingexporting"></a> Listing /Exporting
### <a name="groups"></a> Groups
## <a name="ordermanagement"></a> Order Management
### <a name="receiving"></a> Receiving
### <a name="reports"></a> Reports
# <a name="customization"></a> Customization
## <a name="themes"></a> Themes
# <a name="gettingsupport"></a> Getting Support

#### Check with the community

Browse over to our [support community](http://gocartdv.com/community) to find answers to common issues our users come across.

#### Get Help From The Development Team

We are available to help you install, configure, extend, and customize your cart. Please [contact us](http://gocartdv.com/contact) for enterprise support options.