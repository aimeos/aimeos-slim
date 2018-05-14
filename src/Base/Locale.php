<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package Slim
 * @subpackage Base
 */

namespace Aimeos\Slim\Base;

use Interop\Container\ContainerInterface;


/**
 * Service providing the locale objects
 *
 * @package Slim
 * @subpackage Base
 */
class Locale
{
	private $container;
	private $locale;


	/**
	 * Initializes the object
	 *
	 * @param ContainerInterface $container Dependency container
	 */
	public function __construct( ContainerInterface $container )
	{
		$this->container = $container;
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $attributes Associative list of URL parameter
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item object
	 */
	public function get( \Aimeos\MShop\Context\Item\Iface $context, array $attributes )
	{
		if( $this->locale === null )
		{
			$disableSites = $this->container->get( 'aimeos_config' )->get( 'disableSites', true );

			$site = ( isset( $attributes['site'] ) ? $attributes['site'] : 'default' );
			$lang = ( isset( $attributes['locale'] ) ? $attributes['locale'] : '' );
			$currency = ( isset( $attributes['currency'] ) ? $attributes['currency'] : '' );

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $disableSites );
		}

		return $this->locale;
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param string $sitecode Unique site code
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item object
	 */
	public function getBackend( \Aimeos\MShop\Context\Item\Iface $context, $sitecode )
	{
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );

		try
		{
			$localeItem = $localeManager->bootstrap( $sitecode, '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );
		}
		catch( \Aimeos\MShop\Locale\Exception $e )
		{
			$item = \Aimeos\MShop\Factory::createManager( $context, 'locale/site' )->findItem( $sitecode );
			$localeItem = $localeManager->createItem();
			$localeItem->setSiteId( $item->getId() );
		}

		return $localeItem;
	}
}
