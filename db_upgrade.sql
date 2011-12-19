
-- --------------------------------------------------------

-- GoCart :: Upgrade Database from 1.1.x to 1.2 

--    Don't forget to add your prefix

-- --------------------------------------------------------

ALTER TABLE `products` ADD `taxable` TINYINT( 1 ) NOT NULL DEFAULT 1 AFTER `shippable`,
						   `fixed_quantity` TINYINT( 1 ) NOT NULL DEFAULT 0 AFTER `taxable`,
						   `enabled` TINYINT( 1 ) NOT NULL;
						   
						   

--
-- Table structure for table `digital_products`
--

CREATE TABLE `digital_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `max_downloads` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `size` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `download_packages`
--

CREATE TABLE `download_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(60) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `code` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `download_package_files`
--

CREATE TABLE `download_package_files` (
  `package_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `downloads` int(11) NOT NULL,
  `link` varchar(32) NOT NULL,
  KEY `package_id` (`package_id`),
  KEY `package_id_2` (`package_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `products_files`
--

CREATE TABLE `products_files` (
  `product_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
						   
						   
						   
-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
