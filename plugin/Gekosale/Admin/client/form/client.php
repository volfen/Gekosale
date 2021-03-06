<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2013 WellCommerce sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;

class ClientForm extends Component\Form
{
	protected $populateData;

	public function setPopulateData ($Data)
	{
		$this->populateData = $Data;
	}

	public function initForm ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'client',
			'action' => '',
			'method' => 'post'
		));
		
		$personalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'personal_data',
			'label' => _('TXT_PERSONAL_DATA')
		)));
		
		$personalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'viewid',
			'label' => _('TXT_SHOP'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_SHOP'))
			),
			'options' => FormEngine\Option::Make(App::getModel('view')->getViewAllSelect()),
			'default' => Helper::getViewId()
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => _('TXT_FIRSTNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'surname',
			'label' => _('TXT_SURNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_SURNAME'))
			)
		)));
		
		if ($this->getParam() > 0){
			$personalData->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Uwaga zmieniając adres Email zmieni sie również login do sklepu</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));
		}
		else{
			$personalData->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Dla klienta zostanie wygenerowane hasło które umożliwi mu dostęp do panelu sklepu. Hasło zostanie wysłane na podany adres e-mail.</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));
		}
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'email',
			'label' => _('TXT_EMAIL'),
			'comment' => _('TXT_EMAIL_FORM'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_EMAIL')),
				new FormEngine\Rules\Email(_('ERR_WRONG_EMAIL')),
				new FormEngine\Rules\Unique(_('ERR_EMAIL_ALREADY_EXISTS'), 'clientdata', 'email', null, Array(
					'column' => 'email',
					'values' => (int) $this->registry->core->getParam()
				))
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'newsletter',
			'label' => _('TXT_NEWSLETTER'),
			'default' => '1'
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'phone',
			'label' => _('TXT_PHONE'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_PHONE')),
				new FormEngine\Rules\Format(_('ERR_WRONG_FORMAT'), '/^[0-9 -+]+$/')
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'phone2',
			'label' => _('TXT_ADDITIONAL_PHONE'),
			'rules' => Array(
				new FormEngine\Rules\Format(_('ERR_WRONG_FORMAT'), '/^[0-9 -+]+$/')
			)
		)));

		$personalData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'clientgroupid',
			'label' => _('TXT_GROUPS'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_GROUPS'))
			),
			'addable' => true,
			'onAdd' => 'xajax_AddClientGroup',
			'add_item_prompt' => 'Podaj nazwę grupy',
			'options' => FormEngine\Option::Make(App::getModel('clientgroup/clientgroup')->getClientGroupAllToSelect())
		)));
		
		$personalData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Klient będzie awansowany/degradowany zgodnie z ustawieniami automatycznego awansu w <a href="' . $this->registry->router->generate('admin', true, Array(
				'controller' => 'view'
			)) . '" target="_blank">Konfiguracja &raquo; Sklepy</a></p>',
		)));
		
		$personalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'autoassign',
			'label' => _('TXT_AUTO_ASSIGN'),
			'default' => '1'
		)));
		
		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => _('TXT_ADDITIONAL_DATA')
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'description',
			'label' => _('TXT_DESCRIPTION')
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>Blokada konta uniemożliwia zalogowanie klienta w sklepie oraz ponowną rejestrację za pomocą tego samego adresu e-mail.</p>',
			'direction' => FormEngine\Elements\Tip::DOWN
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'disable',
			'label' => _('TXT_DISABLE_CLIENT')
		)));
		
		$billingData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'billing_data',
			'label' => _('TXT_EDIT_ORDER_BILLING_DATA')
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => _('TXT_FIRSTNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'surname',
			'label' => _('TXT_SURNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'street',
			'label' => _('TXT_STREET'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_STREET'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'streetno',
			'label' => _('TXT_STREETNO'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_STREETNO'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placeno',
			'label' => _('TXT_PLACENO')
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placename',
			'label' => _('TXT_PLACE'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_PLACE'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'postcode',
			'label' => _('TXT_POSTCODE'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_POSTCODE')),
				new FormEngine\Rules\Format(_('ERR_WRONG_FORMAT'), "/[0-9]{2}\-[0-9]{3}/")
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'countryid',
			'label' => _('TXT_NAME_OF_COUNTRY'),
			'options' => FormEngine\Option::Make(App::getModel('countrieslist')->getCountryForSelect()),
			'default' => $this->registry->loader->getParam('countryid'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_NAME_OF_COUNTRY'))
			)
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'companyname',
			'label' => _('TXT_COMPANYNAME')
		)));
		
		$billingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'nip',
			'label' => _('TXT_NIP'),
			'rules' => Array(
				new FormEngine\Rules\Custom(_('ERR_WRONG_NIP'), Array(
					App::getModel('vat'),
					'checkVAT'
				))
			)
		)));
		
		$shippingData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'shipping_data',
			'label' => _('TXT_EDIT_ORDER_SHIPPING_DATA')
		)));
		
		$copy = $shippingData->AddChild(new FormEngine\Elements\StaticText(Array(
			'text' => '<a href="#" id="copy">' . _('TXT_JS_ADDRESS_COPY_FROM') . '</a>'
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => _('TXT_FIRSTNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'surname',
			'label' => _('TXT_SURNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'street',
			'label' => _('TXT_STREET'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_STREET'))
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'streetno',
			'label' => _('TXT_STREETNO'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_STREETNO'))
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placeno',
			'label' => _('TXT_PLACENO')
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'placename',
			'label' => _('TXT_PLACE'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_PLACE'))
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'postcode',
			'label' => _('TXT_POSTCODE'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_POSTCODE')),
				new FormEngine\Rules\Format(_('ERR_WRONG_FORMAT'), "/[0-9]{2}\-[0-9]{3}/")
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'countryid',
			'label' => _('TXT_NAME_OF_COUNTRY'),
			'options' => FormEngine\Option::Make(App::getModel('countrieslist')->getCountryForSelect()),
			'default' => $this->registry->loader->getParam('countryid'),
			'rules' => Array(
				new FormEngine\Rules\Required(_('ERR_EMPTY_NAME_OF_COUNTRY'))
			)
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'companyname',
			'label' => _('TXT_COMPANYNAME')
		)));
		
		$shippingData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'nip',
			'label' => _('TXT_NIP'),
			'rules' => Array(
				new FormEngine\Rules\Custom(_('ERR_WRONG_NIP'), Array(
					App::getModel('vat'),
					'checkVAT'
				))
			)
		)));
		
		if ((int) $this->registry->core->getParam() > 0){
			$clientsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'orders',
				'label' => _('TXT_CLIENT_ORDERS')
			)));
			
			$history = App::getModel('order')->getclientOrderHistory((int) $this->registry->core->getParam());
			
			if (count($history) > 0){
				$html = '<div class="view-order"><ul class="changes-detailed">';
				foreach ($history as $order){
					$url = App::getURLAdressWithAdminPane() . 'order/edit/' . $order['idorder'];
					$html .= "<li>";
					$html .= "<h4><span>{$order['adddate']}</span></h4>";
					$html .= "<p>Nr. zamówienia:  <strong><a href=\"{$url}\" target=\"_blank\">#{$order['idorder']}</a></strong></p>";
					$html .= "<p class=\"author\">" . _('TXT_ALL_ORDERS_PRICE') . ": <strong>{$order['globalprice']}</strong></p>";
					$html .= "<p class=\"author\">" . _('TXT_ORDER_STATUS') . ": <strong>{$order['status']}</strong></p>";
					$html .= "</li>";
				}
				$html .= '</div>';
			}
			else{
				$html = '<p>' . _('TXT_ORDER_HISTORY_EMPTY') . '</p>';
			}
			
			$clientsData->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => $html
			)));
		}
		
		$Data = Event::dispatch($this, 'admin.client.initForm', Array(
			'form' => $form,
			'id' => (int) $this->registry->core->getParam(),
			'data' => $this->populateData
		));
		
		if (! empty($Data)){
			$form->Populate($Data);
		}
		
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		return $form;
	}
}